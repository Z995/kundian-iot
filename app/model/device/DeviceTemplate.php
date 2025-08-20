<?php

namespace app\model\device;

use app\model\gateway\GatewayMarqueInstruct;
use app\services\device\DeviceTemplateSubordinateServices;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class DeviceTemplate extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function subordinate(){
        return $this->hasMany(DeviceTemplateSubordinate::class,'template_id');
    }

    public function variable(){
        return $this->hasMany(DeviceTemplateSubordinateVariable::class,'template_id');
    }

    public function device(){
        return $this->hasMany(Device::class,'template_id')->where("is_del",0);
    }

    public function searchNameAttr($query, $value)
    {
        if ($value!=='') $query->where('name','like', $value . '%');
    }

}
