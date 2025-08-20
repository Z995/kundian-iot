<?php

namespace app\model\gateway;

use app\model\device\Device;
use plugin\kundian\base\BaseModel;
use plugin\webman\gateway\Events;


/**
 * Class Model
 * @package think
 */
class GatewayOnlineLog extends BaseModel
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
