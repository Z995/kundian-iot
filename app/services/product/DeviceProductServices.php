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

namespace app\services\product;

use app\model\Admin;
use app\model\device\Device;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayInstruct;
use app\model\label\Label;
use app\model\product\DeviceProduct;
use app\serve\RedisServices;
use app\services\device\DeviceServices;
use app\services\device\DeviceSubordinateServices;
use app\services\device\DeviceTemplateServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;
/**
 * Class DeviceProductServices
 * @mixin DeviceProduct
 */
class DeviceProductServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return DeviceProduct::class;
    }

    public function saveProduct($param,$admin_id){
        $this->getModel()->transaction(function () use ($param, $admin_id) {
            if (!empty($param["id"])) {
                $result = $this->getOne(["id" => $param["id"], "admin_id" => $admin_id]);
                if (!$result) {
                    throw new ValidateException("数据不存在");
                }
                $this->update($param["id"], $param);
                $product_id = $param["id"];
            } else {
                $param['admin_id'] = $admin_id;
                $save = $this->save($param);
                $product_id = $save["id"];
            }
            (new DeviceProductVariableServices())->saveProductVariable($param["variable"], $product_id, $admin_id);
        });
    }

}