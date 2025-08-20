<?php

namespace app\model\label;

use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class LabelCategory extends BaseModel
{

    public function label(){
        return $this->hasMany(Label::class,"category_id");
    }

}
