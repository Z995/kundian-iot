<?php

namespace app\model;

use think\Model;

/**
 * Class Model
 * @package think
 * @mixin Query
 * @method static \think\Model findOrEmpty() 查询
 * @method static \think\Model update() 修改
 */
class WebhookLogs extends Model
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
