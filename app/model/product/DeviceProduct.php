<?php

namespace app\model\product;

use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class DeviceProduct extends BaseModel
{
    public function searchNameAttr($query, $value)
    {
        if ($value!=='') $query->where('name','like', $value . '%');
    }

    public function variable(){
        return $this->hasMany(DeviceProductVariable::class,'product_id');
    }
}
