<?php

namespace app\services\monitor;

use app\model\Admin;
use app\model\device\Device;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayInstruct;
use app\model\label\Label;
use app\model\monitor\Monitor;
use app\model\monitor\MonitorAuto;
use app\model\product\DeviceProduct;
use app\serve\monitor\KunDianMonitorServices;
use app\serve\RedisServices;
use app\services\device\DeviceServices;
use app\services\device\DeviceSubordinateServices;
use app\services\device\DeviceTemplateServices;
use app\services\product\DeviceProductVariableServices;
use extend\RedisQueue;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

/**
 * Class MonitorServices
 * @mixin Monitor
 */
class MonitorAutoServices extends BaseServices
{
    public  $week_arr=[1=>"monday",2=>"tuesday",3=>"wednesday",4=>"thursday",5=>"friday",6=>"saturday",7=>"sunday"];
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return MonitorAuto::class;
    }

    /**
     * 执行自动截图，录像
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function executeAuto(){
        $res = $this->getSelect(["status"=>1]);
        foreach ($res as $v) {
            $is_execute=$this->getDelay($v);
            if ($is_execute){
                RedisQueue::queue('iots_redis_queue_auto_work',$v);
            }
        }
    }

    /**
     * 保存配置
     * @param $param
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveMonitorAuto($param){

        $auto=$this->get(["monitor_id"=>$param["monitor_id"],"channels"=>$param["channels"],"type"=>$param["type"]]);
        if ($auto){
            $this->update($auto["id"],$param);
        }else{
            $this->save($param);
        }
    }


    public function getDelay($res)
    {
        $opType = $res['op_type'];
        $opValue = $res['op_value'];
        $times = explode(',',$opValue);
        $day = $times[0]; // 间隔几天
        $time = $times[1]; // 具体时间
        if (empty($res["last_time"])){
            return true;
        }
        if ($opType == 1) {//每天
            $delay=strtotime(date("Y-m-d",$res["last_time"]))+($day*86400);
            $delay=strtotime(date('Y-m-d',$delay).' '.$time.':00');
            if ($delay<time()){
                return true;
            }
        }
        if ($opType == 2) {  // 周
            if (!empty($this->week_arr[$day])){
                $week = $this->week_arr[$day];
                $delay=strtotime(date('Y-m-d',strtotime($week)).' '.$time.':00');
                if ($delay<time()){
                    return true;
                }
            }
        }
        if ($opType == 3) { // 时间点
            $delay = strtotime($opValue);
            if ($delay<time()){
                return true;
            }
        }
        return false;
    }


    /***
     * 执行
     * @param $auto
     * @param MonitorServices $MonitorServices
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function autoWorkExecute($auto,MonitorServices $MonitorServices){
        if ($auto["type"]==1){
            $MonitorServices->snap($auto["monitor_id"],$auto["channels"]);
        }else{
            $MonitorServices->startRecording(["id"=>$auto["monitor_id"],"channels"=>$auto["channels"]]);
            sleep($auto["recording_duration"]);
            $MonitorServices->stopRecording(["id"=>$auto["monitor_id"],"channels"=>$auto["channels"]]);
        }
        $this->update($auto["id"],["last_time"=>time()]);
    }

}