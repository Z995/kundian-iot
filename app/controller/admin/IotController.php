<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-01-28 15:13:57
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-04-01 19:44:10
 * @FilePath: \yunxiao-webman\app\controller\IndexController.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\controller\admin;

use app\model\DeviceLogs;
use app\model\Iot;
use app\model\WebhookLogs;
use app\services\ModbusRTUServices;
use app\services\RedisServices;
use extend\Debug;
use extend\Helper;
use support\Redis;
use extend\Page;
use extend\Request;
use support\View;

class IotController
{
    public function index()
    {
        return json([122]);
    }

}
