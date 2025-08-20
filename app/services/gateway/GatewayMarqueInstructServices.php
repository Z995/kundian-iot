<?php

namespace app\services\gateway;

use app\model\Admin;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayMarque;
use app\model\gateway\GatewayMarqueInstruct;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class GatewayMarqueInstructServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return GatewayMarqueInstruct::class;
    }




}