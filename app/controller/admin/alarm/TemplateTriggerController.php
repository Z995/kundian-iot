<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-01-28 15:13:57
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-04-01 19:44:10
 * @FilePath: \yunxiao-webman\app\controller\IndexController.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\controller\admin\alarm;

use app\serve\TriggerServe;
use app\services\alarm\AlarmTemplateTriggerServices;
use plugin\kundian\base\BaseController;



class TemplateTriggerController extends BaseController
{

    /**
     * 列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTemplateTriggerList(){
        $where=getMore([
            ["name",''],
            ["template_name",''],
        ]);
        $where["admin_id"]=$this->adminId();
        $result=(new AlarmTemplateTriggerServices())->getList($where,["template"]);
        return success($result);
    }

    /**
     * 详情
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTemplateTriggerInfo(){
        $where=getMore([
            ["id",''],
        ]);
        $where["admin_id"]=$this->adminId();
        $result=(new AlarmTemplateTriggerServices())->getOne($where,'*',["template","templateSubordinate",
            "templateSubordinateVariable","linkageSubordinate","linkageSubordinateVariableId"]);
        return success($result);
    }


    /**
     * 添加修改
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveTemplateTrigger(){
        $param=getMore([
            ["id",''],
            ["name",''],
            ["template_id",''],
            ["template_subordinate_id",''],
            ["template_subordinate_variable_id",''],
            ["condition",0],
            ["condition_parameter",""],
            ["dead_zone",0],
            ["is_alarm",0],
            ["alarm_push",""],
            ["resume_push",""],
            ["is_linkage",0],
            ["linkage_type",0],
            ["linkage_subordinate_id",0],
            ["linkage_subordinate_variable_id",0],
            ["control_type",0],
            ["number",0],
        ]);
        $param["admin_id"]=$this->adminId();
        (new TriggerServe())->verification($param["condition"],$param["condition_parameter"]);
        (new AlarmTemplateTriggerServices())->saveData($param);
        return success();
    }

    /**
     * 修改状态
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveTemplateTriggerStatus(){
        $param=getMore([
            ["id",''],
            ["index",''],
            ["value",''],
        ]);
        $data=["id"=>$param["id"],$param["index"]=>$param["value"]];
        (new AlarmTemplateTriggerServices())->saveData($data);
        return success();
    }

    /**
     * 删除
     * @return \support\Response
     */
    public function delTemplateTrigger(){
        $param=getMore([
            ["id",''],
        ]);
        $param["admin_id"]=$this->adminId();
        (new AlarmTemplateTriggerServices())->delete($param);
        return success();
    }

}
