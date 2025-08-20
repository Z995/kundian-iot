<?php

namespace app\model;

use plugin\kundian\base\BaseModel;
use think\Model;

/**
 * Class Model
 * @package think
 * @mixin Query
 * @method static \think\Model findOrEmpty() 查询
 */
class WebhookLogs extends BaseModel
{
    //状态类型
    const STATUS_LIST = [0 => '执行成功', 1 => '执行失败'];
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
