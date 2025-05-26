<?php

namespace app\model;

use think\Model;
use support\Redis;

/**
 * Class Model
 * @package think
 */
class Keys extends Model
{
    const STATUS_NAME = [0 => '未启用', 1 => '已启用'];
    public function getIdAttr($value)
    {
        return (string)$value;
    }
}
