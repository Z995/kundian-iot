<?php

namespace app\model\device;

use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class DeviceSubordinateVariableLog extends BaseModel
{

    public function templateSubordinateVariable(){
        return $this->belongsTo(DeviceTemplateSubordinateVariable::class,"template_subordinate_variable_id");
    }

    public function searchStartTimeAttr($query, $value)
    {
        if ($value!=='') $query->where('create_time',">=",$value);
    }
    public function searchEndTimeAttr($query, $value)
    {
        if ($value!=='') $query->where('create_time',"<=",$value);
    }

}
