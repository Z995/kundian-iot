<?php

namespace app\model;

use think\Model;

/**
 * Class Model
 * @package think
 * @mixin Query
 * @method static \think\Model findOrEmpty() 查询
 * @method static \think\Model create() 新增
 * @method static \think\Model update() 修改
 */
class Iot extends Model
{
    //协议类型
    const TYPE_LIST = [0 => 'TCP', 1 => 'WS/WSS', 2 => 'MQTT'];
    //数据类型
    const VTYPE_LIST = [0 => 'ASCII', 1 => 'HEX', 2 => 'GB2312'];
    public function getIdAttr($value)
    {
        return (string)$value;
    }
}
