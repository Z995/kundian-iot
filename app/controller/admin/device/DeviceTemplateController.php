<?php


namespace app\controller\admin\device;

use app\model\device\DeviceSubordinateVariableLog;
use app\services\device\DeviceTemplateServices;
use app\services\gateway\GatewayServices;
use app\validate\admin\GatewayValidate;
use app\validate\IdValidate;
use plugin\kundian\base\BaseController;


class DeviceTemplateController extends BaseController
{
    /**
     * 列表
     * @return \support\Response
     */
    public function getDeviceTemplateList(){
        $param=getMore([
            ["name",""]
        ]);
        $param["admin_id"]=$this->adminId();
        $result=(new DeviceTemplateServices())->getDeviceTemplateList($param);
        return success($result);
    }

    /**
     * 详情
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDeviceTemplateInfo(){
        $param=getMore([
            ["id",""]
        ]);
        $result=(new DeviceTemplateServices())->get($param["id"],[],["subordinate.variable"]);
        return success($result);
    }

    /**
     * 保存模版
     * @return \support\Response
     */
    public function saveDeviceTemplate(){
        $param=getMore([
            ["id",""],
            ["name",""],
            ["collect",1],
            ["status_config",1],
            ["space_time",3],
            ["subordinate",""],//从机 以及变量
        ]);
        (new DeviceTemplateServices())->saveDeviceTemplate($param,$this->adminId());
        return success();
    }

    /**
     * 删除
     * @return \support\Response
     */
    public function delDeviceTemplate(){
        $param=getMore([
            ["id",""]
        ]);
        (new DeviceTemplateServices())->delDeviceTemplate($param["id"],$this->adminId());
        return success();
    }

}
