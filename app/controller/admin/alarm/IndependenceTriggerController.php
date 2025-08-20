<?php


namespace app\controller\admin\alarm;

use app\serve\TriggerServe;
use app\services\alarm\AlarmIndependenceTriggerServices;
use app\services\alarm\AlarmTemplateTriggerServices;
use plugin\kundian\base\BaseController;



class IndependenceTriggerController extends BaseController
{

    /**
     * 列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getIndependenceTriggerList(){
        $where=getMore([
            ["name",''],
            ["device_name",''],
        ]);
        $where["admin_id"]=$this->adminId();
        $result=(new AlarmIndependenceTriggerServices())->getList($where,["device","subordinate.subordinate",
            "subordinateVariable","linkageSubordinate.subordinate","linkageSubordinateVariableId"]);
        return success($result);
    }

    /**
     * 详情
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getIndependenceTriggerInfo(){
        $where=getMore([
            ["id",''],
        ]);
        $where["admin_id"]=$this->adminId();
        $result=(new AlarmIndependenceTriggerServices())->getOne($where,'*',["device","subordinate.subordinate",
            "subordinateVariable","linkageDevice","linkageSubordinate.subordinate","linkageSubordinateVariableId"]);
        return success($result);
    }


    /**
     * 添加修改
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveIndependenceTrigger(){
        $param=getMore([
            ["id",''],
            ["name",''],
            ["trigger_type",1],
            ["device_id",''],
            ["subordinate_id",''],
            ["subordinate_variable_id",''],
            ["condition",0],
            ["condition_parameter",""],
            ["dead_zone",0],
            ["is_alarm",0],
            ["alarm_push",""],
            ["resume_push",""],
            ["is_linkage",0],
            ["linkage_type",0],
            ["linkage_device_id",0],
            ["linkage_subordinate_id",0],
            ["linkage_subordinate_variable_id",0],
            ["control_type",0],
            ["number",0],
            ["status",0],
        ]);
        $param["admin_id"]=$this->adminId();
        (new TriggerServe())->verification($param["condition"],$param["condition_parameter"]);
        (new AlarmIndependenceTriggerServices())->saveData($param);
        return success();
    }

    /**
     * 修改状态
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveIndependenceTriggerStatus(){
        $param=getMore([
            ["id",''],
            ["index",''],
            ["value",''],
        ]);
        $data=["id"=>$param["id"],$param["index"]=>$param["value"]];
        (new AlarmIndependenceTriggerServices())->saveData($data);
        return success();
    }

    /**
     * 删除
     * @return \support\Response
     */
    public function delIndependenceTrigger(){
        $param=getMore([
            ["id",''],
        ]);
        $param["admin_id"]=$this->adminId();
        (new AlarmIndependenceTriggerServices())->delete($param);
        return success();
    }

}
