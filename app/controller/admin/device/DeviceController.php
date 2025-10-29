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
use app\services\monitor\MonitorServices;
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
        $result["monitor"] = (new MonitorServices())->getMonitorByIds($result["monitor_ids"]);
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
            ["monitor_ids", ""],
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


}
