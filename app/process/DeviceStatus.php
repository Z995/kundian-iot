<?php

namespace app\process;
use app\model\device\Device;
use app\model\gateway\Gateway;
use app\services\device\DeviceServices;
use Workerman\Crontab\Crontab;

class DeviceStatus
{
    public function onWorkerStart()
    {


        // 每3分钟执行一次
        new Crontab('0 */3 * * * *', function(){
            $deviceStatus=new Device();
            $count=$deviceStatus->where(["is_del"=>0])->count();
            $deviceStatus->updateDeviceStatus(1,$count);
        });

        // 每3分钟执行一次
        new Crontab('0 */30 * * * *', function(){
            $deviceStatus=new Gateway();
            $count=$deviceStatus->where(["del"=>0])->count();
            $deviceStatus->updateGatewayStatus(1,$count);
        });

    }
}