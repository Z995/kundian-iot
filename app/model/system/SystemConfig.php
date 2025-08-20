<?php

namespace app\model\system;

use app\model\product\DeviceProductVariable;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class SystemConfig extends BaseModel
{

    public function searchNameArrAttr($query, $value)
    {
        if ($value!=='') $query->whereIn('name', $value );
    }
}
