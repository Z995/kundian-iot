<?php

namespace app\services\device;

use app\model\Admin;
use app\model\device\Device;
use app\model\device\DeviceOnlineLog;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class DeviceOnlineLogServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return DeviceOnlineLog::class;
    }

    public static function saveOnlineLog($device,$status){
        (new self())->save(['admin_id'=>$device["admin_id"],'device_id'=>$device["id"],"name"=>$device["name"],"code"=>$device["code"],"status"=>$status]);
    }

}