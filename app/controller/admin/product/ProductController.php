<?php


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
