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

namespace plugin\kundian\base\cloud;

use app\services\monitor\MonitorServices;
use think\exception\ValidateException;
use Workerman\Http\Client;

/**
 * 插件控制器需继承
 */
class OpenApi extends Base
{
    public function processingJudgment($result)
    {
        if (empty($result)) {
            throw new ValidateException("云端错误");
        }
        if (empty($result['data']) && !in_array($result['code'], [0, 1000])) {
            throw new ValidateException($result['message'] ?? "云端错误");
        }
    }

    public function bindAccount($mobile, $password, $admin_id)
    {
        $data = ["program_name" => "物联网", "program_id" => 0, "mobile" => $mobile, "password" => $password, "domain" => "iot"];
        $result = httpsRequest($this->url . "/iot/authorize/shopLoginOrRegis", $data);
        $this->processingJudgment($result);
        set_system_config(["kun_dian_cloud_mobile" => $mobile, "kun_dian_cloud_password" => $password, "kun_dian_cloud_key" => $result['data']["key"], "kun_dian_cloud_secret" => $result['data']["secret"]], $admin_id);
    }

    public function bindAccountSmsLogin($mobile, $code, $admin_id)
    {
        $data = ["program_name" => "物联网", "program_id" => 0, "mobile" => $mobile, "code" => $code, "domain" => "iot"];
        $result = httpsRequest($this->url . "/iot/authorize/shopSmsLogin", $data);
        $this->processingJudgment($result);
        set_system_config(["kun_dian_cloud_mobile" => $mobile, "kun_dian_cloud_key" => $result['data']["key"], "kun_dian_cloud_secret" => $result['data']["secret"]], $admin_id);
    }

    /**
     * 获取验证码
     * @param $mobile
     * @return void
     */
    public function getShopSmsLoginCode($mobile)
    {
        $data = ["mobile" => $mobile, "program_id" => 1];
        $result = httpsRequest($this->url . "/iot/authorize/generateShopSmsLoginCode", $data);
        $this->processingJudgment($result);
    }


    public function addDevice($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/addDevice", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    public function devicesDetails($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicesdetails", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    public function delDevices($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/deletedevicesqiniu", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 获取通道
     * @param $data
     * @return mixed
     */
    public function getDeviceChannels($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/getdevicechannels", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }


    /**
     * 模版配置
     * @param $data
     * @return mixed
     */
    public function updateStreamInfo($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/updateStreamInfo", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 截图
     * @param $data
     * @return mixed
     */
    public function snap($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/snap", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 截图列表
     * @param $data
     * @return mixed
     */
    public function snapshots($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/snapshots", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 关闭流
     * @param $data
     * @return mixed
     */
    public function deviceStop($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicestop", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 开启流
     * @param $data
     * @return mixed
     */
    public function deviceStart($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicestart", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 开启流
     * @param $data
     * @return mixed
     */
    public function deviceControl($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicecontrol", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 预设位置
     * @param $data
     * @return mixed
     */
    public function devicePresets($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicepresets", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 预设位置列表
     * @param $data
     * @return mixed
     */
    public function devicePresetsList($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/devicepresetsList", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 开始录制
     * @param $data
     * @return mixed
     */
    public function startRecording($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/startRecording", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 停止录制
     * @param $data
     * @return mixed
     */
    public function stopRecording($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/startRecording", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 停止录制
     * @param $data
     * @return mixed
     */
    public function getRecordingList($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/getRecordingList", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 删除图片
     * @param $data
     * @return mixed
     */
    public function delSnapshots($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/delSnapshots", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 删除视频
     * @param $data
     * @return mixed
     */
    public function delRecording($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/delRecording", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 修改设备
     * @param $data
     * @return mixed
     */
    public function updateDeviceQiniu($data)
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/updateDeviceQiniu", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }

    /**
     * 修改设备
     * @param $data
     * @return mixed
     */
    public function namespacesInfo($data = [])
    {
        $data = $this->package($data);
        $result = httpsRequest($this->url . "/iot/open/namespacesInfo", $data);
        $this->processingJudgment($result);
        return $result["data"];
    }


}