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

namespace app\services\alarm;

use app\model\Admin;
use app\model\alarm\AlarmIndependenceTrigger;
use app\model\alarm\AlarmTemplateTrigger;
use app\model\device\Device;
use app\serve\RedisServices;
use app\serve\TextProtocolServices;
use app\serve\TriggerServe;
use app\services\device\DeviceSubordinateServices;
use app\services\device\DeviceSubordinateVariableServices;
use app\services\device\DeviceTemplateServices;
use plugin\kundian\base\BaseServices;
use plugin\webman\gateway\servers\SortingDataServices;
use support\Redis;
use think\exception\ValidateException;
use function Symfony\Component\String\b;

class AlarmIndependenceTriggerServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return AlarmIndependenceTrigger::class;
    }

    public function getRedisIndependenceTrigger($subordinate_variable_id)
    {
        $key= "AlarmIndependenceTrigger:" . $subordinate_variable_id;
        $list=Redis::get($key);
        if (empty($list)){
            $list=$this->getSelect(["subordinate_variable_id"=>$subordinate_variable_id,"status"=>1,"id"=>22],["subordinateVariable"]);
            $expire=rand(10000, 15000);//防止大面积同一时间过期
            if (empty($list)) {
                $list=[];
            }
            Redis::set($key,json_encode($list),"Ex", $expire);
        }else{
            $list=json_decode($list,true);
        }
        return $list;
    }
    /**
     * 独立触发器
     * @param $admin_id
     * @param $subordinate_variable_id
     * @param $value
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function trigger($subordinate_variable_id,$value){
//        $list=$this->getSelect(["subordinate_variable_id"=>$subordinate_variable_id,"status"=>1,"id"=>22],["subordinateVariable"]);
        $list=$this->getRedisIndependenceTrigger($subordinate_variable_id);
        TriggerServe::trigger($list,$subordinate_variable_id,$value,2);
    }





}