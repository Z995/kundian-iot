<?php

namespace app\model;

use app\model\device\Device;
use app\model\gateway\Gateway;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class Admin extends BaseModel
{
    public function device(){
        return $this->hasMany(Device::class,"admin_id");
    }
    public function gateway(){
        return $this->hasMany(Gateway::class,"admin_id");
    }
    public function searchKeyWordAttr($query, $value)
    {
        if ($value!=='') $query->where('name|phone',"like","%".$value."%");
    }
}
