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
use app\model\alarm\AlarmLinkageLog;
use app\model\alarm\AlarmTemplateTrigger;
use app\model\alarm\AlarmWarningLog;
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

class AlarmLinkageLogServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return AlarmLinkageLog::class;
    }


    public function saveLinkageLog($trigger,$variable,$trigger_condition,$trigger_linkage,$trigger_type){
        $data=["trigger_id"=>$trigger["id"],"admin_id"=>$trigger["admin_id"],"name"=>$variable["device"]["name"],"trigger_type"=>$trigger_type,
            "trigger_name"=>$trigger['name'],"subordinate_name"=>$variable["subordinate"]["subordinate"]["name"],"variable_name"=>$variable["name"],
            "trigger_condition"=>$trigger_condition,"trigger_device"=>$trigger_linkage['device']["name"],
            "device_id"=>$trigger_linkage['device_id'],"variable_id"=>$trigger_linkage['id']];
        $this->save($data);
    }


}