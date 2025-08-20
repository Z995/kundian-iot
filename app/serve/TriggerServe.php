<?php
/**
 * // 坤典智慧农场V6
 * // @link https://www.cqkd.com
 * // @description 软件开发团队为 重庆坤典科技有限公司
 * // @description The software development team is Chongqing Kundian Technology Co., Ltd.
 * // @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * // @description 软件版权归 重庆坤典科技有限公司 所有
 * // @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * // @description 文件路径与名称: widsomFarmV6_admin/src/page/miniapp/homestay/set.vue
 * // @description File path and name:   widsomFarmV6_admin/src/page/miniapp/homestay/set.vue
 * // @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * // @description This file is licensed to 重庆坤典科技-www.cqkd.com
 * // @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * // @warning This is not a free software, please get the license before use.
 * // @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * // @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility
 * // @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * // @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 */

namespace app\serve;

use app\services\alarm\AlarmLinkageLogServices;
use app\services\alarm\AlarmWarningLogServices;
use app\services\device\DeviceSubordinateVariableServices;
use plugin\webman\gateway\servers\SortingDataServices;
use think\Exception;
use think\exception\ValidateException;

class TriggerServe
{
    /**
     * 验证触发条件
     * @param $condition
     * @param $condition_parameter
     * @return void
     */
    public function verification($condition, $condition_parameter)
    {
        switch ($condition) {
            case 6;
            case 2;
                if (empty($condition_parameter["A"])) {
                    throw new ValidateException("A不能为空");
                }
                break;
            case 3;
                if (empty($condition_parameter["B"])) {
                    throw new ValidateException("B不能为空");
                }
                break;
            case 4;
            case 5;
                if (empty($condition_parameter["B"]) || empty($condition_parameter["A"])) {
                    throw new ValidateException("A|B不能为空");
                }
                break;

        }
    }


    /**
     * 是否执行
     * @param $trigger
     * @param $value
     * @return array
     */
    public static function warning($trigger, $value)
    {

        $is_warning = 0; //不报警
        $msg="";
        $condition_parameter=$trigger["condition_parameter"];
        switch ($trigger["condition"]) {
            case 0;

                if ($value == 0) {
                    $is_warning = 1;
                    $msg="开关off";
                }
                break;
            case 1;
                if ($value == 1) {
                    $is_warning = 1;
                    $msg="开关on";
                }
                break;
            case 2;
                if ($value < $condition_parameter["A"]) {
                    $is_warning = 1;
                    $msg="数值小于".$condition_parameter["A"];
                }
                break;
            case 3;
                if ($value >$condition_parameter["B"]) {
                    $is_warning = 1;
                    $msg="数值大于".$condition_parameter["B"];
                }
                break;
            case 4;
                if ($value > $condition_parameter["A"] && $value < $condition_parameter["B"]) {
                    $is_warning = 1;
                    $msg="数值大于".$condition_parameter["A"]."且小于".$condition_parameter["B"];

                }
                break;
            case 5;
                if ($value < $condition_parameter["A"] || $value > $condition_parameter["B"]) {
                    $is_warning = 1;
                    $msg="数值小于".$condition_parameter["A"]."或大于".$condition_parameter["B"];
                }
                break;
            case 6;
                if ($value == $condition_parameter["A"]) {
                    $is_warning = 1;
                    $msg='数值等于'.$condition_parameter["A"];
                }
                break;
        }
        return [$is_warning,$msg];
    }

    /**
     * 是否恢复
     * @param $trigger
     * @param $value
     * @param $dead_zone
     * @return int
     */
    public static function recover($trigger, $value)
    {
        $is_recover = 0;
        $dead_zone = $trigger["dead_zone"];
        $condition_parameter=$trigger["condition_parameter"];
        switch ($trigger["condition"]) {
            case 0;
                if ($value == 1) {
                    $is_recover = 1;
                }
                break;
            case 1;
                if ($value == 0) {
                    $is_recover = 1;
                }
                break;
            case 2;
                if ($value > ($condition_parameter["A"] - $dead_zone)) {
                    $is_recover = 1;
                }
                break;
            case 3;
                if ($value < ($condition_parameter["B"] - $dead_zone)) {
                    $is_recover = 1;
                }
                break;
            case 4;
                if ($value < ($condition_parameter["A"] - $dead_zone) && $value > ($condition_parameter["B"] - $dead_zone)) {
                    $is_recover = 1;
                }
                break;
            case 5;
                if ($value > ($condition_parameter["A"] - $dead_zone) || $value < ($condition_parameter["B"] - $dead_zone)) {
                    $is_recover = 1;
                }
                break;
            case 6;
                if ($value != $condition_parameter["A"]) {
                    $is_recover = 1;
                }
                break;
        }
        return $is_recover;
    }

    /**
     * 触发器
     * @param $admin_id
     * @param $trigger
     * @param $variable_id
     * @param $value
     * @return void
     * @throws \Exception
     */
    public static function trigger($trigger,$variable_id,$value,$type)
    {
        $DeviceSubordinateVariableServices=new DeviceSubordinateVariableServices();
        $variable=$DeviceSubordinateVariableServices->get(["id"=>$variable_id],[],["device","subordinate.subordinate"]);
        $variable_is_warning=$variable["is_warning"];
        foreach ($trigger as $v){
            $admin_id=$v["admin_id"];
            if ($v["is_alarm"]==1&&!empty($admin_id)){ //是否报警
                if ($variable_is_warning==0){
                    [$is_warning,$msg]=self::warning($v,$value);
                    if ($is_warning){//是否警报
                        TextProtocolServices::sendMsg($admin_id,json_encode(["type"=>SortingDataServices::$Alarm,"code"=>200,"msg"=>$v["alarm_push"],"data"=>[]]));
                        $DeviceSubordinateVariableServices->update($variable_id,["is_warning"=>1]);
                        (new AlarmWarningLogServices())->saveWarningLog($v,$variable,$msg,$value,$type,1);
                    }
                }else{
                    if (self::recover($v,$value)){//是否恢复
                        TextProtocolServices::sendMsg($admin_id,json_encode(["type"=>SortingDataServices::$Alarm,"code"=>200,"msg"=>$v["alarm_push"],"data"=>[]]));
                        $DeviceSubordinateVariableServices->update($variable_id,["is_warning"=>0]);
                        (new AlarmWarningLogServices())->saveWarningLog($v,$variable,"",$value,$type,0,1);
                    }
                }
            }
            if ($v["is_linkage"]==1){
                [$is_warning,$msg]=self::warning($v,$value);
                if (!$is_warning){//是否警报
                    continue;
                }
                $linkage_variable=$DeviceSubordinateVariableServices->getVariableInfo($v["linkage_subordinate_variable_id"]);
                (new AlarmLinkageLogServices())->saveLinkageLog($v,$variable,$msg,$linkage_variable,$type);
                if ($v["linkage_type"]==1){//采集
                    $DeviceSubordinateVariableServices->createDeviceCommand($linkage_variable);
                }else{//控制
                    $number=$v['control_type']==1?$v['number']:$value;
                    $DeviceSubordinateVariableServices->createDeviceCommand($linkage_variable,"write",$number);
                }
            }
        }
    }
}
