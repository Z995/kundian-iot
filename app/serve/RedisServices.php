<?php

namespace app\serve;

use app\services\device\DeviceSubordinateVariableServices;
use plugin\webman\gateway\Events;
use plugin\webman\gateway\servers\TimeServices;
use support\Redis;
use think\exception\ValidateException;

class RedisServices
{

    static $token_admin_start = "admin-token:";
    static $token_bearer = "Bearer ";

    static $timer_list_key = "device_timer_list";
    static $gateway_list_key = "gateway_list";
    static $gateway_filter_list = "gateway_filter_list";
    static $gateway_directive_list = "gateway_directive_list";//缓存被动回复
    static $timer_execution = "timer_execution";//变量定时器
    static $crontab_execution = "crontab_execution";
    static $gateway_command = "gateway_command_queue";
    static $isExecuteTheNextCommand = "is_execute_the_next_command";//是否执行获取下一条命令
    static $TextPortToken = "TextPortToken";//text 协议token验证
    //采集频率【0：不采集，1:1分钟，2:3分钟，3:5分钟，4:10分钟，5:15分钟，6:20分钟，7:30分钟，8:1小时，9:5小时，10:1天，11:5天，12:15天】
    static $collect_frequency = [1 => 60, 2 => 180, 3 => 300, 4 => 600, 5 => 900, 6 => 1200, 7 => 1800, 8 => 3600, 9 => 18000, 10 => 86400, 11 => 432000, 12 => 1296000];
    static $timeArr = array(1 => 1, 2 => 0.5, 3 => 60, 4 => 30, 5 => 3, 6 => 10, 7 => 600, 8 => 3600);

    public static function setGatewayRedis($data)
    {
        $data["uid"]= $data['code'] . '-Uid';
        if (!empty($data['directive'])) { //缓存被动回复
            $directive = $data['directive'];
            if (is_array($directive)) {
                $directive = json_encode($directive, JSON_UNESCAPED_UNICODE);
            }
            Redis::hSet(self::$gateway_directive_list, $data['code'], $directive);
        }
        if (!empty($data['filter'])) {//缓存过滤
            $filter = $data['filter'];
            if (is_array($filter)) {
                $filter = json_encode($filter, JSON_UNESCAPED_UNICODE);
            }
            Redis::hSet(self::$gateway_filter_list, $data['code'], $filter);
        }
        if (!empty($data['crontab'])&&$data["crontab"]["status"]==1) {//缓存定时任务
            $data['crontab']=["expire"=>self::$timeArr[$data['crontab']["expire"]],"val"=>$data["crontab"]["val"],"val_type"=>$data["crontab"]["val_type"]];
        }
        Redis::hSet(self::$gateway_list_key, $data['code'], json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取网关
     * @param $code
     * @return false|string
     */
    public static function getGatewayRedis($code)
    {
        $result = Redis::hGet(self::$gateway_list_key, $code);
        if ($result) {
            $result = json_decode($result, true);
        }
        return $result;
    }

    /**
     * 判断 表中是否缓存了网关
     * @param $code
     * @return bool
     */
    public static function isGatewayRedis($code)
    {
        return Redis::hExists(self::$gateway_list_key, $code);
    }

    /**
     * 删除
     * @param $code
     * @return false|int
     */
    public static function delGatewayRedis($code)
    {
        Redis::hDel(self::$gateway_list_key, $code);
        if (Redis::hExists(self::$gateway_directive_list, $code)) Redis::hDel(self::$gateway_directive_list, $code);
        if (Redis::hExists(self::$gateway_filter_list, $code)) Redis::hDel(self::$gateway_filter_list, $code);
    }

    /**
     * 获取网关被动回复
     * @param $code
     * @return false|mixed|string
     */
    public static function getGatewayDirective($code)
    {
        $result = Redis::hGet(self::$gateway_directive_list, $code);
        if ($result) {
            $result = json_decode($result, true);
        }
        return $result;
    }

    /**
     * 获取过滤数据
     * @param $code
     * @return false|mixed|string
     */
    public static function getGatewayFilterRedis($code)
    {
        $result = Redis::hGet(self::$gateway_filter_list, $code);
        if ($result) {
            $result = json_decode($result, true);
        }
        return $result;
    }

    /**
     * 获取token中的信息
     * @param $token
     * @return bool|string
     */
    public static function getUserInfoByToken($token)
    {
        $token = ltrim($token, self::$token_bearer);
        $result = Redis::get(self::$token_admin_start . $token);
        if (!empty($result)) {
            $result = json_decode($result, true);
        }
        return $result;
    }


    /**
     * 生成token
     * @param $user_info
     * @return string
     */
    public static function setToken($user_info)
    {
        $token = md5(generateRandomString(50) . time());
        Redis::set(self::$token_admin_start . $token, json_encode($user_info), "Ex", 360000);
        return $token;
    }

    /**
     * 验证token
     * @return array|string
     */
    public function checkToken()
    {
        $token = request()->header('token');
        if (empty($token)) {
            throw new ValidateException('token 不能为空');
        }
        $info = $this->getUserInfoByToken($token);
        if (empty($info)) {
            throw new ValidateException('token 错误');
        }
        return $info;
    }

    /**
     * 保存设备定时任务列表
     * @param $templateVariable
     * @param $variable_id
     * @param $code
     * @param $subordinate_address
     * @return array|false
     */
    public static function saveRedisTime($protocol,$templateVariable,$variable_id,$subordinate_id,$code,$subordinate_address)
    {
        if (empty($templateVariable)) return false;//寄存器地址
        if ($templateVariable["collect_frequency"]==0) return false;//不采集
        if (!isset(self::$collect_frequency[$templateVariable["collect_frequency"]])) return false;//采集频率不存在
        [$command,$function_code,$command_type]= DeviceSubordinateVariableServices::generateCommands($protocol,$variable_id, $templateVariable,$subordinate_address);
        $list=["id"=>$variable_id,"subordinate_id"=>$subordinate_id,'command'=>$command,'function_code'=>$function_code,'command_type'=>$command_type,'template'=>$templateVariable,'subordinate_address'=>$subordinate_address,"code"=>$code,"register_address"=>$templateVariable["register_address"],"execution_time"=>0,"collect_frequency"=>self::$collect_frequency[$templateVariable["collect_frequency"]]];
        Redis::hSet(self::$timer_list_key . ":" . $code, $variable_id,  json_encode($list));
        return $list;
    }

    /**
     * 保存设备定时任务列表
     * @param $device_code
     * @param $list
     * @return void
     */
    public static function setRedisTime($code, $key, $list)
    {
        if (is_array($list)) {
            $list = json_encode($list);
        }
        Redis::hSet(self::$timer_list_key . ":" . $code, $key, $list);
    }

    /**
     * 删除设备定时任务列表
     * @param $device_code
     * @return void
     */
    public static function againRedisTime($code,$again=true)
    {
        $key=self::$timer_list_key . ":" . $code;
        if (Redis::exists($key)){
            Redis::Del($key);
        }
        if ($again){
            TimeServices::getTimeList($code);//重新缓存
        }
    }

    /**
     * 获取设备定时器任务
     * @param $device_code
     * @return mixed
     */
    public static function getRedisTime($code)
    {
        $result = Redis::hGetAll(self::$timer_list_key . ":" . $code);
        if ($result) {
            $data = [];
            foreach ($result as $v) {
                $data[] = json_decode($v, true);
            }
        }
        return $data ?? [];
    }

    /**
     * 获取定时器执行间隔
     * @return bool|string
     */
    public static function getTimerExecution($code)
    {
        return Redis::get(self::$timer_execution . ":" . $code);
    }

    /**
     * 设置定时器间隔
     * @return bool
     */
    public static function setTimerExecution($code)
    {
        //定时任务最低执行间隔1分钟
        return Redis::set(self::$timer_execution . ":" . $code, "1", "Ex", 30);
    }


    /**
     * 获取定时器执行间隔
     * @return bool|string
     */
    public static function getCrontabExecution($code)
    {
        return Redis::get(self::$crontab_execution . ":" . $code);
    }

    /**
     * 设置定时器间隔
     * @return bool
     */
    public static function setCrontabExecution($code,$expire)
    {
        return Redis::set(self::$crontab_execution . ":" . $code, "1", "Ex", $expire);
    }


    /**
     * 从头部添加
     * @param $code
     * @param $command_type
     * @param $command_param
     * @param $type
     * @return false|void
     */
    public static function addExecuteList($code, $command_type, $command_param,$type="right")
    {
        //command_type=ModbusRTUS $command_param=["command"=> $command, "function_code" => $function_code, "variable_id" => $variable_id]
        //command_type=Original $command_param=["command"=> $command]
        //command_type=AtInstruction $command_param=["command"=> $command,"type"=>$type,"instruct"=>$instruct]
        if ($type=="right"){//先执行
            Redis::rPush(self::$gateway_command . ":" . $code, json_encode(["command_type" => $command_type,"command_param" =>$command_param]));
        }
        if ($type=="left"){ //后执行
            Redis::lPush(self::$gateway_command . ":" . $code, json_encode(["command_type"=>$command_type,"command_param"=>$command_param]));
        }

    }


    /**
     * 读取列表第一个 从尾部开始取
     * @param $code
     * @return false|mixed|string
     */
    public static function readExecuteList($code)
    {
        $result= Redis::rPop(self::$gateway_command . ":" . $code);
        if ($result){
            $result = json_decode($result, true);
        }
        return $result;
    }


    /**
     * 是否执行下一条获取命令
     * @param $code
     * @return bool|mixed|string
     */
    public static function getIsExecuteTheNextCommand($code)
    {
        $result = Redis::get(self::$isExecuteTheNextCommand . ":" . $code);
        if ($result) {
            $result = json_decode($result, true);
        }
        return $result;
    }

    /**
     * 缓存执行的命令
     * @param $code
     * @return bool
     */
    public static function setIsExecuteTheNextCommand($code, $data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        return Redis::set(self::$isExecuteTheNextCommand . ":" . $code, $data, "Ex", 2);
    }

    /**
     * 删除判断
     * @param $code
     * @return bool
     */
    public static function delIsExecuteTheNextCommand($code)
    {
        return Redis::del(self::$isExecuteTheNextCommand . ":" . $code);
    }

    /**
     * 生成token
     * @return string
     * @throws \Exception
     */
    public static function createTextPortToken(){
        $key=generateRandomString(10);
        Redis::set(self::$TextPortToken . ":" . $key, 1, "Ex", 5);
        return $key;
    }

    /**
     * 验证token
     * @param $key
     * @return bool
     */
    public static function verificationTextPortToken($key){
        $result=Redis::get(self::$TextPortToken . ":" . $key);
        if (empty($result)){
            return false;
        }else{
            return true;
        }
    }

}