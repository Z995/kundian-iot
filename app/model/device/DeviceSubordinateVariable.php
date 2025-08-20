<?php

namespace app\model\device;

use app\services\device\DeviceSubordinateServices;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class DeviceSubordinateVariable extends BaseModel
{


    public function templateVariable(){
        return $this->belongsTo(DeviceTemplateSubordinateVariable::class,"template_subordinate_variable_id");
    }
    public function device(){
        return $this->belongsTo(Device::class,"device_id");
    }
    public function subordinate(){
        return $this->belongsTo(DeviceSubordinate::class,"subordinate_id");
    }
    public function variableLog(){
        return $this->hasOne(DeviceSubordinateVariableLog::class,"variable_id")->order("id desc");
    }

    public function searchNotIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereNotIn('id',$value);
    }


    public function searchNameAttr($query, $value)
    {
        if ($value!=='') $query->where('name','like', $value . '%');
    }
    public function searchTypeAttr($query, $value)
    {
        if ($value!=='') $query->where('type', $value);
    }
    public function searchNotSubordinateIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereNotIn('subordinate_id',$value);
    }
    public function searchSubordinateIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereIn('subordinate_id',$value);
    }

    public function searchIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereIn('id',$value);
    }
    public function searchDeviceIdAttr($query, $value)
    {
        if ($value!=='') $query->where('device_id',$value);
    }

}
