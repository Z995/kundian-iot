<?php

namespace app\validate;

use app\model\Iot;
use app\model\Keys;
use think\Validate;

class IdValidate extends Validate
{

    protected $rule =   [
        'id'  => 'require|integer',
    ];



}
