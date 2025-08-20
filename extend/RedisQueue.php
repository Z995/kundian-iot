<?php


namespace extend;

use support\Log;
use Webman\RedisQueue\Redis;

/**
 * RedisQueue redis队列
 */
class RedisQueue
{
    //通用的队列方法
    /**
     * @description: 通用的队列方法
     * @param {string} $queue 队列名
     * @param {array} $data 数据
     * @param {int} $delay 延迟时间
     * //调用方法
     * RedisQueue::queue('redis_queue_wage_passcard', ['name' => 'test'], 10);
     */
    public static function queue($queue, $data, $delay = 0)
    {
        if (!$queue || !$data) {
            return false;
        }
        // Redis::send($queue, $data, $delay);
        $redis = myRedis();
        //发送时间,非送达时间
        $queue_waiting = '{redis-queue}-waiting'; //1.0.5版本之前为redis-queue-waiting
        $queue_delay = '{redis-queue}-delayed'; //1.0.5版本之前为redis-queue-delayed
        $now = time();
        $package_str = json_encode([
            'id'       => rand(),
            'time'     => $now,
            'delay'    => 0,
            'attempts' => 0,
            'queue'    => $queue,
            'data'     => $data
        ]);
        if ($delay) {
            return $redis->zAdd($queue_delay, $now + $delay, $package_str);
        }
        return $redis->lPush($queue_waiting . $queue, $package_str);
    }
}
