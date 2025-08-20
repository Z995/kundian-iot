<?php

namespace app\services\device;

use app\model\Admin;
use app\model\device\Device;
use app\serve\RedisServices;
use app\services\label\LabelServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class DeviceServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return Device::class;
    }

    /**
     * 保存设备
     * @param $param
     * @param $admin_id
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveDevice($param, $admin_id)
    {
        $this->getModel()->transaction(function () use ($param, $admin_id) {
            $template=(new DeviceTemplateServices())->get($param['template_id']);
            if (empty($template)){
                throw new ValidateException("模版不存在");
            }

            if (!empty($param["id"])) {
                $result = $this->getOne(["id" => $param["id"], "admin_id" => $admin_id]);
                if (!$result) {
                    throw new ValidateException("数据不存在");
                }
                $param["status"]=Device::getDeviceStatus($template,$param["gateway_id"],$param["id"]);
                $this->update($param["id"], $param);
                $device_id = $param["id"];
                $code = $result["code"];
            } else {
                $code=get_order_sn("KD",13);
                $param['code'] =$code;
                $param['admin_id'] = $admin_id;
                $param["status"]=Device::getDeviceStatus($template,$param["gateway_id"]);
                $save = $this->save($param);
                $device_id = $save["id"];
            }
            (new DeviceSubordinateServices())->saveDeviceSubordinate($param["subordinate"], $device_id, $admin_id);

            if (!empty($code)) {//设备从机发生修改 重新缓存 缓存包含从机数据
                RedisServices::againRedisTime($code);

            }
        });
    }


    /**
     * 当模版发生变化后修改对应设备
     * @param $template_id
     * @return true|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function synchronize($template_id)
    {
        $device = $this->getSelect(["template_id" => $template_id,"is_del"=>0],["gateway"]);
        if (!$device) {
            return true;
        }
        $template = (new DeviceTemplateServices())->getOne(["id" => $template_id], '*', ["subordinate.variable"]);
        if (!$template) {
            return true;
        }
        $DeviceSubordinateServices = new DeviceSubordinateServices();
        $DeviceSubordinateVariableServices = new DeviceSubordinateVariableServices();
        $code_arr=[];
        foreach ($device as $v) {
            $this->synchronizeTemplate($v, $template, $DeviceSubordinateServices, $DeviceSubordinateVariableServices);
            if (!empty($v["gateway"])&&!in_array($v["gateway"]['code'],$code_arr)){
                $code_arr[]=$v["gateway"]['code'];
                RedisServices::againRedisTime($v["gateway"]['code']);
            }
        }
    }


    /**
     * 同步模版的改变
     * @param $device
     * @param $template
     * @param DeviceSubordinateServices $DeviceSubordinateServices
     * @param DeviceSubordinateVariableServices $DeviceSubordinateVariableServices
     * @return void
     */
    public function synchronizeTemplate($device, $template, $DeviceSubordinateServices, $DeviceSubordinateVariableServices)
    {
        $admin_id = $device['admin_id'];
        if (empty($template['subordinate'])) {
            //删除
            $DeviceSubordinateServices->search(["device_id" => $device["id"]])->update(['is_del'=>1]);
            $DeviceSubordinateVariableServices->search(["device_id" => $device["id"]])->update(['is_del'=>1]);
        }
        $template_subordinate_ids=[];
        foreach ($template['subordinate'] as $vo) {
            $template_subordinate_ids[] = $vo["id"];
            $subordinate = $DeviceSubordinateServices->getOne(["device_id" => $device["id"], "admin_id" => $admin_id, "template_subordinate_id" => $vo["id"]]);
            if (!$subordinate) {
                $subordinate = $DeviceSubordinateServices->save(["admin_id" => $admin_id, "device_id" => $device["id"], "template_subordinate_id" => $vo["id"]]);
            }
            $DeviceSubordinateVariableServices->saveVariable($vo["variable"], $subordinate["id"], $device["id"], $admin_id);
        }
        $DeviceSubordinateServices->deleteByTemplate($device["id"], $template_subordinate_ids, $DeviceSubordinateVariableServices);
    }

    /**
     * 获取设备网关
     * @param $device_id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getDeviceByGatewayId($gateway_id)
    {
        return (new self())->getSelect(['gateway_id' => $gateway_id], ['subordinate'=>["subordinate","variable.templateVariable"]]);
    }

    /**
     * 设备状态统计
     * @param $admin_id
     * @return array
     */
    public function deviceStatusStatistics($admin_id){
        $online=$this->count(["admin_id"=>$admin_id,"status"=>1,"is_warning"=>0]);
        $not_online=$this->count(["admin_id"=>$admin_id,"status"=>0,"is_warning"=>0]);
        $warning=$this->count(["admin_id"=>$admin_id,"is_warning"=>1]);
        return compact("online","not_online","warning");
    }

    /**
     * 设备标签统计
     * @param $admin_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deviceLabelStatistics($admin_id){
        $label=(new LabelServices())->getSelect(["admin_id"=>$admin_id]);
        foreach ($label as &$v){
            $v["count"]=$this->count(["admin_id"=>$admin_id,"label_ids"=>$v["id"]]);
        }
        return $label;
    }

    /**
     * 新设备统计
     * @param $admin_id
     * @param $type
     * @return array
     */
    public function newDeviceStatistics($admin_id,$type=1){
        if ($type==1){
            $time=getDailyTimeRanges(7);
        }else{
            $time=getDailyTimeRanges(30);
        }
        $result=[];
        foreach ($time as $v){
            $count=$this->count(["admin_id"=>$admin_id,"start_time"=>$v["start_time"],"end_time"=>$v["end_time"]]);
            $result[]=["date"=>$v["date"],"count"=>$count];
        }
        return $result;
    }

}