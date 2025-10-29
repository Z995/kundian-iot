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