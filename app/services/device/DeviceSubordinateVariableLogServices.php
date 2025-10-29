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

use app\model\device\DeviceSubordinateVariableLog;
use plugin\kundian\base\BaseServices;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use think\exception\ValidateException;

class DeviceSubordinateVariableLogServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return DeviceSubordinateVariableLog::class;
    }

    /**
     * 保存日志
     * @param $gateway_id
     * @param $code
     * @param $subordinate_address
     * @param $function_code
     * @param $variable_id
     * @param $value
     * @return false|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveLog($gateway_id, $code, $subordinate_address, $function_code, $variable_id, $value)
    {
        $variable = DeviceSubordinateVariableServices::getRedisVariableInfo($variable_id);
        if (empty($variable)) {
            throw new ValidateException('变量不存在');
        }
        if (empty($variable["device"])) {
            throw new ValidateException('设备不存在');
        }
        $templateVariable=$variable["templateVariable"];

        $device = $variable["device"];
        $date = date("Y-m-d H:i:s");
        if ($variable["templateVariable"]["storage_mode"] == 1 && empty($variable["variableLog"])) {
            if ($value == $variable["variableLog"]["storage_mode"]) return false;
        }
        $save = ["device_id" => $device["id"], "gateway_id" => $gateway_id, "subordinate_id" => $variable['subordinate_id'],
            "variable_id" => $variable_id, "subordinate_address" => $subordinate_address,
            "template_subordinate_variable_id" => $variable['template_subordinate_variable_id'], "register_address" => $templateVariable['register_address'],
            "gateway_code" => $code, "function_code" => $function_code, "val" => $value, "create_time" => $date];
        $this->save($save);
    }

    /**
     * 获取统计
     * @param $param
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStatisticalChart($param)
    {
        $variable = (new DeviceSubordinateVariableServices())->getSelect(["ids" => $param["ids"]]);
        if (!$variable) {
            throw new ValidateException("变量不存在");
        }
        if (!empty($param["start_time"])) {
            $start_time = $param["start_time"];
        } else {
            $startOfDay = strtotime('today midnight');
            $start_time = date('Y-m-d H:i:s', $startOfDay);
        }
        if (!empty($param["end_time"])) {
            $end_time = $param["end_time"];
        } else {
            $endOfDay = strtotime('tomorrow midnight') - 1;
            $end_time = date('Y-m-d H:i:s', $endOfDay);
        }
        $where["start_time"] = $start_time;
        $where["end_time"] = $end_time;
        $res = [];
        foreach ($variable as $v) {
            $where["variable_id"] = $v["id"];
            $log = (new DeviceSubordinateVariableLogServices())->getSelect($where, [], "id,val,create_time");
            $res[] = ['variable' => $v, 'log' => $log];
        }
        return $res;
    }

}