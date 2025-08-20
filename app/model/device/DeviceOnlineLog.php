<?php

namespace app\model\device;

use app\model\gateway\Gateway;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class DeviceOnlineLog extends BaseModel
{
    public function searchStartTimeAttr($query, $value)
    {
        if ($value!=='') $query->where('create_time','>=',$value);
    }
    public function searchEndTimeAttr($query, $value)
    {
        if ($value!=='') $query->where('create_time','<=',$value);
    }

}
