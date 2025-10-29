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