<?php

namespace app\model\alarm;

use app\model\device\DeviceSubordinate;
use app\model\device\DeviceSubordinateVariableLog;
use app\model\device\DeviceTemplate;
use app\model\device\DeviceTemplateSubordinate;
use app\model\device\DeviceTemplateSubordinateVariable;
use app\model\gateway\Gateway;
use app\services\device\DeviceTemplateServices;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class AlarmWarningLog extends BaseModel
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
