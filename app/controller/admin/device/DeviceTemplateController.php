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


namespace app\controller\admin\device;

use app\model\device\DeviceSubordinateVariableLog;
use app\services\device\DeviceTemplateServices;
use app\services\gateway\GatewayServices;
use app\validate\admin\GatewayValidate;
use app\validate\IdValidate;
use plugin\kundian\base\BaseController;


class DeviceTemplateController extends BaseController
{
    /**
     * 列表
     * @return \support\Response
     */
    public function getDeviceTemplateList(){
        $param=getMore([
            ["name",""]
        ]);
        $param["admin_id"]=$this->adminId();
        $result=(new DeviceTemplateServices())->getDeviceTemplateList($param);
        return success($result);
    }

    /**
     * 详情
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDeviceTemplateInfo(){
        $param=getMore([
            ["id",""]
        ]);
        $result=(new DeviceTemplateServices())->get($param["id"],[],["subordinate.variable"]);
        return success($result);
    }

    /**
     * 保存模版
     * @return \support\Response
     */
    public function saveDeviceTemplate(){
        $param=getMore([
            ["id",""],
            ["name",""],
            ["collect",1],
            ["status_config",1],
            ["space_time",3],
            ["subordinate",""],//从机 以及变量
        ]);
        (new DeviceTemplateServices())->saveDeviceTemplate($param,$this->adminId());
        return success();
    }

    /**
     * 删除
     * @return \support\Response
     */
    public function delDeviceTemplate(){
        $param=getMore([
            ["id",""]
        ]);
        (new DeviceTemplateServices())->delDeviceTemplate($param["id"],$this->adminId());
        return success();
    }

}
