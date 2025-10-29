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

namespace app\model\gateway;

use app\model\device\Device;
use app\services\gateway\GatewayOnlineLogServices;
use plugin\kundian\base\BaseModel;
use plugin\webman\gateway\Events;


/**
 * Class Model
 * @package think
 */
class Gateway extends BaseModel
{
    public function marque(){
        return $this->belongsTo(GatewayMarque::class,'marque_id');
    }
    public function device(){
        return $this->hasMany(Device::class,'gateway_id');
    }
    protected function setFilterAttr($value)
    {
        if (is_array($value))return json_encode($value);
        return $value;
    }
    protected function getFilterAttr($value)
    {
        if (is_string($value))return json_decode($value,true);
        return $value;
    }
    protected function setCrontabAttr($value)
    {
        if (is_array($value))return json_encode($value);
        return $value;
    }
    protected function getCrontabAttr($value)
    {
        if (is_string($value))return json_decode($value,true);
        return $value;
    }

    public function searchNameAttr($query, $value)
    {
        if ($value!=='') $query->where('name','like', $value . '%');
    }

    public function searchMarqueIdAttr($query, $value)
    {
        if ($value!=='') $query->where('marque_id',$value);
    }

    public function searchGatewayStatusAttr($query, $value)
    {
        if ($value!=='') {
            if (in_array($value,[-1,0,1])){
                $query->where('online', $value);
            }
            if ($value==2){
                $query->where('is_warning', 1);
            }
        }
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
     * 设备状态
     * @param $page
     * @param $count
     * @param $limit
     * @return false|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateGatewayStatus($page,$count,$limit=10)
    {
        $gateway= $this->where(["del" => 0])
            ->field("id,code,admin_id,name,online")->page($page, $limit)->select()->toArray();
        if (empty($gateway)){
            return false;
        }
        foreach ($gateway as $v) {
            $online=Events::isOnlineByUid($v["code"]);
            if ($online!=$v["online"]){
                GatewayOnlineLogServices::saveOnlineLog($v,$online);
                $this->update(["online" => $online],["id"=>$v["id"]]);
            }
        }
        if ($count>($page*$limit)){
            $this->updateGatewayStatus($page+1,$count,$limit);
        }
    }


    /**
     * 保存网关在线状态
     * @param $code
     * @param $online
     * @return void
     */
    public static function saveGatewayOnline($code,$online){
       self::update(["online"=>$online],["code"=>$code]);
    }

}
