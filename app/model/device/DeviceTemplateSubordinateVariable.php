<?php

namespace app\model\device;

use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class DeviceTemplateSubordinateVariable extends BaseModel
{
    public function searchNotIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereNotIn('id',$value);
    }


    public function searchNotTemplateSubordinateIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereNotIn('template_subordinate_id',$value);
    }


}
