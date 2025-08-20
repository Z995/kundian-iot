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

class GatewayValidate extends Validate
{
    //1分钟最多请求10次
    const RATE_LIMIT = 10;


    protected $rule =   [
        'id'  => 'integer',
        'mac'  => 'require',
        'marque_id'  => 'integer',
        'network'  => 'in:1,2,3,4',
        'locate'  => 'in:1,2',
        'log'  => 'in:0,1',
        'type'  => 'in:0,1,2',
        'rtype'  => 'in:0,1',
        'vtype'  => 'in:0,1',

    ];

    protected $message  =   [

    ];
    //场景
    protected $scene = [
        'saveGateway' => ['id', 'marque_id', 'network', 'locate', 'log', 'type', 'rtype', 'vtype', 'code'],
    ];

}
