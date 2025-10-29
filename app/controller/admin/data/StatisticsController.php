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
