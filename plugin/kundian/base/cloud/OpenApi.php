<?php
// 坤典开源物联网系统
// @link `https://www.cqkd.com` 
// @description 软件开发团队为 重庆坤典科技有限公司
// @description The software development team is Chongqing Kundian Technology Co., Ltd.
// @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
// @description 软件版权归 重庆坤典科技有限公司 所有
// @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
// @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
// @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
// @Date: 2025/10/9/上午10:09

namespace plugin\kundian\base\cloud;

use app\services\monitor\MonitorServices;
use think\exception\ValidateException;
use Workerman\Http\Client;

/**
 * 插件控制器需继承
 */
class OpenApi extends Base
{

    public function bindAccount($mobile, $password, $admin_id)
    {
        $result=httpsRequest($this->url . "/iot/authorize/shopLoginOrRegis");
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        set_system_config(["kun_dian_cloud_mobile" => $mobile, "kun_dian_cloud_password" => $password, "kun_dian_cloud_key" => $result['data']["key"], "kun_dian_cloud_secret" => $result['data']["secret"]], $admin_id);
    }


    public function addDevice($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/addDevice", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    public function devicesDetails($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicesdetails", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    public function delDevices($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/deletedevicesqiniu", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 获取通道
     * @param $data
     * @return mixed
     */
    public function getDeviceChannels($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/getdevicechannels", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }


    /**
     * 模版配置
     * @param $data
     * @return mixed
     */
    public function updateStreamInfo($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/updateStreamInfo", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 截图
     * @param $data
     * @return mixed
     */
    public function snap($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/snap", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }
    /**
     * 截图列表
     * @param $data
     * @return mixed
     */
    public function snapshots($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/snapshots", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 关闭流
     * @param $data
     * @return mixed
     */
    public function deviceStop($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicestop", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 开启流
     * @param $data
     * @return mixed
     */
    public function deviceStart($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicestart", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 开启流
     * @param $data
     * @return mixed
     */
    public function deviceControl($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicecontrol", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }
    /**
     * 预设位置
     * @param $data
     * @return mixed
     */
    public function devicePresets($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicepresets", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 预设位置列表
     * @param $data
     * @return mixed
     */
    public function devicePresetsList($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicepresetsList", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 开始录制
     * @param $data
     * @return mixed
     */
    public function startRecording($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/startRecording", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 停止录制
     * @param $data
     * @return mixed
     */
    public function stopRecording($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/startRecording", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 停止录制
     * @param $data
     * @return mixed
     */
    public function getRecordingList($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/getRecordingList", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 删除图片
     * @param $data
     * @return mixed
     */
    public function delSnapshots($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/delSnapshots", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 删除视频
     * @param $data
     * @return mixed
     */
    public function delRecording($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/delRecording", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }

    /**
     * 修改设备
     * @param $data
     * @return mixed
     */
    public function updateDeviceQiniu($data){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/updateDeviceQiniu", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }
    /**
     * 修改设备
     * @param $data
     * @return mixed
     */
    public function namespacesInfo($data=[]){
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/namespacesInfo", $data);
        if (empty($result['data']) && $result['code'] !== 0) {
            throw new ValidateException($result['message']??"云端错误");
        }
        return $result["data"];
    }
}