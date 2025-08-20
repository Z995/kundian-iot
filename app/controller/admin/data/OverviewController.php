<?php


namespace app\controller\admin\data;

use app\services\alarm\AlarmLinkageLogServices;
use app\services\alarm\AlarmWarningLogServices;
use app\services\device\DeviceOnlineLogServices;
use app\services\device\DeviceServices;
use app\services\gateway\GatewayOnlineLogServices;
use app\services\gateway\GatewayServices;
use app\services\monitor\MonitorServices;
use plugin\kundian\base\BaseController;



class OverviewController extends BaseController
{
    public function generalSituation(){
        $start_time=date('Y-m-d 00:00:00');
        $end_time=date('Y-m-d 23:59:59');
        $device_count=(new DeviceServices())->count(["admin_id"=>$this->adminId(),"is_del"=>0]);
        $gateway_count=(new GatewayServices())->count(["admin_id"=>$this->adminId(),"del"=>0]);
        $monitor_count=(new MonitorServices())->count(["admin_id"=>$this->adminId(),"is_del"=>0]);
        $warning_count=(new AlarmWarningLogServices())->count(["admin_id"=>$this->adminId(),"start_time"=>$start_time,"end_time"=>$end_time]);
        $linkageLog_count=(new AlarmLinkageLogServices())->count(["admin_id"=>$this->adminId(),"start_time"=>$start_time,"end_time"=>$end_time]);
        return success(["device_count"=>$device_count,"gateway_count"=>$gateway_count,"monitor_count"=>$monitor_count,"warning_count"=>$warning_count,"linkageLog_count"=>$linkageLog_count]);
    }
}
