<?php

namespace app\services\alarm;

use app\model\Admin;
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
use think\exception\ValidateException;

class AlarmTemplateTriggerServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return AlarmTemplateTrigger::class;
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
    public function trigger($variable_id,$template_subordinate_variable_id,$value){
        $template=$this->getSelect(["template_subordinate_variable_id"=>$template_subordinate_variable_id]);
        TriggerServe::trigger($template,$variable_id,$value,1);
    }


}