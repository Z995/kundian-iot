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


namespace app\queue\redis\heartbeat;

use app\model\DeviceLogs as ModelDeviceLogs;
use app\model\Iot;
use app\serve\HeartbeatFilter;
use app\serve\ModbusRTUServices;
use app\services\device\DeviceServices;
use app\services\device\DeviceSubordinateVariableLogServices;
use support\Log as SysLog;
use think\facade\Db;
use Webman\RedisQueue\Consumer;

//设备同步队列
class Filter implements Consumer
{
    // 要消费的队列名
    public $queue = '';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';
    //日志文件
    public $logFile = 'queue-error';

    public function __construct()
    {
        $this->queue = 'iots_redis_queue_heartbeat_filter';
    }
    public function consume($data)
    {
        //不是ModbusRTUS格式数据记录到过滤中
        try {
            ModbusRTUServices::verificationModbusRTUS($data["message"]);//不进行crc校验
            $is_record=false;
        }catch (\Throwable){//不是的才记录
            $is_record=true;
        }

        try {
            if ($is_record){
                HeartbeatFilter::processTempCache($data["code"],$data["message"],$data["is_permanent"]);
            }
        } catch (\Throwable $e) {
            SysLog::channel($this->logFile)->info('heartbeat_filter异常: ' . $e->getMessage(), [
                'package' => $data,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

}
