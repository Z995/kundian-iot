<?php

namespace plugin\webman\gateway\servers;

use app\model\Admin;
use app\model\Iot;
use app\serve\ModbusRTUServices;
use app\serve\RedisServices;
use app\services\device\DeviceServices;
use app\services\device\DeviceSubordinateVariableServices;
use app\services\gateway\GatewayServices;
use GatewayWorker\Lib\Gateway;
use plugin\kundian\base\BaseServices;
use support\Log;
use support\Redis;
use think\exception\ValidateException;
use Workerman\Timer;

class TimeServices
{

    /**
     * 创建定时器
     * @param $client_id
     * @param $code
     * @param $is_execution
     * @return false|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function createTimer($client_id, $code)
    {
        var_dump("设备连接-" . $code . "-client_id:" . $client_id);
        $_SESSION['timerId'] = Timer::add(0.5, function () use ($client_id, $code) {
            //执行任务
            self::execution($client_id, $code);
            //添加系统定时任务
            if (empty(RedisServices::getTimerExecution($code))) {//定时任务间隔
                $timer = self::getTimeList($code);//获取需要执行的定时任务
                if (!empty($timer["time_list"])) {
                    foreach ($timer["time_list"] as $v) {
                        if ($v['execution_time'] < time()) {
                            var_dump("系统添加任务-" . $code . ":" . $v["command"]);
                            //将任务放入执行队列
                            RedisServices::addExecuteList($code, $v["command_type"], ["command" => $v["command"], "function_code" => $v["function_code"], "variable_id" => $v['id'], "subordinate_id" => $v['subordinate_id'], "template" => $v['template']], "left");
                            $v["execution_time"] = time() + $v["collect_frequency"];
                            RedisServices::setRedisTime($code, $v['id'], $v);//跟新任务执行时间
                        }
                    }
                    RedisServices::setTimerExecution($code);
                }
            }
            //添加用户定时任务
            if (empty(RedisServices::getCrontabExecution($code))) {
                $gateway = RedisServices::getGatewayRedis($code);
                if (!empty($gateway["crontab"])&&!empty($gateway["crontab"]['status'])) {
                    $crontab = $gateway["crontab"];
                    $val = explode(',', $crontab['val']);
                    foreach ($val as $va) {
                        if (!empty($va)) {
                            var_dump("用户添加任务-" . $code . ":" . $va);
                            RedisServices::addExecuteList($code, SortingDataServices::$Crontab, ["command" => $va,"val_type"=>$crontab["val_type"]], "left");
                        }
                    }
                    RedisServices::setCrontabExecution($code, $crontab['expire']);
                }
            }

        });
    }


    /**
     * 执行客户端命令
     * @param $client_id
     * @param $code
     * @return false|void
     */
    public static function execution($client_id, $code)
    {
        if (self::isExecuteTheNextCommand($code)) {
            $myVal = RedisServices::readExecuteList($code);//读取第一个
            if (!empty($myVal)) {
                RedisServices::setIsExecuteTheNextCommand($code, $myVal);//缓存执行的命令
                $command = $myVal["command_param"]['command'];
                var_dump("执行任务-" . $code ." ". $myVal["command_type"]."--" . $command);//转bin之后可能无法打印
                switch ($myVal["command_type"]) {
                    case SortingDataServices::$WebSocket:
                        $command = self::webSocketCommand($myVal["command_param"]);//websocket需要重新编辑命令
                        break;
                    case SortingDataServices::$ModbusRTUS:
                        $command = ModbusRTUServices::myHexTobin($command);
                        break;
                    case SortingDataServices::$Crontab:
                        if ($myVal["command_param"]["val_type"]==2){//1 ASCII  2HEX
                            $command = ModbusRTUServices::myHexTobin($command);
                        }
                        break;
                }
                Gateway::sendToClient($client_id, $command);//执行命令
            }
        }
    }

    public static function webSocketCommand($command_param){
        $val=$command_param["command"];
        //HEX类型
        if (strtoupper($command_param['type']) == '1') {
            $val = ModbusRTUServices::myHexTobin($val);
        }
        //GB2312
        if (strtoupper($command_param['type']) == '2') {
            $val = iconv("UTF-8", "gb2312//IGNORE", $val);
        }
        $line_feed=myHexTobin("0d0a");//换行
        return $val . ($command_param['eol'] == 1 ? $line_feed : '');
    }
    /**
     * 是否获取下一条命令执行
     * @param $code
     * @return bool
     */
    public static function isExecuteTheNextCommand($code)
    {
        $result = RedisServices::getIsExecuteTheNextCommand($code);
        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *获取定时任务列表
     * @param $device_code
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getTimeList($device_code)
    {
        $time_list = RedisServices::getRedisTime($device_code);
        if (empty($time_list)) {
            $gateway = GatewayServices::getGateway($device_code);
            if (!$gateway) {
                return ["status" => false, "text" => "网关不存在"];
            }
            $device = DeviceServices::getDeviceByGatewayId($gateway["id"]);
            if (!$device) {
                return ["status" => false, "text" => "设备不存在"];
            }
            foreach ($device as $d) {
                $subordinate = $d['subordinate'] ?? [];
                foreach ($subordinate as $s) {
                    if (empty($s['subordinate_address'])) continue;//从机地址
                    $protocol=$s["subordinate"]["protocol"];
                    $variable = $s["variable"] ?? [];
                    foreach ($variable as $v) {
                        $templateVariable = $v["templateVariable"] ?? [];
                        $list = RedisServices::saveRedisTime($protocol,$templateVariable, $v['id'], $v["subordinate_id"], $device_code, $s['subordinate_address']);
                        if ($list) $time_list[] = $list;
                    }
                }
            }
        }
        return ["status" => true, "time_list" => $time_list];
    }


}