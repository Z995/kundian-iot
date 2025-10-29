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