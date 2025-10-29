<?php

/**
 * 坤典智慧农场V6
 * @link https://www.cqkd.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing KunDian Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing KunDian Technology Co., Ltd.
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-www.cqkd.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 */

namespace app\model;

use plugin\kundian\base\BaseModel;
use think\Model;

/**
 * Class Model
 * @package think
 * @mixin Query
 * @method static \think\Model findOrEmpty() 查询
 * @method static \think\Model create() 新增
 */
class Iot extends BaseModel
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
