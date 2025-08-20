<?php


namespace app\validate\admin;

use app\model\Iot;
use app\model\Keys;
use think\Validate;

class AdminValidate extends Validate
{
    //1分钟最多请求10次
    const RATE_LIMIT = 10;


    protected $rule =   [
        'phone'  => 'require',
        'password'  => 'require',

    ];

    protected $message  =   [
        'phone.require' => '账号不能为空',
        'password.require' => '密码不能为空',
    ];
    //场景
    protected $scene = [
        'login' => ['phone', 'password'],

    ];

}
