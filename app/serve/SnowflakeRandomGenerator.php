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

use RuntimeException;
use InvalidArgumentException;
use think\exception\ValidateException;


class SnowflakeRandomGenerator
{
    // 可配置的位数分配
    private $timestampBits;
    private $randomBits;
    private $sequenceBits;
    private $totalBits;

    // 计算常量
    private $maxTimestamp;
    private $maxRandom;
    private $maxSequence;
    private $timestampShift;
    private $randomShift;

    // 状态管理
    private $lastTimestamp = -1;
    private $sequence = 0;
    private $epoch;
    private $workerId;

    /**
     * 初始化生成器
     *
     * @param int $timestampBits 时间戳位数 (建议20-40位)
     * @param int $randomBits   随机数位数 (建议10-20位)
     * @param int $sequenceBits 序列号位数 (建议6-12位)
     * @param int $workerId     工作节点ID (0-1023)
     * @param int|null $epoch   自定义起始时间(毫秒)
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        int $timestampBits = 35,
        int $randomBits = 20,
        int $sequenceBits = 8,
        int $workerId = 0,
        int $epoch = null
    ) {
        // 验证位数配置
        $this->totalBits = $timestampBits + $randomBits + $sequenceBits;
        if ($this->totalBits > 63) {
            throw new ValidateException("总位数不能超过63位");
        }

        // 保存配置
        $this->timestampBits = $timestampBits;
        $this->randomBits = $randomBits;
        $this->sequenceBits = $sequenceBits;
        $this->workerId = $workerId;

        // 计算最大值
        $this->maxTimestamp = (1 << $timestampBits) - 1;
        $this->maxRandom = (1 << $randomBits) - 1;
        $this->maxSequence = (1 << $sequenceBits) - 1;

        // 计算位移量
        $this->randomShift = $sequenceBits;
        $this->timestampShift = $sequenceBits + $randomBits;

        // 设置起始时间
        $this->epoch = $epoch ?? $this->defaultEpoch();
    }

    /**
     * 生成指定位数的随机数
     *
     * @param int $digits 输出位数 (6-19位)
     * @return int 指定位数的随机整数
     *
     * @throws RuntimeException
     */
    public function generate(int $digits = 10): int
    {
        // 验证输出位数
        if ($digits < 6 || $digits > 19) {
            throw new ValidateException("输出位数必须在6-19之间");
        }

        // 获取当前时间戳
        $timestamp = $this->currentTimestamp();

        // 处理时钟回拨
        if ($timestamp < $this->lastTimestamp) {
            $this->handleClockDrift($timestamp);
            return $this->generate($digits);
        }

        // 序列管理
        if ($this->lastTimestamp === $timestamp) {
            $this->sequence = ($this->sequence + 1) & $this->maxSequence;
            if ($this->sequence === 0) {
                // 序列耗尽，等待下一毫秒
                $timestamp = $this->waitNextMillis($this->lastTimestamp);
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        // 计算时间偏移
        $timeOffset = $timestamp - $this->epoch;

        // 时间溢出处理
        if ($timeOffset > $this->maxTimestamp) {
            $this->rebaseEpoch($timestamp);
            $timeOffset = 0;
        }

        // 生成随机部分
        $randomPart = random_int(0, $this->maxRandom);

        // 组合Snowflake ID
        $snowflakeId = ($timeOffset << $this->timestampShift) |
            ($randomPart << $this->randomShift) |
            $this->sequence;

        // 转换为指定位数的随机数
        return $this->toFixedDigits($snowflakeId, $digits);
    }

    /**
     * 将ID转换为指定位数的随机数
     */
    private function toFixedDigits(int $id, int $digits): int
    {
        // 计算范围
        $min = pow(10, $digits - 1);
        $max = pow(10, $digits) - 1;

        // 使用模运算映射到指定范围
        $range = $max - $min + 1;
        $value = $id % $range + $min;

        // 添加扰动避免模式重复
        $perturbation = $this->calculatePerturbation($id, $digits);
        $value = ($value + $perturbation) % $range + $min;

        return (int)$value;
    }

    /**
     * 计算扰动值
     */
    private function calculatePerturbation(int $id, int $digits): int
    {
        // 使用ID的哈希值作为扰动源
        $hash = crc32((string)$id);

        // 根据位数确定扰动幅度
        $maxPerturbation = pow(10, max(1, $digits - 3));

        // 计算扰动值
        return $hash % (2 * $maxPerturbation) - $maxPerturbation;
    }

    /**
     * 获取当前毫秒时间戳
     */
    private function currentTimestamp(): int
    {
        return (int) round(microtime(true) * 1000);
    }

    /**
     * 获取默认起始时间
     */
    private function defaultEpoch(): int
    {
        return strtotime('2023-01-01') * 1000;
    }

    /**
     * 处理时钟回拨
     */
    private function handleClockDrift(int $timestamp): void
    {
        $driftAmount = $this->lastTimestamp - $timestamp;

        // 微小回拨安全忽略
        if ($driftAmount < 10) {
            $this->lastTimestamp = $timestamp;
            return;
        }

        // 中等回拨使用休眠
        if ($driftAmount <= 1000) {
            usleep($driftAmount * 1000);
            $this->lastTimestamp = $this->currentTimestamp();
        }
        // 严重回拨自动重置
        else {
            $this->rebaseEpoch($timestamp);
        }
    }

    /**
     * 等待下一毫秒
     */
    private function waitNextMillis(int $lastTimestamp): int
    {
        $timestamp = $this->currentTimestamp();
        while ($timestamp <= $lastTimestamp) {
            usleep(100); // 0.1ms休眠
            $timestamp = $this->currentTimestamp();
        }
        return $timestamp;
    }

    /**
     * 重新设置起始时间
     */
    private function rebaseEpoch(int $currentTimestamp): void
    {
        $this->epoch = $currentTimestamp - $this->maxTimestamp + 1000;
        $this->lastTimestamp = -1;
        $this->sequence = 0;
    }

    /**
     * 获取当前配置
     */
    public function getConfig(): array
    {
        return [
            'timestampBits' => $this->timestampBits,
            'randomBits' => $this->randomBits,
            'sequenceBits' => $this->sequenceBits,
            'workerId' => $this->workerId,
            'epoch' => $this->epoch,
            'maxTimestamp' => $this->maxTimestamp,
            'maxRandom' => $this->maxRandom,
            'maxSequence' => $this->maxSequence
        ];
    }
}