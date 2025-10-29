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
