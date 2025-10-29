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

namespace app\services\system;

use app\model\system\SystemConfig;
use plugin\kundian\base\BaseServices;


/**
 * Class SystemConfigServices
 * @mixin SystemConfig
 */
class SystemConfigServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return SystemConfig::class;
    }

    /**
     * 获取配置
     * @param $key
     * @param $admin_id
     * @param $default
     * @return array|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static  function getSystemConfig($key,$admin_id,$default){
        $name=[];
        if (!is_array($key)){
            $is_str=true;
            $name[]=$key;
        }else{
            $is_str=false;
            $name=$key;
        }
        $config=(new self())->getSelect(["admin_id"=>$admin_id,"name_arr"=>$name]);
        foreach ($name as $v){
            $data[$v]="";
            foreach ($config as $vo){
                if ($v==$vo["name"]){
                    $data[$v]=$vo["value"];
                }
            }
        }
        $result=$is_str?$data[$key]:$data;
        if ($result==="") $result=$default;
        return $result;
    }

    /**
     * 保存配置
     * @param $param
     * @param $admin_id
     * @return false|void
     */
    public  function setSystemConfig($param,$admin_id){
        if (empty($param)){
            return false;
        }
        $data=[];
        $date=date('Y-m-d H:i:s');
        foreach ($param as $key=>$v){
            $result=$this->get(["name"=>$key]);
            if ($result){
                $this->update($result["id"],['value'=>$v]);
            }else{
                $data[]=["name"=>$key,"value"=>$v,"admin_id"=>$admin_id,"create_time"=>$date,"update_time"=>$date];
            }
        }
        if (!empty($data)){
            $this->saveAll($data);
        }
    }

}