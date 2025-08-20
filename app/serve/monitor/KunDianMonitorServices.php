<?php

namespace app\serve\monitor;



use app\model\qiniu\QiniuDeviceModel;
use app\services\monitor\MonitorServices;
use plugin\kundian\base\cloud\OpenApi;
use think\exception\ValidateException;

class KunDianMonitorServices
{
    /**
     * 添加监控
     * @param $param
     * @param $admin_id
     * @param $host
     * @return void
     */
    public function addMonitor($param, $admin_id, $host)
    {
        try {
            $MonitorServices = new MonitorServices();
            $id = $MonitorServices->getLastId();
            $data = [
                'name' => 'Kd_' . $host . '_' . $admin_id . '_' . bcadd(100000, $id, 0),
                'desc' => $param["desc"],
                'type' => $param["device_type"] ?? 1,
            ];
            $res = (new OpenApi())->addDevice($data);
            $res['related_id'] = $res['id'];
            $res['related_type'] = "kun_dian";
            unset($res['id']);
            $res['admin_id'] = $admin_id;
            $res['longitude'] = $param["longitude"];
            $res['latitude'] = $param["latitude"];
            $MonitorServices->create($res);
        } catch (\Throwable $e) {
            throw new ValidateException($e->getMessage());
        }
    }

    /**
     * 监控详情
     * @param $info
     * @return mixed
     */
    public function getMonitorInfo($info)
    {
        $result = (new OpenApi())->devicesDetails(["id" => $info["related_id"]]);
        if (!empty($result['qiniu_device']) && $info['state']!=$result['qiniu_device']['state'] ) {
            $info['state'] = $result['qiniu_device']['state'];
            ( new MonitorServices())->update($info["id"],["state"=>$result['qiniu_device']['state']]);
        }
        $info["details"] = $result;
        return $info;
    }

    /**
     * 删除监控
     * @param $info
     * @return mixed
     */
    public function delMonitor($info)
    {
        return (new OpenApi())->delDevices(["id" => $info["related_id"]]);
    }

    /**
     * 删除监控
     * @param $info
     * @return mixed
     */
    public function getDeviceChannels($cloud_id)
    {
        return (new OpenApi())->getDeviceChannels(["id" =>$cloud_id]);
    }

    /**
     * 将流设置成按需截图模版
     * @param $info
     * @return mixed
     */
    public function updateStreamInfo($id,$streams)
    {
        $data=['id' => $id, 'streams' => $streams, 'snapshotTemplateId' =>config("snapshotTemplateId"),
            'recordTemplateId' =>config("recordTemplateId"),];
        return (new OpenApi())->updateStreamInfo($data);
    }

    /**
     * 截图
     * @param $id
     * @return mixed
     */
    public function snap($id,$streams){
        $this->updateStreamInfo($id,$streams);
        return (new OpenApi())->snap(["id"=>$id,"streams"=>$streams]);
    }

    /**
     * 截图
     * @param $id
     * @return mixed
     */
    public function snapshots($param,$info){
        $data=["id"=>$info["related_id"],"end"=>$param["end"],
            "line"=>$param["line"],"start"=>$param["start"],"type"=>$param["type"]];
        if (!empty($param["channels"])){
            $data["streams"]=$param["channels"];
        }
        if (!empty($param["marker"])){
            $data["marker"]=$param["marker"];
        }
        return (new OpenApi())->snapshots($data);
    }
    /**
     * 截图
     * @param $id
     * @return mixed
     */
    public function delSnapshots($param,$info){
        $data=["id"=>$info["related_id"],"file"=>$param["file"]];
        if (!empty($param["channels"])){
            $data["streams"]=$param["channels"];
        }
        return (new OpenApi())->delSnapshots($data);
    }

    /**
     * 关闭流
     * @param $id
     * @return mixed
     */
    public function deviceStop($param,$related_id){
        $data["id"]=$related_id;
        if (!empty($param["channels"])){
            $data["channels"]=$param["channels"];
        }
        return (new OpenApi())->deviceStop($data);
    }

    /**
     * 开启
     * @param $id
     * @return mixed
     */
    public function deviceStart($param,$related_id){
        $data["id"]=$related_id;
        if (!empty($param["channels"])){
            $data["channels"]=$param["channels"];
        }
        return (new OpenApi())->deviceStart($data);
    }
    /**
     * 设备控制
     * @param $id
     * @return mixed
     */
    public function deviceControl($param,$info){
        $data=["id"=>$info["related_id"],"cmd"=>$param["cmd"],"speed"=>$param["speed"],"channels"=>$param["channels"]];
        return (new OpenApi())->deviceControl($data);
    }
    /**
     * 预设位置
     * @param $id
     * @return mixed
     */
    public function devicePresets($param,$info){
        $data=["id"=>$info["related_id"],"cmd"=>$param["cmd"],"presetId"=>$param["presetId"],"name"=>$param["name"],"channels"=>$param["channels"]];
        return (new OpenApi())->devicePresets($data);
    }

    /**
     * 预设列表
     * @param $id
     * @return mixed
     */
    public function devicePresetsList($param,$info){
        $data=["id"=>$info["related_id"],"channels"=>$param["channels"]];
        return (new OpenApi())->devicePresetsList($data);
    }

    /**
     * 开始录制
     * @param $id
     * @return mixed
     */
    public function startRecording($param,$info){
        $data=["id"=>$info["related_id"]];
        if (!empty($param["channels"])){
            $data["streams"]=$param["channels"];
        }
        return (new OpenApi())->startRecording($data);
    }

    /**
     * 停止录制
     * @param $id
     * @return mixed
     */
    public function stopRecording($param,$info){
        $data=["id"=>$info["related_id"]];
        if (!empty($param["channels"])){
            $data["streams"]=$param["channels"];
        }
        return (new OpenApi())->stopRecording($data);
    }

    /**
     * 停止录制
     * @param $id
     * @return mixed
     */
    public function getRecordingList($param,$info){
        $data=["id"=>$info["related_id"],"line"=>$param["line"]];
        if (!empty($param["start_time"])){
            $data["start"]=$param["start_time"];
        }
        if (!empty($param["end_time"])){
            $data["end"]=$param["end_time"];
        }
        if (!empty($param["channels"])){
            $data["streams"]=$param["channels"];
        }
        if (!empty($param["marker"])){
            $data["marker"]=$param["marker"];
        }
        if (!empty($param["format"])){
            $data["format"]=$param["format"];
        }
        return (new OpenApi())->getRecordingList($data);
    }

    /**
     * 停止录制
     * @param $id
     * @return mixed
     */
    public function delRecording($param,$info){
        $data=["id"=>$info["related_id"],"file"=>$param["file"]];
        if (!empty($param["channels"])){
            $data["streams"]=$param["channels"];
        }
        return (new OpenApi())->delRecording($data);
    }

    /**
     * 停止录制
     * @param $id
     * @return mixed
     */
    public function updateDeviceQiniu($param,$info){
        $data=["id"=>$info["related_id"],"data"=>$param["data"]];
        return (new OpenApi())->updateDeviceQiniu($data);
    }

}