<?php

namespace plugin\webman\gateway\servers;


use app\exception\CrcException;
use app\serve\HeartbeatFilter;
use app\serve\ModbusRTUServices;
use app\serve\RedisServices;
use app\services\device\DeviceSubordinateVariableServices;
use extend\RedisQueue;
use GatewayWorker\Lib\Gateway;
use support\Log;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class SortingDataServices
{
    static $AtInstruction = "AtInstruction";
    static $ModbusRTUS = "ModbusRTUS";
    static $WebSocket = "WebSocket";
    static $Crontab = "Crontab";
    static $Alarm = "alarm";
    static $Customize = "Customize";

    public static function receive($message, $myKey,$gateway)
    {
        if ($myKey == "866250068273600") {
            var_dump("返回" . $myKey . ":" . $message);
        }
        //接收数据处理
        $wash = self::wash($message, $myKey);//基本过滤
        if (!$wash["status"]) {
            return false;
        }
        //缓存设备所有记录
        RedisQueue::queue('iots_redis_queue_device_logs', ['type' => $gateway['type'], 'from' => $myKey, 'msg' => $message, 'time' => date('Y-m-d H:i:s')]);

        if (HeartbeatFilter::isHeartbeat($wash["data"])){//重复数据过滤 ,防止进入下面解析中
            return false;
        }

        try {
            $is_permanent=false;//默认不进行永久过滤
            $verification = self::verificationReturnData($myKey, $gateway, $wash["data"]);
        }catch (\Throwable $e){
            $verification=false;//记录过滤
            if (!$e instanceof CrcException){//Crc校验不通过的不进行永久过滤缓存
                $is_permanent=true; //报错的解析参数永久过滤
            }
            var_dump("解析异常：" . "file：" . $e->getFile() . " error：" . $e->getMessage() . " line：" . $e->getLine());
            Log::info('verificationReturnData error', ['error' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'trace' => $e->getTraceAsString(), 'message' => $wash["data"]]);
        }

        if (!$verification) {//记录过滤
            RedisQueue::queue('iots_redis_queue_heartbeat_filter', ["code"=>$myKey,'message' => $wash["data"],"is_permanent"=>$is_permanent]);
            return false;
        }


        if (is_array($verification)){
            $message=$verification;
        }
        self::sendGateway($gateway,$myKey,$message);
        return true;
    }

    /**
     * 发送消息
     * @param $gateway
     * @param $myKey
     * @param $message
     * @return void
     */
    public static function sendGateway($gateway,$myKey,$message){
        $uidArray = [];
        //客户端需转发
        if (!empty($gateway['forward'])){
            $forward = explode(',', $gateway['forward']);
        }
        $forward[]=$gateway["admin_id"];
        foreach ($forward as $v) {
            $uidArray[] = $v . '-Uid';
        }
        //获取对应的clientId
        $arr = array('k' => $myKey, 'v' => $message, 't' => date('Y-m-d H:i:s'));
        Gateway::sendToUid($uidArray, json_encode($arr, JSON_UNESCAPED_UNICODE));
        //需http发送,添加队列
        if (!empty($gateway['http'])){
            $httpClient = array_filter(array_unique(explode(',', $gateway['http'])));
            foreach ($httpClient as $v) {
                RedisQueue::queue('iots_redis_queue_http_api', ['type' => $gateway['type'], 'url' => $v, 'vtype' => $gateway['vtype'], 'msg' => $message, 'from' => $myKey, 'time' => date('Y-m-d H:i:s')]);
            }
        }
    }
    /**
     * 验证设备返回数据
     * @param $code
     * @param $gateway
     * @param $arrange_data
     * @return bool|void
     */
    public static function verificationReturnData($code, $gateway, $wash_data)
    {
        $data = RedisServices::getIsExecuteTheNextCommand($code);
        if (empty($data)) {
            return false;
        }
        RedisServices::delIsExecuteTheNextCommand($code);//删除发送命令判断
        $result=false;
        switch ($data["command_type"]) {//解析返回值
            case self::$Crontab:
            case self::$WebSocket:
                var_dump("设备".$data["command_type"]."---".$wash_data);
                $result= self::returnData($data["command_type"],['data'=>$wash_data]);
                break;
            case self::$AtInstruction:
                var_dump("设备".$data["command_type"]."---".$wash_data);
                $result= self::parsingAtInstruction($code,$wash_data,$data["command_param"]);
                break;
            case self::$ModbusRTUS:
                $modbus_param= ModbusRTUServices:: getModbusParameter($data["command_param"]["template"]);
                $parse= ModbusRTUServices:: parseFrame($wash_data,$modbus_param["type"],$modbus_param["byte_order"]);
                $result=$parse["success"];
                var_dump("设备".$data["command_type"]."---"."返回：".$wash_data." ".json_encode($parse));
                if ($parse["success"]&&$parse['is_log']){
                    $result=self::saveModbusRTUSLog($code,$data["command_param"],$parse["data"],$gateway);
                }
                break;
            case self::$Customize:
                $result=self::parsingCustomize($data["command_type"],$wash_data,$data["command_param"],$gateway);
                break;
        }
        return $result;
    }

    /**
     * 保存日志
     * @param $code
     * @param $command_param
     * @param $parse
     * @param $gateway
     * @return array|false
     */
    public static function saveModbusRTUSLog($code,$command_param,$parse,$gateway){
        if ($command_param["function_code"] == $parse["function_code"]) {
            $gateway['log'] = $gateway['log'] ?? 1;  //添加队列存储数据
            $value=self::calculate($command_param["template"],$parse['value']);
            DeviceSubordinateVariableServices::saveVariableValue($command_param["variable_id"],$value);
            if ($gateway['log'] == 1) {
                RedisQueue::queue('iots_redis_queue_variable_log', ['gateway_id' => $gateway['id'], 'code' => $code,
                    'subordinate_address' => $parse['address'], 'function_code' => $parse['function_code'],
                    'variable_id' => $command_param["variable_id"], 'value' => $value]);
            }
            RedisQueue::queue('iots_redis_queue_trigger', ['variable_id' => $command_param["variable_id"],
                'template_id' => $command_param["template"]["id"], 'value' =>$value]);
            return self::returnData(self::$ModbusRTUS,['value'=>$value,'variable_id'=>$command_param["variable_id"],'subordinate_id'=>$command_param["subordinate_id"],"date"=>date("Y-m-d H:i:s")]);
        }
        //重新将命令添加到尾部 变成下一条执行的命令
        RedisServices::addExecuteList($code, self::$ModbusRTUS, $command_param);// 3读取命令
        return false;
    }

    /**
     * 计算
     * @param $templateVariable
     * @param $value
     * @return float
     */
    public static function calculate($templateVariable,$value){
        if (!empty($templateVariable["collect_formula"])) { //表达式
            try {
                $collect_formula = $templateVariable["collect_formula"];
                $collect_formula = str_replace("%s", $value, $collect_formula);
                $formula_value = (new ExpressionLanguage())->evaluate($collect_formula);
            } catch (\Throwable $Throwable) {}
            if (!empty($formula_value)) {
                $value=$formula_value;
            }
        }
       return round($value,(int)$templateVariable["fraction"]);//保留位数
    }

    /**
     * 解析at指令
     * @param $code
     * @param $wash_data
     * @param $command_param
     * @return array|false|void
     */
    public static function parsingAtInstruction($code,$wash_data,$command_param){
        $wash_data=hex2bin($wash_data);
        $noSpecialSpaceStr = preg_replace('/\s+/', ' ', $wash_data); // 将所有特殊空格替换为一个普通空格
        if (strpos($wash_data, "ERROR") !== false) {//命令错误
            return self::returnData(self::$AtInstruction,['instruct'=>$command_param['instruct'],"msg"=>"命令错误"],1001);
        }

        if ($command_param["type"]=="AtGet"){
            $instruct= substr($command_param['instruct'], strlen("AT+"));
            if (strpos($wash_data, $instruct) === false) { //发送的命令未对应
                RedisServices::addExecuteList($code, self::$AtInstruction, $command_param);
                return false;
            }
            $arr=explode(":",trim($noSpecialSpaceStr));
            $arr=explode(" ",$arr[1]);
            return self::returnData(self::$AtInstruction,['instruct'=>$command_param['instruct'],"type"=>"AtGet",'data'=>$arr[0]]);
        }

        if ($command_param["type"]=="AtSet"){
            return self::returnData(self::$AtInstruction,['instruct'=>$command_param['instruct'],"type"=>"AtSet"]);
        }
        if ($command_param["type"]=="At"){
            return self::returnData(self::$AtInstruction,['instruct'=>$command_param['instruct'],'type'=>"At"]);
        }
    }

    /**
     * 解析at指令
     * @param $code
     * @param $wash_data
     * @param $command_param
     * @param $gateway
     * @return array|true
     */
    public static function parsingCustomize($code,$wash_data,$command_param,$gateway){
        $wash_data=hex2bin($wash_data);
        $noSpecialSpaceStr = preg_replace('/\s+/', ' ', $wash_data); // 将所有特殊空格替换为一个普通空格
        if (strpos($wash_data, "error") !== false) {//命令错误
            return self::returnData(self::$Customize,['command'=>$command_param['command'],"msg"=>"命令错误"],1001);
        }
        $noSpecialSpaceStr=trim($noSpecialSpaceStr);
        var_dump("设备".self::$Customize."---".$noSpecialSpaceStr);
        if ( $command_param["function_code"]== "doout") {
            if (strpos($noSpecialSpaceStr, $command_param["function_code"]) !== false) {
                if (strpos($command_param["command"], "get") !== false) {
                    $arr=explode(",",$noSpecialSpaceStr);
                    $value=$arr[3];
                    DeviceSubordinateVariableServices::saveVariableValue($command_param["variable_id"],$value);
                    RedisQueue::queue('iots_redis_queue_variable_log', ['gateway_id' => $gateway['id'], 'code' => $code,
                        'subordinate_address' => "", 'function_code' => "do",
                        'variable_id' => $command_param["variable_id"], 'value' => $value]);
                    return self::returnData(self::$Customize,['command'=>$command_param['command'],"val"=>$value]);
                }

                if (strpos($command_param["command"], "set") !== false) {
                    return self::returnData(self::$Customize,['command'=>$command_param['command'],"val"=>$noSpecialSpaceStr]);
                }
            }
        }
        if ( $command_param["function_code"]== "diinext") {
            if (strpos($noSpecialSpaceStr, $command_param["function_code"]) !== false) {
                return self::returnData(self::$Customize,['command'=>$command_param['command'],"val"=>$noSpecialSpaceStr]);
            }
        }
        return true;
    }

    /**
     * 如果返回值中包含 16进制注册包则删除
     * @param $data
     * @param $code
     * @return array|string|string[]
     */
    public static function wash($message, $code)
    {

        $check = self::checkFilter($code, $message);
        if (!$check) {//过滤数据
            return ['status' => false, "data" => $message];
        }
        $arr = [$code, bin2hex($code)];
        foreach ($arr as $v) {
            $str = str_replace($v, '', $message);
        }
        if (empty($str)) {//可能是心跳
            return ['status' => false, "data" => $message];
        }
        return ['status' => true, "data" => $str];
    }


    /**
     * Undocumented function 数据过滤
     *
     * @param [type] $my 设备信息
     * @param [type] $message 设备code
     * @return integer 1通过过滤,0未通过
     */
    public static function checkFilter($myKey, $message)
    {

        //是否需要过滤
        $filter = RedisServices::getGatewayFilterRedis($myKey);
        $ok = 1; //1通过过滤,0未通过
        //需要过滤
        if ($filter && $filter['is'] == 1) {
            //字符长度
            if (in_array(0, $filter['type'])) {
                switch ($filter['lengType']) {
                    case '1': // >
                        if (strlen($message) <= $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    case '2': // ＜
                        if (strlen($message) >= $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    case '3': // ＝
                        if (strlen($message) != $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    case '4': // ≥
                        if (strlen($message) < $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    case '5': // ≤
                        if (strlen($message) > $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    default:
                        # code...
                        break;
                }
            }
            //前N位
            if (in_array(1, $filter['type'])) {
                if (!in_array(substr($message, 0, $filter['before']), explode(',', $filter['beforeVal']))) {
                    $ok = 0;
                }
            }
            //忽略心跳包
            if (in_array(2, $filter['type'])) {
                if (in_array($message, explode(',', $filter['heartVal']))) {
                    $ok = 0;
                }
            }
        }
        return $ok;
    }


    public static function isAscii($char)
    {
        return ord($char) >= 0 && ord($char) <= 127;
    }

    /**
     * 返回命令执行结果 AT指令，ModbusRTUS命令，websocket发送命令
     * @param $type
     * @param $code
     * @param $data
     * @return array
     */
    public static function returnData($type,$data,$code=200){
        return ["type"=>$type,"code"=>$code,"data"=>$data];
    }

}