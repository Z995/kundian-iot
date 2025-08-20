<?php

namespace app\model\gateway;

use app\services\gateway\GatewayMarqueInstructServices;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class GatewayMarque extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function instruct(){
        return $this->hasMany(GatewayMarqueInstruct::class,'marque_id');
    }

    public function searchNameAttr($query, $value)
    {
        if ($value!=='') $query->where('name','like', $value . '%');
    }

}
