<?php

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
