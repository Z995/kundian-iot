<?php

namespace app\services\gateway;

use app\model\Admin;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayInstruct;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class GatewayInstructServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return GatewayInstruct::class;
    }



}