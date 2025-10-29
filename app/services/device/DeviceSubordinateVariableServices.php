<?php

/**
 * 坤典智慧农场V6
 * @link https://www.cqkd.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing KunDian Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing KunDian Technology Co., Ltd.
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-www.cqkd.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 */

namespace app\services\device;

use app\model\Admin;
use app\model\device\Device;
use app\model\device\DeviceSubordinate;
use app\model\device\DeviceSubordinateVariable;
use app\model\device\DeviceTemplateSubordinate;
use app\serve\ModbusRTUServices;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use plugin\webman\gateway\Events;
use plugin\webman\gateway\servers\SortingDataServices;
use support\Redis;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use think\exception\ValidateException;

class DeviceSubordinateVariableServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return DeviceSubordinateVariable::class;
    }


    /**
     * 保存变量
     * @param $variable
     * @param $subordinate_id
     * @param $device_id
     * @param $admin_id
     * @return void
     */
    public function saveVariable($variable, $subordinate_id, $device_id, $admin_id)
    {
        $ids = [];
        foreach ($variable as $v_v) {
            $result = $this->getOne(["template_subordinate_variable_id" => $v_v["id"], "subordinate_id" => $subordinate_id, "admin_id" => $admin_id, "is_del" => 0]);
            $data = ["name" => $v_v['name'], "type" => $v_v["type"], "sort" => $v_v["sort"]];
            if ($result) {
                $ids[] = $result["id"];
                $this->update($result["id"], $data);
                self::delRedisVariableInfo($result["id"]);
            } else {
                $date = date("Y-m-d H:i:s");
                $data['template_subordinate_variable_id'] = $v_v["id"];
                $data['subordinate_id'] = $subordinate_id;
                $data['device_id'] = $device_id;
                $data['admin_id'] = $admin_id;
                $data['create_time'] = $date;
                $data['update_time'] = $date;
                $all[] = $data;
            }
        }
        if (!empty($ids)) {
            $this->search(["not_ids" => $ids, "subordinate_id" => $subordinate_id])->update(['is_del' => 1]);
        }
        if (!empty($all)) {
            $this->saveAll($all);
        }
    }

    /**
     * 通过缓存获取详情
     * @param $variable_id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getRedisVariableInfo($variable_id){
        $key= "VariableInfo:" . $variable_id;
        $variable=Redis::get($key);
        if (empty($variable)){
            $variable =(new self())->getOne(["id" => $variable_id], '*', ['templateVariable', "variableLog","device.gateway","subordinate.subordinate"]);
            $expire=rand(30000, 35000);//防止大面积同一时间过期
            if (!empty($variable)&&!empty($variable["device"])) {
                $variable=$variable->toArray();
            }else{
                $variable=[];
            }
            Redis::set($key,json_encode($variable),"Ex", $expire);
        }else{
            $variable=json_decode($variable,true);
        }
        return $variable;
    }

    /**
     * 删除缓存
     * @param $variable_id
     * @return void
     */
    public static function delRedisVariableInfo($variable_id){
        $key= "VariableInfo:" . $variable_id;
        if (Redis::exists($key)){
            Redis::Del($key);
        }
    }

    /**
     * 删除不属于改从机的变量
     * @param $subordinate_ids
     * @param $template_id
     * @return void
     */
    public function delVariableByNotSubordinateIds($subordinate_ids, $device_id)
    {
        $this->search(["not_subordinate_ids" => $subordinate_ids, "device_id" => $device_id])->update(['is_del' => 1]);
    }

    /**
     * 删除
     * @param $subordinate_ids
     * @param $template_id
     * @return void
     */
    public function delVariableBySubordinateIds($subordinate_ids, $device_id)
    {
        $this->search(["subordinate_ids" => $subordinate_ids, "device_id" => $device_id])->update(['is_del' => 1]);
    }


    public static function getVariableByDeviceId($device_id)
    {
        return (new self())->getSelect(["device_id" => $device_id], ['templateVariable']);
    }

    public function getVariableInfo($variable_id)
    {
//        $variable = $this->getOne(["id" => $variable_id], "*", ['device.gateway', "subordinate.subordinate", "templateVariable"]);
        $variable = $this->getRedisVariableInfo($variable_id);
        if (!$variable) {
            throw new  ValidateException("设备不存在");
        }
        if (empty($variable["device"]["gateway"]["code"])) {
            throw new  ValidateException("网关code不存在");
        }
        if (empty($variable["subordinate"]["subordinate_address"])) {
            throw new  ValidateException("从机地址不存在");
        }
        if (empty($variable["subordinate"]["subordinate"]["protocol"])) {
            throw new  ValidateException("从机协议错误");
        }
        if (empty($variable["templateVariable"]["register_address"])) {
            throw new  ValidateException("寄存器地址不存在");
        }
        if (!in_array($variable["templateVariable"]["register_mark"], [0, 1, 3, 4,"do","di"])) {
            throw new  ValidateException("寄存器不存在");
        }
        return $variable;
    }

    /**
     * 生成命令
     * @param $variable_id
     * @param $type
     * @param $value
     * @return false|void
     */
    public function createDeviceCommand($variable_id, $type = "read", $value = "")
    {
        if (is_int($variable_id)){
            $variable = $this->getVariableInfo($variable_id);
        }else{
            $variable = $variable_id;
        }
        //判断设备是否在线
        $online = Events::isOnlineByUid($variable["device"]["gateway"]["code"]);
        if (!$online) {
            return false;
        }
        $protocol= $variable["subordinate"]["subordinate"]["protocol"];
        [$command,$function_code,$command_type]= self::generateCommands($protocol,$variable["id"], $variable["templateVariable"],$variable["subordinate"]["subordinate_address"],$type,$value);

        RedisServices::addExecuteList($variable["device"]["gateway"]["code"],$command_type, ["command" => $command, "function_code" => $function_code, "variable_id" => $variable["id"], "subordinate_id" => $variable["subordinate_id"], "template" => $variable["templateVariable"]]);
    }

    /**
     * 控制表达式
     * @param $templateVariable
     * @param $value
     * @return mixed
     */
    public static function controFormula($templateVariable, $value)
    {
        if (!empty($templateVariable["contro_formula"])) { //表达式
            try {
                $collect_formula = $templateVariable["contro_formula"];
                $collect_formula = str_replace("%s", $value, $collect_formula);
                $formula_value = (new ExpressionLanguage())->evaluate($collect_formula);
            } catch (\Throwable $Throwable) {
            }
            if (!empty($formula_value)) {
                $value = $formula_value;
            }
        }
        return $value;
    }

    /**
     * 保存value值
     * @param $variable_id
     * @param $data
     * @return void
     */
    public static function saveVariableValue($variable_id, $value)
    {
        (new self())->update($variable_id, ['value' => $value, 'last_time' => date("Y-m-d H:i:s")]);
    }

    /**
     * 生成命令
     * @param $protocol
     * @param $variable_id
     * @param $templateVariable
     * @param $subordinate_address
     * @param $type
     * @param $value
     * @return array
     */
    public static function generateCommands($protocol,$variable_id, $templateVariable, $subordinate_address, $type = "read", $value = "")
    {
        switch ($protocol) {
            case 1://ModbusRTU命令
                $result = self::generateModbusRTU($variable_id, $templateVariable,$subordinate_address, $type, $value);
                break;
            case 2://自定义指令
                $result = self::generateCustomize( $templateVariable,$value,$type);
                break;
            default:
                throw new ValidateException('协议错误');
        }
        return $result;
    }

    /**
     * 生成ModbusRTU命令
     * @param $variable_id
     * @param $templateVariable
     * @param $subordinate_address
     * @param $type
     * @param $value
     * @return array
     */
    public static function generateModbusRTU($variable_id,$templateVariable, $subordinate_address, $type = "read", $value = "")
    {
        $register_mark=(int)$templateVariable["register_mark"];
        [$function_code, $startAddress] = ModbusRTUServices::getFunctionCode($register_mark,$templateVariable["register_address"], $type);
        if ($type == "read") {
            $parameter=ModbusRTUServices::getModbusParameter($templateVariable);
            $command = ModbusRTUServices::buildReadRegistersCommand((int)$subordinate_address, $function_code, $startAddress,$parameter["quantity"]);
        } else {//write
            if (in_array($register_mark, [0, 1])) {
                if (!in_array($value, [1, 0])) {
                    throw new ValidateException("05功能码值为true或false");
                }
                $command = ModbusRTUServices::writeSingleCoil((int)$subordinate_address, (int)$startAddress, (bool)$value);
            }
            if (in_array($register_mark, [3, 4])) {
                $value = self::controFormula($templateVariable, $value);
                $command = ModbusRTUServices::writeSingleRegister((int)$subordinate_address, (int)$startAddress, $value);
            }
            (new self())->update($variable_id, ["value" => $value]);
        }
        if (empty($command)){
            throw new ValidateException("命令生成失败");
        }
        return [ $command, $function_code, SortingDataServices::$ModbusRTUS];
    }

    /**
     * 生成自定义指令
     * @param $templateVariable
     * @param $value
     * @return array
     * @throws \Exception
     */
    public static function generateCustomize($templateVariable,$value,$type="read"){
        $line_feed=myHexTobin("0d0a");//换行
        $register_mark="";
        if ($templateVariable["register_mark"]=="do"){
            $register_mark="doout";
            if ($type=="read"){
                $command="config,get,doout,".$templateVariable["register_address"].$line_feed;
            } else{
                $command="config,set,doout,".$templateVariable["register_address"].",".$value.$line_feed;
            }
        }
        if ($templateVariable["register_mark"]=="di"){
            $register_mark="diinext";
            $command="config,get,diinext,".$templateVariable["register_address"].$line_feed;
        }
        if (empty($command)){
            throw new ValidateException("命令生成失败");
        }
        return [ $command, $register_mark, SortingDataServices::$Customize];
    }


}