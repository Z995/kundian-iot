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
use app\services\device\DeviceSubordinateVariableServices;
use app\services\gateway\GatewayOnlineLogServices;
use plugin\kundian\base\BaseController;



class LogController extends BaseController
{

    public function gatewayOnlineLog(){
        $param = getMore([
            ["gateway_id", ""],
            ["start_time", ""],
            ["end_time", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new GatewayOnlineLogServices())->getList($param);
        return success($result);
    }
    public function deviceOnlineLog(){
        $param = getMore([
            ["device_id", ""],
            ["start_time", ""],
            ["end_time", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new DeviceOnlineLogServices())->getList($param);
        return success($result);
    }
    public function warningLog(){
        $param = getMore([
            ["device_id", ""],
            ["variable_id", ""],
            ["is_warning", ""],
            ["status", ""],
            ["start_time", ""],
            ["end_time", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new AlarmWarningLogServices())->getList($param);
        return success($result);
    }

    /**
     * 处理报警
     * @return \support\Response
     */
    public function dealWarningLog(){
        $param = getMore([
            ["id", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $AlarmWarningLogServices=new AlarmWarningLogServices();
        $info=$AlarmWarningLogServices->get($param["id"]);
        if ($info){
            $AlarmWarningLogServices->update($param,["status"=>1]);
            (new DeviceSubordinateVariableServices())->update($info["variable_id"],["is_warning"=>0]);
        }
        return success();
    }
    /**
     * 处理报警
     * @return \support\Response
     */
    public function delWarningLog(){
        $param = getMore([
            ["id", ""],
        ]);
        $param["admin_id"] = $this->adminId();
         (new AlarmWarningLogServices())->delete($param);
        return success();
    }

    public function linkageLog(){
        $param = getMore([
            ["device_id", ""],
            ["variable_id", ""],
            ["start_time", ""],
            ["end_time", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new AlarmLinkageLogServices())->getList($param);
        return success($result);
    }
    public function delLinkageLog(){
        $param = getMore([
            ["id", ""],
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new AlarmLinkageLogServices())->delete($param);
        return success($result);
    }

}
