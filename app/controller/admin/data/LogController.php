<?php


namespace app\controller\admin\data;

use app\services\alarm\AlarmLinkageLogServices;
use app\services\alarm\AlarmWarningLogServices;
use app\services\device\DeviceOnlineLogServices;
use app\services\gateway\GatewayOnlineLogServices;
use plugin\kundian\base\BaseController;



class LogController extends BaseController
{

    public function gatewayOnlineLog(){
        $param = getMore([
            ["gateway_id", ""],
            ["start_time", ""],
            ["end_time", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new GatewayOnlineLogServices())->getList($param);
        return success($result);
    }
    public function deviceOnlineLog(){
        $param = getMore([
            ["device_id", ""],
            ["start_time", ""],
            ["end_time", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new DeviceOnlineLogServices())->getList($param);
        return success($result);
    }
    public function warningLog(){
        $param = getMore([
            ["device_id", ""],
            ["variable_id", ""],
            ["is_warning", ""],
            ["status", ""],
            ["start_time", ""],
            ["end_time", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new AlarmWarningLogServices())->getList($param);
        return success($result);
    }

    /**
     * 处理报警
     * @return \support\Response
     */
    public function dealWarningLog(){
        $param = getMore([
            ["id", ""],
        ]);
        $param["admin_id"] = $this->adminId();
         (new AlarmWarningLogServices())->update($param,["status"=>1]);
        return success();
    }
    /**
     * 处理报警
     * @return \support\Response
     */
    public function delWarningLog(){
        $param = getMore([
            ["id", ""],
        ]);
        $param["admin_id"] = $this->adminId();
         (new AlarmWarningLogServices())->delete($param);
        return success();
    }

    public function linkageLog(){
        $param = getMore([
            ["device_id", ""],
            ["variable_id", ""],
            ["start_time", ""],
            ["end_time", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new AlarmLinkageLogServices())->getList($param);
        return success($result);
    }
    public function delLinkageLog(){
        $param = getMore([
            ["id", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new AlarmLinkageLogServices())->delete($param);
        return success($result);
    }

}
