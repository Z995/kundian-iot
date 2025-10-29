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


namespace app\controller\admin\product;

use app\services\gateway\GatewayServices;
use app\services\label\LabelCategoryServices;
use app\services\label\LabelServices;
use app\services\product\DeviceProductServices;
use app\services\product\DeviceProductVariableServices;
use app\validate\admin\GatewayValidate;
use app\validate\IdValidate;
use plugin\kundian\base\BaseController;


class ProductController extends BaseController
{
    /**
     * 产品列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductList(){
        $param = getMore([
            ["name", ""],
            ["status", ""],
        ]);
        $param["admin_id"]=$this->adminId();
        $result=(new DeviceProductServices())->getList($param);
        return success($result);
    }
    /**
     * 产品列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductInfo(){
        $param = getMore([
            ["id", ""],
        ]);
        $param["admin_id"]=$this->adminId();
        $result=(new DeviceProductServices())->get($param,[],["variable"]);
        return success($result);
    }

    /**
     * 改变状态
     * @return \support\Response
     */
    public function updateStatus(){
        $param = getMore([
            ["id", ""],
            ["status", ""],
        ]);
        (new DeviceProductServices())->update($param['id'],["status"=>$param["status"]]);
        return success();
    }

    /**
     * 保存产品
     * @return \support\Response
     */
    public function saveProduct(){
        $param = getMore([
            ["id", ""],
            ["name", ""],
            ["protocol", ""],
            ["status", ""],
            ["variable", ""],
        ]);
        (new DeviceProductServices())->saveProduct($param,$this->adminId());
        return success();
    }

    /**
     * 删除产品
     * @return \support\Response
     */
    public function delProduct(){
        $param = getMore([
            ["id", ""],
        ]);
        $DeviceProductServices=new DeviceProductServices();
        $DeviceProductServices->getModel()->transaction(function () use ($param,$DeviceProductServices){
            $DeviceProductServices->delete(["id"=>$param["id"],"admin_id"=>$this->adminId()]);
            (new DeviceProductVariableServices())->delete(["product_id"=>$param["id"],"admin_id"=>$this->adminId()]);
        });
        return success();
    }



}
