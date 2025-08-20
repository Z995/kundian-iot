<?php

namespace app\services\gateway;

use app\model\Admin;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayOnlineLog;
use app\model\Iot;
use app\serve\ModbusRTUServices;
use app\serve\RedisServices;
use app\serve\Snowflake;
use extend\Request;
use plugin\kundian\base\BaseServices;
use plugin\webman\gateway\Events;
use plugin\webman\gateway\servers\SortingDataServices;
use support\Redis;
use think\exception\ValidateException;


class GatewayOnlineLogServices extends BaseServices
{


    /**
     * @return string
     */
    protected  function setModel(): string
    {
        return GatewayOnlineLog::class;
    }


    public static function saveOnlineLog($gateway,$online){
        if(!empty($gateway)){
            (new self())->save(["online"=>$online,"code"=>$gateway["code"],"admin_id"=>$gateway["admin_id"],"gateway_id"=>$gateway["id"],"name"=>$gateway['name']]);
        }
    }




}