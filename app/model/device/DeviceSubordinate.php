<?php

namespace app\model\device;

use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class DeviceSubordinate extends BaseModel
{

    public function subordinate(){
        return $this->belongsTo(DeviceTemplateSubordinate::class,"template_subordinate_id");
    }

    public function variable(){
        return $this->hasMany(DeviceSubordinateVariable::class,"subordinate_id")->where("is_del",0)->order("sort desc");
    }

    public function searchNotIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereNotIn('id',$value);
    }
    public function searchIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereIn('id',$value);
    }
    public function searchNotTemplateSubordinateIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereNotIn('template_subordinate_id',$value);
    }

}
