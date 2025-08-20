<?php

namespace app\services\device;

use app\model\Admin;
use app\model\device\DeviceTemplate;
use app\model\device\DeviceTemplateSubordinateVariable;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class DeviceTemplateSubordinateVariableServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return DeviceTemplateSubordinateVariable::class;
    }

    /**
     * 保存变量
     * @param $variable
     * @param $template_id
     * @param $subordinate_id
     * @param $admin_id
     * @return void
     */
    public function saveVariable($variable,$template_id,$template_subordinate_id,$admin_id){
        $ids=[];
        foreach ($variable as $v_v){
            if (!empty($v_v["id"])){
                $result = $this->getOne(["id" => $v_v["id"], "admin_id" => $admin_id]);
                if (!$result) {
                    throw new ValidateException("变量不存在");
                }
                $this->update($v_v['id'],$v_v);
                $variable_id=$v_v['id'];
                $ids[]=$variable_id;
            }else{
                $v_v["template_id"]=$template_id;
                $v_v["template_subordinate_id"]=$template_subordinate_id;
                $v_v["admin_id"]=$admin_id;
                $all[]=$v_v;
            }
        }
        $this->search(["not_ids"=>$ids??[],"template_subordinate_id"=>$template_subordinate_id])->delete();
        if (!empty($all)){
            $this->saveAll($all);
        }
    }

    /**
     * 删除不属于改从机的变量
     * @param $subordinate_ids
     * @param $template_id
     * @return void
     */
    public function delVariableByNotSubordinateIds($subordinate_ids,$template_id){
        $this->search(["not_template_subordinate_ids"=>$subordinate_ids,"template_id"=>$template_id])->delete();
    }


    public function getTemplateSubordinateVariable($template_subordinate_id){
        return $this->getSelect(['template_subordinate_id'=>$template_subordinate_id]);
    }



}