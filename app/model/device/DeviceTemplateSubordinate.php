<?php

namespace app\model\device;

use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class DeviceTemplateSubordinate extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function variable(){
        return $this->hasMany(DeviceTemplateSubordinateVariable::class,'template_subordinate_id')->order("sort desc");
    }

    public function searchNotIdsAttr($query, $value)
    {
        if ($value!=='') $query->whereNotIn('id',$value);
    }

    public function getProtocol($id){
        return self::where("id",$id)->value("protocol");
    }

}
