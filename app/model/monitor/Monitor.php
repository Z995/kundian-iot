<?php

namespace app\model\monitor;

use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class Monitor extends BaseModel
{

    public function searchNameAttr($query, $value)
    {
        if ($value!=='') $query->where('name|desc','like', $value . '%');
    }
}
