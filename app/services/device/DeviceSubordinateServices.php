<?php

namespace app\services\device;

use app\model\Admin;
use app\model\device\Device;
use app\model\device\DeviceSubordinate;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class DeviceSubordinateServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return DeviceSubordinate::class;
    }

    /**
     * 保存变量
     * @param $variable
     * @param $device_id
     * @param $admin_id
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveDeviceSubordinate($subordinate,$device_id,$admin_id){
        $DeviceSubordinateVariableServices=new DeviceSubordinateVariableServices();
        $DeviceTemplateSubordinateVariableServices=new DeviceTemplateSubordinateVariableServices();
        $ids=[];
        foreach ($subordinate as $vo){
            $result=$this->get(["template_subordinate_id"=>$vo["template_subordinate_id"],"device_id"=>$device_id]);
            if ($result){
                $this->update($result["id"],$vo);
                $subordinate_id=$result["id"];
            }else{
                $vo["device_id"]=$device_id;
                $vo["admin_id"]=$admin_id;
                $save=$this->save($vo);
                $subordinate_id=$save['id'];
            }

            $variable=$DeviceTemplateSubordinateVariableServices->getTemplateSubordinateVariable($vo['template_subordinate_id']);
            $DeviceSubordinateVariableServices->saveVariable($variable,$subordinate_id,$device_id,$admin_id);
            $ids[]=$subordinate_id;
        }
        $this->search(["not_ids"=>$ids,"device_id"=>$device_id])->update(['is_del'=>1]);
        $DeviceSubordinateVariableServices->delVariableByNotSubordinateIds($ids,$device_id);
    }

    /**
     * 根据模版删除从机和变量
     * @param $device_id
     * @param $template_subordinate_id
     * @param DeviceSubordinateVariableServices $DeviceSubordinateVariableServices
     * @return void
     */
    public function deleteByTemplate($device_id,$template_subordinate_id,$DeviceSubordinateVariableServices){
       $ids= $this->getColumn(['is_del'=>0,'device_id'=>$device_id,"not_template_subordinate_ids"=>$template_subordinate_id],"id");
       if (!empty($ids)){
           $this->search(["ids"=>$ids,"device_id"=>$device_id])->update(['is_del'=>1]);
           $DeviceSubordinateVariableServices->delVariableBySubordinateIds($ids,$device_id);
       }
    }

}