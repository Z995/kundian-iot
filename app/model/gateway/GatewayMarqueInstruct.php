<?php

namespace app\model\gateway;

use app\services\gateway\GatewayMarqueInstructServices;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class GatewayMarqueInstruct extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function instruct(){
        return $this->belongsTo(GatewayInstruct::class,'instruct_id');
    }

    public function searchNotIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereNotIn('id',$value);
    }

}
