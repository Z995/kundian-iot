<?php

namespace app\process;

use app\services\monitor\MonitorAutoServices;
use extend\RedisQueue;
use Workerman\Crontab\Crontab;

class AutoWorkTimer
{
    /**
     * 监控定时任务
     * @return void
     */
    public function onWorkerStart()
    {
        // 每30分钟执行一次
        new Crontab('0 */30 * * * *', function(){
            $MonitorAutoServices=new MonitorAutoServices();
            $MonitorAutoServices->executeAuto();
        });
    }
}