<?php

namespace app\services\label;

use app\model\Admin;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayInstruct;
use app\model\label\Label;
use app\model\label\LabelCategory;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class LabelCategoryServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return LabelCategory::class;
    }



}