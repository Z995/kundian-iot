<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-01-28 15:13:57
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-04-01 19:44:10
 * @FilePath: \yunxiao-webman\app\controller\IndexController.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\controller\admin\device;

use app\model\device\Device;
use app\serve\RedisServices;
use app\services\alarm\AlarmIndependenceTriggerServices;
use app\services\alarm\AlarmTemplateTriggerServices;
use app\services\device\DeviceServices;
use app\services\device\DeviceSubordinateServices;
use app\services\device\DeviceSubordinateVariableLogServices;
use app\services\device\DeviceSubordinateVariableServices;
use app\services\label\LabelServices;
use plugin\kundian\base\BaseController;
use plugin\webman\gateway\servers\TimeServices;


class DeviceController extends BaseController
{

    /**
     * 设备列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDeviceList()
    {
        $param = getMore([
            ["name", ""],
            ["device_status", ""],
            ["template_id", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $param["is_del"] = 0;
        $result = (new DeviceServices())->getList($param, ['template',"gateway","subordinate"=>["variable","subordinate"]]);
        return success($result);
    }

    /**
     * 设备详情
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDeviceInfo()
    {
        $param = getMore([
            ["id", ""],
        ]);
        $deviceStatus=new Device();
        $count=$deviceStatus->where(["is_del"=>0])->count();
        $deviceStatus->updateDeviceStatus(1,$count);
        $param["admin_id"] = $this->adminId();
        $result = (new DeviceServices())->get($param, [], ["template", 'subordinate.subordinate']);
        $result["label"] = (new LabelServices())->getLabelByIds($result["label_ids"]);
        return success($result);
    }

    /**
     * 保存设备
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveDevice()
    {
        $param = getMore([
            ["id", ""],
            ["gateway_id", ""],
            ["template_id", ""],
            ["name", ""],
            ["code", ""],
            ["describe", ""],
            ["label_ids", ""],
            ["location", ""],
            ["longitude", ""],
            ["latitude", ""],
            ["subordinate", ""],
        ]);
        (new DeviceServices())->saveDevice($param, $this->adminId());
        return success();
    }

    /**
     * 删除设备
     * @return \support\Response
     */
    public function delDevice()
    {
        $param = getMore([
            ["id", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $DeviceServices = new DeviceServices();
        $result = $DeviceServices->get($param);
        if (!$result) {
            return fail("数据不存在");
        }
        $DeviceServices->getModel()->transaction(function ()use ($param,$result,$DeviceServices){
            $DeviceServices->update($param["id"], ["is_del" => 1]);
            (new DeviceSubordinateServices())->update(["device_id"=>$param["id"]],["is_del" => 1]);
            (new DeviceSubordinateVariableServices())->update(["device_id"=>$param["id"]],["is_del" => 1]);
            RedisServices::againRedisTime($result["code"], false);
        });
        return success();
    }

    /**
     * 获取设备列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getVariable()
    {
        $param = getMore([
            ["subordinate_id", ""],
            ["device_id", ""],
            ["name", ""],
            ["type", ""],
        ]);
        $param["admin_id"] = $this->getAdminInfo();
        $param["is_del"] = 0;
        $result = (new DeviceSubordinateVariableServices())->getList($param,["templateVariable"],"*","sort desc");
        return success($result);
    }

    /**
     * 获取设备数据
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDeviceData()
    {
        $param = getMore([
            ["variable_id", 0],
        ]);
        (new DeviceSubordinateVariableServices())->createDeviceCommand((int)$param['variable_id']);
        return success();
    }
    /**
     * 获取设备数据
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setDeviceData()
    {
        $param = getMore([
            ["variable_id", 0],
            ["value", 0],
        ]);
        (new DeviceSubordinateVariableServices())->createDeviceCommand((int)$param['variable_id'],"write",$param["value"]);
        return success();
    }

    /**
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getVariableLog(){
        $param = getMore([
            ["variable_id", 0],
        ]);

        $result=(new DeviceSubordinateVariableLogServices())->getList(["variable_id"=>$param['variable_id']]);
        return success($result);
    }
    /**
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStatisticalChart(){
        $param = getMore([
            ["ids",""],
            ["start_time", ""],
            ["end_time",""],
        ]);
        $result=(new DeviceSubordinateVariableLogServices())->getStatisticalChart($param);
        return success($result);
    }

    public function test(){

        $frame1="030304bf0709fc4bf7";
//        (new AlarmTemplateTriggerServices())->trigger(1,44,29,501);
        (new AlarmIndependenceTriggerServices())->trigger(1,44,501);
//        $result1 = (new ModbusRTUServices())->parseFrame($frame1, 'bit',["bitPosition"=>7]);
        return success();

    }
}
