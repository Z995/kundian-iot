<?php


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
