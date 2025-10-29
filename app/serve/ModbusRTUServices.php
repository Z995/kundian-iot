<?php

/**
 * 坤典智慧农场V6
 * @link https://www.cqkd.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing KunDian Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing KunDian Technology Co., Ltd.
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-www.cqkd.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 */

namespace app\serve;

use app\exception\CrcException;
use think\exception\ValidateException;

class ModbusRTUServices
{
    // 字节序常量
    const BYTE_ORDER_ABCD = 'ABCD';  // 大端序
    const BYTE_ORDER_DCBA = 'DCBA';  // 小端序
    const BYTE_ORDER_BADC = 'BADC';  // 字节交换
    const BYTE_ORDER_CDAB = 'CDAB';  // 字节交换+反转
    const READ_DISCRETE_INPUTS = 2;
    const WRITE_SINGLE_COIL = 5;
    const WRITE_SINGLE_REGISTER = 6;
    // 支持的数据类型及其字节长度
    static $supportedTypes = [
        'bit' => 1,
        'uint8' => 1, 'int8' => 1,
        'uint16' => 2, 'int16' => 2,
        'uint32' => 4, 'int32' => 4, 'float32' => 4,
        'uint64' => 8, 'int64' => 8, 'float64' => 8
    ];

    static $byteOrder;
    static $crcChecks = true;

    /**
     * 精确计算 Modbus RTU CRC16 校验码
     */
    private static function crc16($data)
    {
        $crc = 0xFFFF;
        $len = strlen($data);

        for ($i = 0; $i < $len; $i++) {
            $crc ^= ord($data[$i]);
            for ($j = 0; $j < 8; $j++) {
                $lsb = $crc & 0x0001;
                $crc >>= 1;
                if ($lsb) {
                    $crc ^= 0xA001;
                }
                $crc &= 0xFFFF;
            }
        }
        return pack('v', $crc);
    }

    /**
     * 验证参数范围
     */
    private static function validateRange($value, $min, $max, $name)
    {
        if ((float)$value < $min || (float)$value > $max) {
            throw new ValidateException("$name 必须在 $min-$max 范围内");
        }
    }

    /**
     * 构建命令帧
     */
    private static function buildCommand($slaveAddress, $functionCode, $payload)
    {
        self::validateRange($slaveAddress, 1, 247, '从站地址');

        $frame = chr($slaveAddress) . chr($functionCode) . $payload;
        $crc = self::crc16($frame);
        $binaryData = $frame . $crc;

        return bin2hex($binaryData);

    }

    /**
     * 生成写单个线圈命令
     */
    public static function writeSingleCoil($slaveAddress, $coilAddress, $value)
    {
        self::validateRange($coilAddress, 0, 65535, '线圈地址');

        $packedValue = $value ? 0xFF00 : 0x0000;
        $payload = pack('n', $coilAddress) . pack('n', $packedValue);

        return self::buildCommand($slaveAddress, self::WRITE_SINGLE_COIL, $payload);
    }

    /**
     * 生成写单个寄存器命令（精确值处理）
     */
    public static function writeSingleRegister($slaveAddress, $registerAddress, $decimalValue)
    {
        self::validateRange($registerAddress, 0, 65535, '寄存器地址');
        self::validateRange($decimalValue, 0, 65535, '寄存器值');

        // 将输入值解释为十六进制表示
        $hexValue = hexdec($decimalValue);

        // 构建载荷
        $payload = pack('n', $registerAddress) . pack('n', $hexValue);

        return self::buildCommand($slaveAddress, self::WRITE_SINGLE_REGISTER, $payload);
    }


    /**
     * 解析Modbus RTU帧
     * @param string $hexFrame 十六进制字符串（如 "010304BEEE416E2C52"）
     * @param string $dataType 数据类型（如 'float32'）
     * @param array $options 附加选项（字节序/位位置等）
     * @return array 解析结果
     */
    public static function parseFrame($hexFrame, $dataType, $byteOrder = self::BYTE_ORDER_ABCD, $options = [])
    {


        if ($dataType == "bit" && empty($options)) {
            $options = ["bitPosition" => 10];
        }

        self::$byteOrder = $byteOrder;

        $result = [
            "success" => false,
            "data" => null,
            "is_log" => true,
        ];
        $bytes = self::hexStringToByteArray($hexFrame);
        $function_code = $bytes[1];
        if (in_array($function_code, [self::WRITE_SINGLE_COIL, self::WRITE_SINGLE_REGISTER])) {//写入命令
            return ["success" => true, "is_log" => false];
        }
        $cleanFrame = self::verificationModbusRTUS($hexFrame);

        // CRC校验 [2](@ref)
        if (self::$crcChecks) {
            $crcReceived = substr($cleanFrame, -4);
            $dataPart = substr($cleanFrame, 0, -4);
            $crcCalculated = self::calculateCRC($dataPart);
            if (strtoupper($crcCalculated) !== strtoupper($crcReceived)) {
                throw new CrcException("CRC verification failed hex:" . $hexFrame);
            }
        }
        // 提取数据部分（跳过地址和功能码）
        $dataPart = substr($cleanFrame, 6, strlen($cleanFrame) - 10);
        $value = self::parseData($dataPart, $dataType, $options);
        $result["data"] = ["value" => $value, "function_code" => $bytes[1], "address" => $bytes[0]];
        $result["success"] = true;
        return $result;
    }

    /**
     * 验证是不是ModbusRTUS格式
     * @param $hexFrame
     * @return array|string|string[]
     */
    public static function verificationModbusRTUS($hexFrame)
    {
        // 清洗帧并转换为二进制
        $cleanFrame = str_replace([' ', "\n"], '', $hexFrame);
        if (!preg_match('/^[0-9A-Fa-f]+$/', $cleanFrame)) {
            throw new ValidateException("Invalid hex characters");
        }

        // 基础校验
        if (strlen($cleanFrame) < 8) {
            throw new ValidateException("Frame too short (min 8 chars)");
        }

        $isModbus=ModbusFrameAnalyzer::isModbusResponse($cleanFrame);
        if (!$isModbus){
            throw new ValidateException("非ModbusRTUS格式设备返回值：".$hexFrame);
        }
        return $cleanFrame;
    }


    /**
     * 计算Modbus CRC16校验（小端序）[2](@ref)
     * @param string $hexData 十六进制数据
     * @return string 十六进制CRC值（4字符）
     */
    private static function calculateCRC($hexData)
    {
        $data = hex2bin($hexData);
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]);
            for ($j = 0; $j < 8; $j++) {
                $lsb = $crc & 0x0001;
                $crc = $crc >> 1;
                if ($lsb) $crc ^= 0xA001;
            }
        }

        return str_pad(dechex(($crc & 0xFF00) >> 8 | ($crc & 0xFF) << 8), 4, '0', STR_PAD_LEFT);
    }

    /**
     * 解析数据部分
     * @param string $hexData 十六进制数据
     * @param string $dataType 数据类型
     * @param array $options 解析选项
     * @return mixed 解析后的值
     */
    private static function parseData($hexData, $dataType, $options)
    {
        // 验证数据类型
        if (!isset(self::$supportedTypes[$dataType])) {
            throw new ValidateException("Unsupported data type: $dataType");
        }

        $byteOrder = $options['byte_order'] ?? self::$byteOrder;
        $bitPosition = $options['bit_position'] ?? 0;
        $byteIndex = $options['byte_index'] ?? 0;

        $bytes = [];
        $hexData = substr($hexData, $byteIndex * 2);

        // 将十六进制转换为字节数组
        for ($i = 0; $i < strlen($hexData); $i += 2) {
            $bytes[] = hexdec(substr($hexData, $i, 2));
        }

        // 处理位类型
        if ($dataType === 'bit') {
            return (int)$hexData;
//            return ($bytes[0] >> $bitPosition) & 1;
        }

        // 应用字节序
        $orderedBytes = self::applyByteOrder($bytes, $dataType, $byteOrder);
        $binary = call_user_func_array('pack', array_merge(['C*'], $orderedBytes));

        // 转换为目标数据类型
        return self::decodeValue($binary, $dataType);
    }

    /**
     * 应用字节序重排
     * @param array $bytes 原始字节数组
     * @param string $dataType 数据类型
     * @param string $byteOrder 字节序
     * @return array 重排后的字节数组
     */
    private static function applyByteOrder(array $bytes, $dataType, $byteOrder)
    {
        $length = self::$supportedTypes[$dataType];
        if ($length === 1) return $bytes; // 单字节不处理

        $ordered = $bytes;
        switch ($byteOrder) {
            case self::BYTE_ORDER_DCBA: // 完全反转 (DCBA)
                $ordered = array_reverse($bytes);
                break;

            case self::BYTE_ORDER_BADC: // 每2字节交换 (BADC)
                for ($i = 0; $i < $length; $i += 2) {
                    if ($i + 1 < $length) {
                        [$ordered[$i], $ordered[$i + 1]] = [$bytes[$i + 1], $bytes[$i]];
                    }
                }
                break;

            case self::BYTE_ORDER_CDAB: // 交换后反转 (CDAB)
                for ($i = 0; $i < $length; $i += 2) {
                    if ($i + 1 < $length) {
                        [$ordered[$i], $ordered[$i + 1]] = [$bytes[$i + 1], $bytes[$i]];
                    }
                }
                $ordered = array_reverse($ordered);
                break;
        }
        return $ordered;
    }

    /**
     * 将二进制数据解码为指定类型
     * @param string $binary 二进制数据
     * @param string $dataType 数据类型
     * @return mixed 解码后的值
     */
    private static function decodeValue($binary, $dataType)
    {
        $formatMap = [
            'uint8' => ['C', 1], 'int8' => ['c', 1],
            'uint16' => ['n', 2], 'int16' => ['s', 2],
            'uint32' => ['N', 4], 'int32' => ['l', 4],
            'float32' => ['G', 4], 'float64' => ['E', 8]
        ];

        if (isset($formatMap[$dataType])) {
            [$format, $length] = $formatMap[$dataType];
            return unpack($format, $binary)[1];
        }

        // 特殊处理64位整数
        if ($dataType === 'uint64') {
            $parts = unpack('N2', $binary);
            return ($parts[1] << 32) | $parts[2];
        }

        if ($dataType === 'int64') {
            $parts = unpack('N2', $binary);
            $value = ($parts[1] << 32) | $parts[2];
            if ($value > 0x7FFFFFFFFFFFFFFF) {
                $value -= 0x10000000000000000;
            }
            return $value;
        }

        throw new ValidateException("Unsupported decoding for $dataType");
    }

    /**
     * Undocumented function HEX转bin
     *
     * @param [type] $str HEX字符串
     * @return string bin字符串
     */
    public static function myHexTobin($str)
    {
        $res = '';
        //去除空格
        $str = str_replace([' ', PHP_EOL, '　'], '', $str);
        if (!$str) {
            return $res;
        }
        if (strlen($str) % 2 != 0) {
            return $str;
        }
        return hex2bin($str);
    }

    /**
     * 十六进制字符串转字节数组
     */
    private static function hexStringToByteArray(string $hex): array
    {
        $bytes = [];
        $length = strlen($hex);

        for ($i = 0; $i < $length; $i += 2) {
            $bytes[] = hexdec(substr($hex, $i, 2));
        }

        return $bytes;
    }

    /**
     * 寄存器：与组态软件的寄存器写法相同，填十进制寄存器地址，寄存器为起始地址+1.如：
     * 功能码03H或06H，起始地址0000H，则寄存器选则4，地址填1；
     * 功能码04H，起始地址000AH，则寄存器选则3，地址填11；
     * 功能码01H或05H，起始地址0002H，则寄存器选则0，地址填3；
     * 功能码02H，起始地址0003H，则寄存器选则1，地址填4。
     * 寄存器转换功能码
     * @param $register
     * @param $operate
     */
    public static function getFunctionCode($register, $startAddress, $operate = "read")
    {
        //register_mark
        if ($operate == "write" && !in_array($register, [0, 4])) {
            throw new ValidateException("该寄存器不支持写入");
        }
        switch ($register) {
            case 0:
                if ($operate == "read") {
                    $function_code = 1; //读取线圈
                } else {
                    $function_code = 5;//写入单个线圈
                }
                break;
            case 1:
                $function_code = 2;//读取离散输入
                break;
            case 3:
                $function_code = 4;//读取输入寄存器
                break;
            case 4:
                if ($operate == "read") {
                    $function_code = 3;//读取保持寄存器
                } else {
                    $function_code = 6;//写入寄存器
                }
                break;
            default:
                throw new ValidateException("寄存器错误");
        }
        $startAddress -= 1;//plc组态地址减1
        if ($startAddress < 0) {
            $startAddress = 0;
//            throw new ValidateException("寄存器地址错误");
        }
        return [$function_code, $startAddress];
    }

    public static function getModbusParameter($param)
    {
        /**
         * 1:16位无符号整数(ABCD),2:16位无符号整数(DCBA),3:16位无符号整数(BADC),4:16位无符号整数(CDAB)
         * 5:16位有符号整数(ABCD),6:16位有符号整数(DCBA),7:16位有符号整数(BADC),8:16位有符号整数(CDAB)
         *
         * 9:32位无符号整数(ABCD),10:32位无符号整数(DCBA),11:32位无符号整数(BADC),12:32位无符号整数(CDAB)
         * 13:32位有符号整数(ABCD),14:32位有符号整数(DCBA),15:32位有符号整数(BADC),16:32位有符号整数(CDAB)
         * 17:32位浮点数(ABCD),18:32位浮点数(DCBA),19:32位浮点数(BADC),20:32位浮点数(CDAB)
         *
         * 21:64位无符号整数(ABCD),22:64位无符号整数(DCBA),23:64位无符号整数(BADC),24:64位无符号整数(CDAB)
         * 25:64位有符号整数(ABCD),26:64位有符号整数(DCBA),27:64位有符号整数(BADC),28:64位有符号整数(CDAB)
         * 29:64位浮点数(ABCD),30:64位浮点数(DCBA),31:64位浮点数(BADC),32:64位浮点数(CDAB)
         * 33:位
         */
        switch ($param['data_format']) {
            //16位无符号整数
            case 1:
                $result = ["type" => "uint16", "byte_order" => self::BYTE_ORDER_ABCD, "quantity" => 1];
                break;
            case 2:
                $result = ["type" => "uint16", "byte_order" => self::BYTE_ORDER_DCBA, "quantity" => 1];
                break;
            case 3:
                $result = ["type" => "uint16", "byte_order" => self::BYTE_ORDER_BADC, "quantity" => 1];
                break;
            case 4:
                $result = ["type" => "uint16", "byte_order" => self::BYTE_ORDER_CDAB, "quantity" => 1];
                break;

            //16位有符号整数
            case 5:
                $result = ["type" => "int16", "byte_order" => self::BYTE_ORDER_ABCD, "quantity" => 1];
                break;
            case 6:
                $result = ["type" => "int16", "byte_order" => self::BYTE_ORDER_DCBA, "quantity" => 1];
                break;
            case 7:
                $result = ["type" => "int16", "byte_order" => self::BYTE_ORDER_BADC, "quantity" => 1];
                break;
            case 8:
                $result = ["type" => "int16", "byte_order" => self::BYTE_ORDER_CDAB, "quantity" => 1];
                break;

            //32位无符号整数
            case 9:
                $result = ["type" => "uint32", "byte_order" => self::BYTE_ORDER_ABCD, "quantity" => 2];
                break;
            case 10:
                $result = ["type" => "uint32", "byte_order" => self::BYTE_ORDER_DCBA, "quantity" => 2];
                break;
            case 11:
                $result = ["type" => "uint32", "byte_order" => self::BYTE_ORDER_BADC, "quantity" => 2];
                break;
            case 12:
                $result = ["type" => "uint32", "byte_order" => self::BYTE_ORDER_CDAB, "quantity" => 2];
                break;

            //32位有符号整数
            case 13:
                $result = ["type" => "int32", "byte_order" => self::BYTE_ORDER_ABCD, "quantity" => 2];
                break;
            case 14:
                $result = ["type" => "int32", "byte_order" => self::BYTE_ORDER_DCBA, "quantity" => 2];
                break;
            case 15:
                $result = ["type" => "int32", "byte_order" => self::BYTE_ORDER_BADC, "quantity" => 2];
                break;
            case 16:
                $result = ["type" => "int32", "byte_order" => self::BYTE_ORDER_CDAB, "quantity" => 2];
                break;
            //32位浮点数
            case 17:
                $result = ["type" => "float32", "byte_order" => self::BYTE_ORDER_ABCD, "quantity" => 2];
                break;
            case 18:
                $result = ["type" => "float32", "byte_order" => self::BYTE_ORDER_DCBA, "quantity" => 2];
                break;
            case 19:
                $result = ["type" => "float32", "byte_order" => self::BYTE_ORDER_BADC, "quantity" => 2];
                break;
            case 20:
                $result = ["type" => "float32", "byte_order" => self::BYTE_ORDER_CDAB, "quantity" => 2];
                break;

            //64位无符号整数
            case 21:
                $result = ["type" => "uint64", "byte_order" => self::BYTE_ORDER_ABCD, "quantity" => 4];
                break;
            case 22:
                $result = ["type" => "uint64", "byte_order" => self::BYTE_ORDER_DCBA, "quantity" => 4];
                break;
            case 23:
                $result = ["type" => "uint64", "byte_order" => self::BYTE_ORDER_BADC, "quantity" => 4];
                break;
            case 24:
                $result = ["type" => "uint64", "byte_order" => self::BYTE_ORDER_CDAB, "quantity" => 4];
                break;

            //64位有符号整数
            case 25:
                $result = ["type" => "int364", "byte_order" => self::BYTE_ORDER_ABCD, "quantity" => 4];
                break;
            case 26:
                $result = ["type" => "int64", "byte_order" => self::BYTE_ORDER_DCBA, "quantity" => 4];
                break;
            case 27:
                $result = ["type" => "int64", "byte_order" => self::BYTE_ORDER_BADC, "quantity" => 4];
                break;
            case 28:
                $result = ["type" => "int64", "byte_order" => self::BYTE_ORDER_CDAB, "quantity" => 4];
                break;
            //64位浮点数
            case 29:
                $result = ["type" => "float64", "byte_order" => self::BYTE_ORDER_ABCD, "quantity" => 4];
                break;
            case 30:
                $result = ["type" => "float64", "byte_order" => self::BYTE_ORDER_DCBA, "quantity" => 4];
                break;
            case 31:
                $result = ["type" => "float64", "byte_order" => self::BYTE_ORDER_BADC, "quantity" => 4];
                break;
            case 32:
                $result = ["type" => "float64", "byte_order" => self::BYTE_ORDER_CDAB, "quantity" => 4];
                break;
            case 33:
                $result = ["type" => "bit", "byte_order" => self::BYTE_ORDER_ABCD, "quantity" => 1];
                break;
            default:
                throw new ValidateException("数据类型错误");
        }
        return $result;
    }

    /**
     * 构建读取寄存器的 Modbus RTU 指令
     *
     * @param int $slaveId 从站地址 (1-247)
     * @param int $functionCode 功能码 (3:保持寄存器, 4:输入寄存器)
     * @param int $startAddress 起始寄存器地址 (0-65535)
     * @param string $quantity 寄存器数量 (十六进制字符串表示)
     * @return string 十六进制格式的指令字符串 (带空格分隔)
     */
    public static function buildReadRegistersCommand($slaveId, $functionCode, $startAddress, $quantity = 1)
    {
        // 参数验证
        if ($slaveId < 1 || $slaveId > 247) {
            throw new ValidateException("从站地址必须在 1-247 范围内");
        }

        if ($startAddress < 0 || $startAddress > 65535) {
            throw new ValidateException("起始地址必须在 0-65535 范围内");
        }

        // 特殊处理：将寄存器数量转换为十六进制整数
        $quantityValue = hexdec($quantity);
        $quantityBytes = hex2bin(sprintf('%04x', $quantityValue));

        // 构建指令数据部分
        $commandData = pack('C', $slaveId) .      // 从站地址
            pack('C', $functionCode) . // 功能码
            pack('n', $startAddress) . // 起始地址 (大端序)
            $quantityBytes;            // 寄存器数量

        // 计算CRC16校验码
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($commandData); $i++) {
            $crc ^= ord($commandData[$i]);
            for ($j = 0; $j < 8; $j++) {
                $lsb = $crc & 0x0001;
                $crc >>= 1;
                if ($lsb) {
                    $crc ^= 0xA001;
                }
            }
        }

        // 构建完整指令 (附加CRC小端序)
        $fullCommand = $commandData . pack('v', $crc);

        // 格式化为带空格的十六进制字符串
        $hexString = bin2hex($fullCommand);
        return $hexString;
    }


}