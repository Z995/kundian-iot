<?php

namespace app\services\gateway;

use app\model\Admin;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayMarque;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class GatewayMarqueServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return GatewayMarque::class;
    }

    /**
     * 保存型号
     * @param $param
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveMarque($param){
        $this->getModel()->transaction(function () use ($param){
            if (!empty($param["id"])){
                $data=$this->get(["id"=>$param["id"],"admin_id"=>$param["admin_id"]]);
                if (!$data){
                    throw new ValidateException("网关不存在");
                }
                $this->update($param["id"],$param);
                $id=$param['id'];
            }else{
                $save=$this->save($param);
                $id=$save['id'];
            }
            $instruct=$param["instruct"];
            $GatewayMarqueInstructServices=new GatewayMarqueInstructServices();
            foreach ($instruct as $v){
                if (!empty($v["id"])){
                    $ids[]=$v["id"];
                    $GatewayMarqueInstructServices->update($v["id"],$v);
                }else{
                    $v['marque_id']=$id;
                    $v['create_time']=date("Y-m-d H:i:s");
                    $all[]=$v;
                }
            }
            $GatewayMarqueInstructServices->search(["not_ids"=>$ids??[],"marque_id"=>$id])->delete();

            if (!empty($all)){
                $GatewayMarqueInstructServices->saveAll($all);
            }
        });


    }

    /**
     * 删除
     * @param $id
     * @return void
     */
    public function delMarque($id,$admin_id){
        $this->getModel()->transaction(function () use ($id,$admin_id){
            $data=$this->get(["id"=>$id,"admin_id"=>$admin_id]);
            if (!$data){
                throw new ValidateException("网关不存在");
            }
            $this->delete($id);
            (new GatewayMarqueInstructServices())->delete(["marque_id"=>$id]);
        });
    }

}