<?php

namespace app\model;

use think\Model;
use support\Redis;

/**
 * Class Model
 * @package think
 */
class DeviceLogs extends Model
{
    public function getIdAttr($value)
    {
        return (string)$value;
    }
    public function getMemberIdAttr($value)
    {
        return (string)$value;
    }
    public function getDeviceIdAttr($value)
    {
        return (string)$value;
    }
}
