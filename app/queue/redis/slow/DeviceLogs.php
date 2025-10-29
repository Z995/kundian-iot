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
