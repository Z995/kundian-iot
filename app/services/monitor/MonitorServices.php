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

namespace app\services\monitor;

use app\model\Admin;
use app\model\device\Device;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayInstruct;
use app\model\label\Label;
use app\model\monitor\Monitor;
use app\model\product\DeviceProduct;
use app\serve\monitor\KunDianMonitorServices;
use app\serve\RedisServices;
use app\services\device\DeviceServices;
use app\services\device\DeviceSubordinateServices;
use app\services\device\DeviceTemplateServices;
use app\services\product\DeviceProductVariableServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

/**
 * Class MonitorServices
 * @mixin Monitor
 */
class MonitorServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return Monitor::class;
    }

    public function getLastId()
    {
        return $this->search()->order("id desc")->value("id") ?? 1;
    }
    /**
     * 获取监控
     * @param $ids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMonitorByIds($ids){
        if (empty($ids)){
            return [];
        }
        return $this->getModel()->whereIn('id',$ids)->select()->toArray();
    }


    public function updateMonitor($param,$admin_id){
        $result=$this->get(["id"=>$param["id"],"admin_id"=>$admin_id]);
        if (!$result){
            throw new ValidateException("监控不能为空");
        }
        $this->update($param["id"], $param);
    }

    public function createMonitor($param, $admin_id){
        $param["admin_id"]=$admin_id;
        $param["related_type"]="local";
        if (!empty($param["id"])){
            $this->update($param["id"],$param);
        }else{
            $this->save($param);
        }
    }

    public function addMonitor($param, $admin_id, $host)
    {
        switch ($param["type"]) {
            case "kun_dian":
                (new KunDianMonitorServices())->addMonitor($param, $admin_id, $host);
                break;
            case "local":
                $this->createMonitor($param, $admin_id);
                break;
        }
    }

    public function getMonitorInfo($id)
    {
        $info = $this->get($id);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result = (new KunDianMonitorServices())->getMonitorInfo($info);
                break;
            default:
                $result=$info;

        }
        return $result ;
    }

    public function delMonitor($param)
    {
        $info = $this->get($param);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        $this->update($info["id"],["is_del"=>1]);
        switch ($info["related_type"]) {
            case "kun_dian":
                 (new KunDianMonitorServices())->delMonitor($info);
        }
    }

    /**
     * 获取通道
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDeviceChannels($id){
        $info = $this->get($id);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
               $result= (new KunDianMonitorServices())->getDeviceChannels($info["related_id"]);
        }
        return $result??[];
    }


    /**
     * 截图
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function snap($id,$channels){
        $info = $this->get($id);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
               $result= (new KunDianMonitorServices())->snap($info["related_id"],$channels);
        }
        return $result??[];
    }


    /**
     * 截图列表
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function snapshots($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
               $result= (new KunDianMonitorServices())->snapshots($param,$info);
        }
        return $result??[];
    }

    /**
     * 开启流
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deviceStart($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }

        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->deviceStart($param,$info["related_id"]);
        }
        $this->save(['stream_status' => 1, 'state' => 'online']);
        return $result??[];
    }

    /**
     * 关闭流
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deviceStop($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->deviceStop($param,$info["related_id"]);
        }
        $this->save(['stream_status' => 0]);
        return $result??[];
    }
    /**
     * 关闭流
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deviceControl($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->deviceControl($param,$info);
        }
        return $result??[];
    }

    /**
     * 预设位置
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function devicePresets($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->devicePresets($param,$info);
        }
        return $result??[];
    }
    /**
     * 关闭流
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function devicePresetsList($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->devicePresetsList($param,$info);
        }
        return $result??[];
    }
    /**
     * 开始录制
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function startRecording($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->startRecording($param,$info);
        }
        return $result??[];
    }


    /**
     * 停止录制
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function stopRecording($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->stopRecording($param,$info);
        }
        return $result??[];
    }

    /**
     * 录制视频列表
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRecordingList($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->getRecordingList($param,$info);
        }
        return $result??[];
    }

    /**
     * 录制视频列表
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delSnapshots($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->delSnapshots($param,$info);
        }
        return $result??[];
    }

    /**
     * 录制视频列表
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delRecording($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->delRecording($param,$info);
        }
        return $result??[];
    }


    /**
     * 录制视频列表
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateDevice($param){
        $info = $this->get($param["id"]);
        if (empty($info)) {
            throw new ValidateException("监控不存在");
        }
        switch ($info["related_type"]) {
            case "kun_dian":
                $result= (new KunDianMonitorServices())->updateDeviceQiniu($param,$info);
        }
        return $result??[];
    }







}