<?php
/*
 * @Author: duyingjie duyingjie@qq.com
 * @Date: 2023-01-29 15:05:32
 * @LastEditors: duyingjie duyingjie@qq.com
 * @LastEditTime: 2023-06-02 08:39:54
 * @FilePath: \erpd:\phpstudy_pro\webman\app\queue\redis\MyMailSend.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\queue\redis;

use app\model\DeviceLogs as ModelDeviceLogs;
use app\model\Iot;
use app\services\alarm\AlarmIndependenceTriggerServices;
use app\services\alarm\AlarmTemplateTriggerServices;
use app\services\device\DeviceServices;
use app\services\device\DeviceSubordinateVariableLogServices;
use app\services\monitor\MonitorAutoServices;
use app\services\monitor\MonitorServices;
use support\Log as SysLog;
use think\facade\Db;
use Webman\RedisQueue\Consumer;

class AutoWork implements Consumer
{
    // 要消费的队列名
    public $queue = '';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';
    //日志文件
    public $logFile = 'queue-error';

    public function __construct()
    {
        $this->queue = 'iots_redis_queue_auto_work';
    }

    public function consume($data)
    {
        try {
            $MonitorServices=new MonitorServices();
            (new MonitorAutoServices())->autoWorkExecute($data,$MonitorServices);
        } catch (\Throwable $e) {
            SysLog::channel($this->logFile)->info('auto_work-异常: ' . $e->getMessage(), [
                'package' => $data,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

}
