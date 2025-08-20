<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-01-28 15:13:57
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-04-01 19:44:10
 * @FilePath: \yunxiao-webman\app\controller\IndexController.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\controller\admin\data;

use app\services\alarm\AlarmLinkageLogServices;
use app\services\alarm\AlarmWarningLogServices;
use app\services\device\DeviceOnlineLogServices;
use app\services\device\DeviceServices;
use app\services\gateway\GatewayOnlineLogServices;
use app\services\gateway\GatewayServices;
use plugin\kundian\base\BaseController;



class StatisticsController extends BaseController
{
    /**
     * 设备状态统计
     * @return \support\Response
     */
    public function deviceStatusStatistics(){
        $result = (new DeviceServices())->deviceStatusStatistics($this->adminId());
        return success($result);
    }

    /**
     * 设备标签统计
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deviceLabelStatistics(){
        $result = (new DeviceServices())->deviceLabelStatistics($this->adminId());
        return success($result);
    }

    /**
     * 网关状态统计
     * @return \support\Response
     */
    public function gatewayStatusStatistics(){
        $result = (new GatewayServices())->gatewayStatusStatistics($this->adminId());
        return success($result);
    }

    /**
     * 设备报警记录
     * @return \support\Response
     */
    public function getDeviceWarningRecord(){
        $result = (new AlarmWarningLogServices())->getDeviceWarningRecord($this->adminId());
        return success($result);
    }

    /**
     * 新设备统计
     * @return \support\Response
     */
    public function newDeviceStatistics(){
        $param = getMore([
            ["type", 1],
        ]);
        $result = (new DeviceServices())->newDeviceStatistics($this->adminId(),$param["type"]);
        return success($result);
    }

    /**
     * 新设备统计
     * @return \support\Response
     */
    public function newGatewayStatistics(){
        $param = getMore([
            ["type", 1],
        ]);
        $result = (new GatewayServices())->newGatewayStatistics($this->adminId(),$param["type"]);
        return success($result);
    }
}
