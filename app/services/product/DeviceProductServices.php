<?php

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