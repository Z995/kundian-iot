<?php

namespace app\services\product;

use app\model\Admin;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayInstruct;
use app\model\label\Label;
use app\model\product\DeviceProductVariable;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class DeviceProductVariableServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return DeviceProductVariable::class;
    }

    /**
     * 保存变量
     * @param $variable
     * @param $product_id
     * @param $admin_id
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveProductVariable($variable,$product_id,$admin_id){
        $ids=[];
        foreach ($variable as $v){
            if (!empty($v["id"])){
                $result = $this->getOne(["id" => $v["id"], "admin_id" => $admin_id]);
                if (!$result) {
                    throw new ValidateException("变量不存在");
                }
                $this->update($v['id'],$v);
                $variable_id=$v['id'];
                $ids[]=$variable_id;
            }else{
                $v["product_id"]=$product_id;
                $v["admin_id"]=$admin_id;
                $all[]=$v;
            }
        }
        if (!empty($ids)){
            $this->search(["not_ids"=>$ids,"product_id"=>$product_id])->delete();
        }
        if (!empty($all)){
            var_dump($all);
           $a= $this->saveAll($all);
           var_dump($a);
        }
    }


}