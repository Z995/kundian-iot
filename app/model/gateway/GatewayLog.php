<?php

namespace app\model\gateway;

use app\model\device\Device;
use app\services\gateway\GatewayOnlineLogServices;
use plugin\kundian\base\BaseModel;
use plugin\webman\gateway\Events;


/**
 * Class Model
 * @package think
 */
class GatewayLog extends BaseModel
{
    public function searchYearAttr($query, $value)
    {
        if ($value!=='') $query->where('year', $value);
    }
    public function searchMonthAttr($query, $value)
    {
        if ($value!=='') $query->where('month', $value);
    }
    public function searchDayAttr($query, $value)
    {
        if ($value!=='') $query->where('day', $value);
    }

}
