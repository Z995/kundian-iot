<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-01-28 15:13:57
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-04-01 19:44:10
 * @FilePath: \yunxiao-webman\app\controller\IndexController.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

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
