<?php

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