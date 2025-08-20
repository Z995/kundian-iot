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
use app\model\gateway\Gateway;
use app\model\gateway\GatewayLog;
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
            $iot = Gateway::where([['code', '=', $data['from']], ['del', '=', 0]])->findOrEmpty()->toArray();
            if (!$iot) {
                Log::channel($this->logFile)->info('DeviceLogs消费捕获异常: ' . '设备不存在');
                return;
            }
            //组合日志数据
            $logs = [];
            $logs['gateway_id'] = $iot['id'];
            $logs['admin_id'] = $iot['admin_id'];
            $logs['val'] = $data['msg'];
            $logs['year'] = date('Y', strtotime($data['time']));
            $logs['month'] = date('m', strtotime($data['time']));
            $logs['day'] = date('d', strtotime($data['time']));
            $logs['date'] = date('Y-m-d', strtotime($data['time']));
            $logs['create_time'] = $data['time'];
            GatewayLog::create($logs);
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

}
