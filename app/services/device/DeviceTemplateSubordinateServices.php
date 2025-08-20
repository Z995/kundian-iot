<?php

namespace app\services\device;

use app\model\Admin;
use app\model\device\DeviceTemplate;
use app\model\device\DeviceTemplateSubordinate;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class DeviceTemplateSubordinateServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return DeviceTemplateSubordinate::class;
    }

    /**
     * 保存从机
     * @param $subordinate
     * @param $template_id
     * @param $admin_id
     * @return void
     */
    public function saveSubordinate($subordinate,$template_id,$admin_id){
        $ids=[];
        $DeviceTemplateSubordinateVariableServices=new DeviceTemplateSubordinateVariableServices();
        foreach ($subordinate as $v_s){
            if (!empty($v_s["id"])){
                $result = $this->getOne(["id" => $v_s["id"], "admin_id" => $admin_id]);
                if (!$result) {
                    throw new ValidateException("从机不存在");
                }
                $this->update($v_s['id'],$v_s);
                $template_subordinate_id=$v_s['id'];
            }else{
                $v_s["template_id"]=$template_id;
                $v_s["admin_id"]=$admin_id;
                $save=$this->save($v_s);
                $template_subordinate_id=$save['id'];
            }
            $DeviceTemplateSubordinateVariableServices->saveVariable($v_s["variable"],$template_id,$template_subordinate_id,$admin_id);
            $ids[]=$template_subordinate_id;
        }

        $this->search(["not_ids"=>$ids,"template_id"=>$template_id])->delete();
        (new DeviceTemplateSubordinateVariableServices())->delVariableByNotSubordinateIds($ids,$template_id);
    }
}