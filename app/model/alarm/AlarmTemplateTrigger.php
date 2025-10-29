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

namespace app\model\alarm;

use app\model\device\DeviceSubordinate;
use app\model\device\DeviceSubordinateVariableLog;
use app\model\device\DeviceTemplate;
use app\model\device\DeviceTemplateSubordinate;
use app\model\device\DeviceTemplateSubordinateVariable;
use app\model\gateway\Gateway;
use app\services\device\DeviceTemplateServices;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class AlarmTemplateTrigger extends BaseModel
{

    public function template(){
        return $this->belongsTo(DeviceTemplate::class,"template_id");
    }
    public function templateSubordinate(){
        return $this->belongsTo(DeviceTemplateSubordinate::class,"template_subordinate_id");
    }
    public function templateSubordinateVariable(){
        return $this->belongsTo(DeviceTemplateSubordinateVariable::class,"template_subordinate_variable_id");
    }
    public function linkageSubordinate(){
        return $this->belongsTo(DeviceTemplateSubordinate::class,"linkage_subordinate_id");
    }

    public function linkageSubordinateVariableId(){
        return $this->belongsTo(DeviceTemplateSubordinateVariable::class,"linkage_subordinate_variable_id");
    }

    protected function getConditionParameterAttr($value)
    {
        if (is_string($value))return json_decode($value,true);
        return $value;
    }

    protected function setConditionParameterAttr($value)
    {
        if (is_array($value))return json_encode($value);
        return $value;
    }

    public function searchTemplateNameAttr($query, $value)
    {
        if ($value!=='') {
            $ids=(new DeviceTemplateServices())->getColumn(["name"=>$value],'id');
            if (!empty($ids)){
                $query->whereIn('id',$ids);
            }
        }
    }
    public function searchNameAttr($query, $value)
    {
        if ($value!=='') $query->where('name','like', $value . '%');
    }


}
