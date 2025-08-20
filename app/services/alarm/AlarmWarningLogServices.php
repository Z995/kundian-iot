<?php

namespace app\services\alarm;

use app\model\Admin;
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

class AlarmWarningLogServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return AlarmWarningLog::class;
    }


    public function saveWarningLog($trigger,$variable,$msg,$value,$trigger_type,$is_warning=0,$status=0){
        $data=["trigger_id"=>$trigger["id"],"device_id"=>$variable["device_id"],"admin_id"=>$trigger["admin_id"],"name"=>$variable["device"]["name"],"trigger_type"=>$trigger_type,
            "trigger_name"=>$trigger['name'],"trigger_condition"=>$msg,"subordinate_name"=>$variable["subordinate"]["subordinate"]["name"],
            "variable_name"=>$variable["name"],  "variable_id"=>$variable["id"], "is_warning"=>$is_warning,"status"=>$status,"value"=>$value];
        $this->save($data);
    }


    public function getDeviceWarningRecord($admin_id){
       $time= $this->getTime();
       $result=[];
       foreach ($time as $v){
           $where=['start_time'=>$v["start_time"],'end_time'=>$v["end_time"],'admin_id'=>$admin_id];
           $where['is_warning']=1;
           $count=$this->count($where);
           $where['status']=1;
           $disposed_count=$this->count($where);
           $where['status']=0;
           $undisposed_count=$this->count($where);
           $result[]=["day"=>$v["day"],"count"=>$count,"disposed_count"=>$disposed_count,"undisposed_count"=>$undisposed_count];
       }
       return $result;
    }

    /**
     * 获取时间
     * @return array[]
     */
    public function getTime(){
        $today = [
            'start_time' => date('Y-m-d 00:00:00'),
            'end_time'   => date('Y-m-d 23:59:59'),
            "day"=>"今天"
        ];

        // 最近7天范围（含今天）
        $sevenDays = [
            'start_time' => date('Y-m-d 00:00:00', strtotime('-6 days')),
            'end_time'   => $today['end_time'],
            "day"=>"最近7天"
        ];

        // 最近30天范围（含今天）
        $thirtyDays = [
            'start_time' => date('Y-m-d 00:00:00', strtotime('-29 days')),
            'end_time'   => $today['end_time'],
             "day"=>"最近30天"
        ];
        return [$today,$sevenDays,$thirtyDays];
    }

}