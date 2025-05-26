<?php
/*
 * @Author: duyingjie duyingjie@qq.com
 * @Date: 2023-01-29 15:05:32
 * @LastEditors: duyingjie duyingjie@qq.com
 * @LastEditTime: 2023-06-02 08:39:54
 * @FilePath: \erpd:\phpstudy_pro\webman\app\queue\redis\MyMailSend.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\queue\redis\slow;

use app\model\DeviceLogs as ModelDeviceLogs;
use app\model\Iot;
use support\Log;
use think\facade\Db;
use Webman\RedisQueue\Consumer;

//设备日志队列
class DeviceLogs implements Consumer
{
    // 要消费的队列名
    public $queue = '';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';
    //日志文件
    public $logFile = 'queue-slow-DeviceLogs';

    public function __construct()
    {
        $this->queue = 'iots_redis_queue_device_logs';
    }
    public function consume($data)
    {
        //开启事务
        Db::startTrans();
        try {
            $iot = Iot::where([['code', '=', $data['from']], ['del', '=', 0]])->findOrEmpty()->toArray();
            if (!$iot) {
                Log::channel($this->logFile)->info('DeviceLogs消费捕获异常: ' . '设备不存在');
                return;
            }
            //组合日志数据
            $logs = [];
            $logs['id'] = genreId('device_logs');
            $logs['device_id'] = $iot['id'];
            $logs['type'] = $data['type'];
            $logs['vtype'] = $data['type'] == 2 ? 0 : $data['vtype'];
            $logs['topic'] = $data['type'] == 2 ? $data['topic'] : '';
            $logs['val'] = $data['msg'];
            $logs['year'] = date('Y', strtotime($data['time']));
            $logs['month'] = date('m', strtotime($data['time']));
            $logs['day'] = date('d', strtotime($data['time']));
            $logs['date'] = date('Y-m-d', strtotime($data['time']));
            $logs['addtime'] = $data['time'];
            $logs['del'] = 0;
            $logs['del_id'] = 0;
            $id = ModelDeviceLogs::create($logs)->id;
            if (!$id) {
                throw new \Exception('设备日志入库失败');
            }
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            Log::channel($this->logFile)->info('DeviceLogs消费捕获异常: ' . $e->getMessage(), [
                'package' => $data,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    /**
     * 消费失败回调
     * @param \Throwable $e
     * @param array $package 消息包
     * @return void
     */
    /* 
    $package = [
        'id' => 1357277951, // 消息ID
        'time' => 1709170510, // 消息时间
        'delay' => 0, // 延迟时间
        'attempts' => 2, // 消费次数
        'queue' => 'redis_queue_test_queue1', // 队列名
        'data' => ['content' => '2024-02-29 09:35:10'], // 消息内容
        'max_attempts' => 2, // 最大重试次数
        'error' => '错误信息' // 错误信息
    ]
    */
    public function onConsumeFailure(\Throwable $e, $package)
    {
        // 达到最大重试次数
        if ($package['attempts'] >= $package['max_attempts']) {
            Log::channel($this->logFile)->info('DeviceLogs消费失败回调,达到最大重试次数: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'package' => $package,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
