<?php
/*
 * @Author: duyingjie duyingjie@qq.com
 * @Date: 2023-01-29 15:05:32
 * @LastEditors: duyingjie duyingjie@qq.com
 * @LastEditTime: 2023-06-02 08:39:54
 * @FilePath: \erpd:\phpstudy_pro\webman\app\queue\redis\MyMailSend.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\queue\redis\synchronize;

use app\model\DeviceLogs as ModelDeviceLogs;
use app\model\Iot;
use app\services\device\DeviceServices;
use support\Log;
use think\facade\Db;
use Webman\RedisQueue\Consumer;

//设备同步队列
class Device implements Consumer
{
    // 要消费的队列名
    public $queue = '';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';
    //日志文件
    public $logFile = 'queue-error';

    public function __construct()
    {
        $this->queue = 'iots_redis_queue_synchronize_device';
    }
    public function consume($data)
    {
        //开启事务
        try {
            (new DeviceServices())->synchronize($data["template_id"]);
        } catch (\Throwable $e) {
            Log::channel($this->logFile)->info('synchronize消费捕获异常: ' . $e->getMessage(), [
                'package' => $data,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

}
