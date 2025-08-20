<?php

namespace app\model\product;

use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class DeviceProductVariable extends BaseModel
{

    public function searchNotIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereNotIn('id',$value);
    }


}
