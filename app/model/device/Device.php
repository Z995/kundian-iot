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

namespace app\model\device;

use app\model\gateway\Gateway;
use app\services\device\DeviceOnlineLogServices;
use plugin\kundian\base\BaseModel;


/**
 * Class Model
 * @package think
 */
class Device extends BaseModel
{

    public function subordinate(){
        return $this->hasMany(DeviceSubordinate::class,"device_id")->where("is_del",0);
    }

    public function template(){
        return $this->belongsTo(DeviceTemplate::class,"template_id");
    }

    public function gateway(){
        return $this->belongsTo(Gateway::class,"gateway_id");
    }
    public function variableLog(){
        return $this->hasOne(DeviceSubordinateVariableLog::class,"device_id")->order("id desc");
    }

    public function searchNameAttr($query, $value)
    {
        if ($value!=='') $query->where('name','like', $value . '%');
    }
    public function searchTypeAttr($query, $value)
    {
        if ($value!=='') $query->where('type', $value);
    }
    public function searchTemplateIdAttr($query, $value)
    {
        if ($value!=='') $query->where('template_id', $value);
    }
    public function searchStartTimeAttr($query, $value)
    {
        if ($value!=='') {
            if (is_int($value)){
                $value=date("Y-m-d H:i:s",$value);
            }
            $query->where('create_time','>=',$value);
        };
    }
    public function searchEndTimeAttr($query, $value)
    {
        if ($value!==''){
            if (is_int($value)){
                $value=date("Y-m-d H:i:s",$value);
            }
            $query->where('create_time','<=',$value);
        }
    }
    /**
     * 配送方式
     * @param $query
     * @param $value
     */
    public function searchLabelIdsAttr($query, $value)
    {
        if (!empty($value)) {
            $query->where('find_in_set(' . $value . ',`label_ids`)');
        }
    }

    public function searchDeviceStatusAttr($query, $value)
    {
        if ($value!=='') {
            if (in_array($value,[0,1])){
                $query->where('status', $value);
            }
            if ($value==2){
                $query->where('is_warning', 1);
            }
        }
    }



    /**
     * 设备状态
     * @param $page
     * @param $count
     * @param $limit
     * @return false|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateDeviceStatus($page,$count,$limit=10)
    {
        $device= $this->where(["is_del" => 0])->with(["template", "gateway"])
            ->field("id,gateway_id,template_id,status")->page($page, $limit)->select();
        if (empty($device)){
            return false;
        }
        foreach ($device as $v) {
            if (empty($v["template"])||empty($v["gateway"])) {
                continue;
            }
            $status=$this->getDeviceStatus($v["template"],$v['gateway']["id"],$v["id"]);
            if ($v["status"]!=$status){
                DeviceOnlineLogServices::saveOnlineLog($v,$status);
                $this->update( ["status" => $status],["id"=>$v["id"]]);
            }
        }
        if ($count>($page*$limit)){
            $this->updateDeviceStatus($page+1,$count,$limit);
        }

    }

    /**
     * 获取设备状态
     * @param $device
     * @return int
     */
    public static function getDeviceStatus($template,$gateway_id,$device_id=""){
        $status = 0;
        if ($template["status_config"] == 1) {//网关
            $status=Gateway::where("id",$gateway_id)->value("online");
        }
        if ($template["status_config"] == 2&&!empty($device_id)) {//设备数据
            $create_time=DeviceSubordinateVariableLog::where("device_id",$device_id)->order("id desc")->value("create_time");
            if (!empty($create_time)) {
                $space_time = time() - ($template["space_time"] * 60);
                if (strtotime($create_time) > $space_time) {
                    $status = 1;
                }
            }
        }
        return $status;
    }



}
