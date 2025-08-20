<?php

namespace app\model\alarm;

use app\model\device\Device;
use app\model\device\DeviceSubordinate;
use app\model\device\DeviceSubordinateVariable;
use app\model\device\DeviceSubordinateVariableLog;
use app\model\device\DeviceTemplate;
use app\model\device\DeviceTemplateSubordinate;
use app\model\device\DeviceTemplateSubordinateVariable;
use app\model\gateway\Gateway;
use app\services\device\DeviceServices;
use app\services\device\DeviceTemplateServices;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class AlarmIndependenceTrigger extends BaseModel
{


    public function device(){
        return $this->belongsTo(Device::class,"device_id");
    }
    public function subordinate(){
        return $this->belongsTo(DeviceSubordinate::class,"subordinate_id");
    }
    public function subordinateVariable(){
        return $this->belongsTo(DeviceSubordinateVariable::class,"subordinate_variable_id");
    }
    public function linkageDevice(){
        return $this->belongsTo(Device::class,"linkage_device_id");
    }
    public function linkageSubordinate(){
        return $this->belongsTo(DeviceSubordinate::class,"linkage_subordinate_id");
    }
    public function linkageSubordinateVariableId(){
        return $this->belongsTo(DeviceSubordinateVariable::class,"linkage_subordinate_variable_id");
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

    public function searchDeviceNameAttr($query, $value)
    {
        if ($value!=='') {
            $ids=(new DeviceServices())->getColumn(["name"=>$value],'id');
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
