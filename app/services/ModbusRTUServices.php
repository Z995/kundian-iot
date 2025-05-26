<?php

namespace app\services;

use think\exception\ValidateException;

class ModbusRTUServices
{
    /*
        // 示例1：构建读取指令
        $readCmd = ModbusRTU::buildReadHoldingRegisters(
            $slaveId = 1,      // 设备地址 7
            $startAddr = 0,    // 起始寄存器地址 1
            $quantity = 2      // 读取寄存器数量 1
        );
        echo "READ CMD: " . bin2hex($readCmd) . "\n";

        // 示例2：解析响应
        $sampleResponse = hex2bin('010304001000209B');
        $parsedData = ModbusRTU::parseReadResponse($sampleResponse);
        print_r($parsedData);

        // 示例3：构建写入指令
        $writeCmd = ModbusRTU::buildWriteSingleRegister(
            $slaveId = 1,      // 设备地址
            $regAddr = 0,      // 寄存器地址
            $value = 255       // 写入值
        );
        echo "WRITE CMD: " . bin2hex($writeCmd) . "\n";
     */

    // 设备地址校验（1-247）
    private static function validateSlaveId($slaveId) {
        if ($slaveId < 1 || $slaveId > 247) {
            throw new ValidateException("Slave ID must be 1-247");
        }
    }

    /**
     * CRC16校验计算（Modbus标准）
     * @param string $data 原始数据
     * @return string 2字节校验码（小端序）
     */
    public static function crc16($data) {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]);
            for ($j = 0; $j < 8; $j++) {
                $lsb = $crc & 0x0001;
                $crc >>= 1;
                if ($lsb) $crc ^= 0xA001;
            }
        }
        return pack('v', $crc);
    }

    /**
     * 构建读取保持寄存器指令（功能码03）
     * @param int $slaveId 从机地址（1-247）
     * @param int $startAddr 起始地址（0-65535）
     * @param int $quantity 读取数量（1-125）
     * @return string 完整指令帧
     */
    public static function buildReadHoldingRegisters($slaveId, $startAddr, $quantity=1) {
        self::validateSlaveId($slaveId);
        $frame = chr($slaveId)
            . chr(0x03)
            . pack('n', $startAddr)
            . pack('n', $quantity);
        return $frame . self::crc16($frame);
    }

    /**
     * 解析读取响应数据
     * @param string $response 设备返回的原始数据
     * @return array 解析后的寄存器值数组
     */
    public static function parseReadResponse($response) {
        if (strlen($response) < 5) {
            throw new ValidateException("Invalid response length");
        }

        // CRC校验
        $crcReceived = substr($response, -2);
        $calculatedCrc = self::crc16(substr($response, 0, -2));
        if ($crcReceived !== $calculatedCrc) {
            throw new ValidateException("CRC check failed");
        }

        $byteCount = ord($response[2]);
        $data = [];
        for ($i = 0; $i < $byteCount; $i += 2) {
            $data[] = unpack('n', substr($response, 3 + $i, 2))[1];
        }
        return $data;
    }

    /**
     * 构建写入单寄存器指令（功能码06）
     * @param int $slaveId 从机地址（1-247）
     * @param int $regAddr 寄存器地址（0-65535）
     * @param int $value 写入值（0-65535）
     * @return string 完整指令帧
     */
    public static function buildWriteSingleRegister($slaveId, $regAddr, $value) {
        self::validateSlaveId($slaveId);
        $frame = chr($slaveId)
            . chr(0x06)
            . pack('n', $regAddr)
            . pack('n', $value);
        return $frame . self::crc16($frame);
    }
}