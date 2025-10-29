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


namespace app\controller\admin\monitor;

use app\serve\ModbusRTUServices;
use app\serve\monitor\KunDianMonitorServices;
use app\services\AdminServices;
use app\services\monitor\MonitorAutoServices;
use app\services\monitor\MonitorServices;
use app\validate\admin\AdminValidate;
use plugin\kundian\base\BaseController;
use plugin\kundian\base\cloud\OpenApi;
use support\Request;
use think\exception\ValidateException;


class MonitorController extends BaseController
{


    public function addMonitor(Request $request)
    {
        $param = getMore([
            ["type", "kun_dian"],
            ["desc", ""],
            ["device_type", ""],

            ["id", ""],
            ["name", ""],
            ["rtmp", ""],
            ["hls", ""],
            ["flv", ""],
            ["webrtc", ""],
            ["latitude", 0],
            ["longitude",0],
        ]);
        (new MonitorServices())->addMonitor($param, $this->adminId(),$request->host(true));
        return success();
    }

    public function updateMonitor()
    {
        $param = getMore([
            ["id", ""],
            ["desc", ""],
            ["name", ""],
            ["rtmp", ""],
            ["hls", ""],
            ["flv", ""],
            ["webrtc", ""],
        ]);
        (new MonitorServices())->updateMonitor($param, $this->adminId());
        return success();
    }


    public function getMonitorList(){
        $param = getMore([
            ["name", ""],
        ]);
        $param["admin_id"]= $this->adminId();
        $param["is_del"]= 0;
        $result=(new MonitorServices())->getList($param);
        return success($result);
    }


    public function getMonitorInfo(){
        $param = getMore([
            ["id", ""],
        ]);
        $result=(new MonitorServices())->getMonitorInfo($param);
        return success($result);
    }

    public function delMonitor(){
        $param = getMore([
            ["id", ""],
        ]);
        $param["admin_id"]= $this->adminId();
        (new MonitorServices())->delMonitor($param);
        return success();
    }

    /**
     * 获取通道
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDeviceChannels(){
        $param = getMore([
            ["id", ""],
        ]);
        $result=(new MonitorServices())->getDeviceChannels($param);
        return success($result);
    }

    /**
     * 获取通道
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function snap(){
        $param = getMore([
            ["id", ""],
            ["channels", ""],
        ]);
        $result=(new MonitorServices())->snap($param["id"],$param["channels"]);
        return success($result);
    }

    /**
     * 获取通道
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function snapshots(){
        $param = getMore([
            ["id", ""],
            ["end", ""],
            ["line", ""],
            ["start", ""],
            ["type", ""],
            ["channels", ""],
            ["marker", ""],
        ]);
        $result=(new MonitorServices())->snapshots($param);
        return success($result);
    }
    /**
     * 获取通道
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delSnapshots(){
        $param = getMore([
            ["id", ""],
            ["file", ""],
            ["channels", ""]
        ]);
        $result=(new MonitorServices())->delSnapshots($param);
        return success($result);
    }

    /**
     * 开启流
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deviceStart(){
        $param = getMore([
            ["id", ""],
            ["channels", ""],
        ]);
        $result=(new MonitorServices())->deviceStart($param);
        return success($result);
    }

    /**
     * 关闭流
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deviceStop(){
        $param = getMore([
            ["id", ""],
            ["channels", ""],
        ]);
        $result=(new MonitorServices())->deviceStop($param);
        return success($result);
    }
    /**
     * 关闭流
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deviceControl(){
        $param = getMore([
            ["id", ""],
            ["cmd", ""],
            ["speed", ""],
            ["channels", ""],
        ]);
        $result=(new MonitorServices())->deviceControl($param);
        return success($result);
    }

    /**
     * 预设位置
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function devicePresets(){
        $param = getMore([
            ["id", ""],
            ["cmd", ""],
            ["name", ""],
            ["presetId",""],
            ["channels", ""],
        ]);
        $result=(new MonitorServices())->devicePresets($param);
        return success($result);
    }

    /**
     * 预设列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function devicePresetsList(){
        $param = getMore([
            ["id", ""],
            ["channels", ""],
        ]);
        $result=(new MonitorServices())->devicePresetsList($param);
        return success($result);
    }

    /**
     * 开始录制
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function startRecording(){
        $param = getMore([
            ["id", ""],
            ["channels", ""],
        ]);
        $result=(new MonitorServices())->startRecording($param);
        return success($result);
    }

    /**
     * 停止录制
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function stopRecording(){
        $param = getMore([
            ["id", ""],
            ["channels", ""],
        ]);
        $result=(new MonitorServices())->stopRecording($param);
        return success($result);
    }

    /**
     * 录制视频列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRecordingList(){
        $param = getMore([
            ["id", ""],
            ["line", ""],
            ["start_time", ""],
            ["end_time", ""],
            ["channels", ""],
            ["marker", ""],
            ["format", ""],
        ]);
        $result=(new MonitorServices())->getRecordingList($param);
        return success($result);
    }

    /**
     * 录制视频列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delRecording(){
        $param = getMore([
            ["id", ""],
            ["file", ""],
            ["channels", ""],
        ]);
        $result=(new MonitorServices())->delRecording($param);
        return success($result);
    }
    /**
     * 录制视频列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateDevice(){
        $param = getMore([
            ["id", ""],
            ["data", ""],
        ]);
        $result=(new MonitorServices())->updateDevice($param);
        return success($result);
    }
    /**
     * 录制视频列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function namespacesInfo(){
        $result= (new OpenApi())->namespacesInfo();
        return success($result);
    }
    /**
     * 配置
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveMonitorAuto(){
        $param = getMore([
            ["monitor_id", ""],
            ["type",1],
            ["op_type",1],
            ["op_value",""],
            ["channels",""],
            ["status",""],
            ["recording_duration",""],
        ]);
        (new MonitorAutoServices())->saveMonitorAuto($param);
        return success();
    }
    /**
     * 获取配置列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMonitorAuto(){
        $param = getMore([
            ["monitor_id", ""],
            ["type",1],
            ["channels",""],
        ]);
        $result= (new MonitorAutoServices())->get($param);
        return success($result);
    }

}
