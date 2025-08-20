<?php

namespace app\services\device;

use app\model\Admin;
use app\model\device\DeviceTemplate;
use app\serve\RedisServices;
use extend\RedisQueue;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class DeviceTemplateServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return DeviceTemplate::class;
    }

    public function getDeviceTemplateList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $model=$this->search($where)->with(["subordinate.variable","device"])->withCount(["device", 'variable'])->order("id desc");
        if (empty($page)||empty($limit)){
            $list = $model->select()->toArray();
            return ["list"=>$list];
        }else{
            $list = $model->page($page, $limit)->select()->toArray();
            $count = $this->search($where)->count();
            return ["list"=>$list,"count"=>$count];
        }
    }

    /**
     * 保存模版
     * @param $param
     * @param $admin_id
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveDeviceTemplate($param, $admin_id)
    {
        $template_id=  $this->getModel()->transaction(function () use ($param,$admin_id){
            $template = ["admin_id" => $admin_id, "name" => $param["name"], "collect" => $param["collect"], "status_config" => $param["status_config"], "space_time" => $param["space_time"]];
            if (!empty($param["id"])) {
                $result = $this->getOne(["id" => $param["id"], "admin_id" => $admin_id]);
                if (!$result) {
                    throw new ValidateException("模版不存在");
                }
                $this->update($param["id"], $template);
                $template_id = $param["id"];
            } else {
                $template["admin_id"]=$admin_id;
                $save=$this->save($template);
                $template_id = $save["id"];
            }
            $DeviceTemplateSubordinateServices=new DeviceTemplateSubordinateServices();
            $DeviceTemplateSubordinateServices->saveSubordinate($param["subordinate"],$template_id,$admin_id);
            return $template_id;
        });
        RedisQueue::queue('iots_redis_queue_synchronize_device', ["template_id"=>$template_id]);

    }

    /**
     * 删除模版
     * @param $id
     * @return void
     */
    public function delDeviceTemplate($id,$admin_id){
        $this->getModel()->transaction(function () use ($id,$admin_id){
            $this->delete(["id"=>$id,"admin_id"=>$admin_id]);
            (new DeviceTemplateSubordinateServices())->delete(["template_id"=>$id,"admin_id"=>$admin_id]);
            (new DeviceTemplateSubordinateVariableServices())->delete(["template_id"=>$id,"admin_id"=>$admin_id]);
        });
        RedisQueue::queue('iots_redis_queue_synchronize_device', ["template_id"=>$id]);
    }


}