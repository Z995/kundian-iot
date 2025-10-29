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
class ModbusFrameAnalyzer
{
    /**
     * 判断是否为 Modbus RTU 响应帧（不进行 CRC 校验）
     *
     * @param string $frameHex 十六进制格式的数据帧
     * @return bool 是否为有效的 Modbus RTU 响应帧
     */
    public static function isModbusResponse(string $frameHex): bool
    {
        // 清理输入数据
        $hexStr = strtoupper(str_replace(' ', '', $frameHex));

        // 基本验证
        if (!self::isValidHexString($hexStr)) {
            return false;
        }

        // 转换为字节数组
        $bytes = self::hexToBytes($hexStr);

        // 检查最小长度
        if (count($bytes) < 5) {
            return false;
        }

        // 检查设备地址
        $deviceAddress = $bytes[0];
        if ($deviceAddress < 1 || $deviceAddress > 247) {
            return false;
        }

        // 检查功能码
        $functionCode = $bytes[1];
        if (!self::isValidFunctionCode($functionCode)) {
            return false;
        }

        // 根据功能码检查帧结构
        return self::validateFrameStructure($bytes, $functionCode);
    }

    /**
     * 验证十六进制字符串
     */
    private static function isValidHexString(string $hex): bool
    {
        return ctype_xdigit($hex) && strlen($hex) % 2 === 0;
    }

    /**
     * 将十六进制字符串转换为字节数组
     */
    private static function hexToBytes(string $hex): array
    {
        $bytes = [];
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $bytes[] = hexdec(substr($hex, $i, 2));
        }
        return $bytes;
    }

    /**
     * 验证功能码是否有效
     */
    private static function isValidFunctionCode(int $code): bool
    {
        // 标准功能码
        $validCodes = [
            // 读取操作
            0x01, // 读线圈状态
            0x02, // 读离散量输入
            0x03, // 读保持寄存器
            0x04, // 读输入寄存器

            // 写入操作
            0x05, // 写单个线圈
            0x06, // 写单个寄存器
            0x0F, // 写多个线圈
            0x10, // 写多个寄存器

            // 诊断功能
            0x08, // 诊断
            0x0B, // 获取通信事件计数器
            0x0C, // 获取通信事件记录
            0x11, // 报告从站ID
            0x14, // 读文件记录
            0x15, // 写文件记录
            0x17, // 读/写多个寄存器
            0x18, // 读FIFO队列
        ];

        // 错误响应功能码 (原始功能码 + 0x80)
        $errorCode = $code & 0x7F;
        $isError = ($code & 0x80) === 0x80;

        return in_array($isError ? $errorCode : $code, $validCodes);
    }

    /**
     * 根据功能码验证帧结构
     */
    private static function validateFrameStructure(array $bytes, int $functionCode): bool
    {
        $frameLength = count($bytes);

        // 检查错误响应帧
        $isError = ($functionCode & 0x80) === 0x80;
        if ($isError) {
            // 错误响应帧结构: [地址][功能码+0x80][异常码][CRC]
            return $frameLength === 5; // 5字节: 地址(1)+功能码(1)+异常码(1)+CRC(2)
        }

        // 根据功能码检查帧结构
        switch ($functionCode) {
            // 读保持寄存器 (03) 或 读输入寄存器 (04)
            case 0x03:
            case 0x04:
                // 结构: [iots_redis_queue_heartbeat_filter地址][功能码][字节数][寄存器值...][CRC]
                $byteCount = $bytes[2];
                $expectedLength = 3 + $byteCount + 2; // 3头 + 数据 + 2CRC
                return $frameLength === $expectedLength;
//                return  in_array($frameLength,[$expectedLength,6]);

            // 写单个寄存器 (06)
            case 0x06:
                // 结构: [地址][功能码][寄存器地址][寄存器值][CRC]
                return $frameLength === 8; // 8字节

            // 写多个寄存器 (10)
            case 0x10:
                // 结构: [地址][功能码][起始地址][寄存器数量][CRC]
                return $frameLength === 8; // 8字节

            // 读线圈状态 (01) 或 读离散量输入 (02)
            case 0x01:
            case 0x02:
                // 结构: [地址][功能码][字节数][线圈状态...][CRC]
                $byteCount = $bytes[2];
                $expectedLength = 3 + $byteCount + 2; // 3头 + 数据 + 2CRC
                return $frameLength === $expectedLength;

            // 写单个线圈 (05)
            case 0x05:
                // 结构: [地址][功能码][输出地址][输出值][CRC]
                return $frameLength === 8; // 8字节

            // 写多个线圈 (0F)
            case 0x0F:
                // 结构: [地址][功能码][起始地址][线圈数量][CRC]
                return $frameLength === 8; // 8字节

            // 报告从站ID (11)
            case 0x11:
                // 结构: [地址][功能码][字节数][从站ID][运行指示][附加数据...][CRC]
                $byteCount = $bytes[2];
                $expectedLength = 3 + $byteCount + 2; // 3头 + 数据 + 2CRC
                return $frameLength === $expectedLength && $byteCount >= 2;

            default:
                // 对于其他功能码，只做基本长度检查
                return $frameLength >= 5 && $frameLength <= 256;
        }
    }

}
