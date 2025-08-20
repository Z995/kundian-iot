<?php

namespace app\services\alarm;

use app\model\Admin;
use app\model\alarm\AlarmLinkageLog;
use app\model\alarm\AlarmTemplateTrigger;
use app\model\alarm\AlarmWarningLog;
use app\model\device\Device;
use app\serve\RedisServices;
use app\serve\TextProtocolServices;
use app\serve\TriggerServe;
use app\services\device\DeviceSubordinateServices;
use app\services\device\DeviceSubordinateVariableServices;
use app\services\device\DeviceTemplateServices;
use plugin\kundian\base\BaseServices;
use plugin\webman\gateway\servers\SortingDataServices;
use think\exception\ValidateException;

class AlarmLinkageLogServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return AlarmLinkageLog::class;
    }


    public function saveLinkageLog($trigger,$variable,$trigger_condition,$trigger_linkage,$trigger_type){
        $data=["trigger_id"=>$trigger["id"],"admin_id"=>$trigger["admin_id"],"name"=>$variable["device"]["name"],"trigger_type"=>$trigger_type,
            "trigger_name"=>$trigger['name'],"subordinate_name"=>$variable["subordinate"]["subordinate"]["name"],"variable_name"=>$variable["name"],
            "trigger_condition"=>$trigger_condition,"trigger_device"=>$trigger_linkage['device']["name"],
            "device_id"=>$trigger_linkage['device_id'],"variable_id"=>$trigger_linkage['id']];
        $this->save($data);
    }


}