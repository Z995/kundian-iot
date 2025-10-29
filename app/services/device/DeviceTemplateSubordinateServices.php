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