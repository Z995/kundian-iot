<?php


namespace app\controller\admin\label;

use app\services\gateway\GatewayServices;
use app\services\label\LabelCategoryServices;
use app\services\label\LabelServices;
use app\validate\admin\GatewayValidate;
use app\validate\IdValidate;
use plugin\kundian\base\BaseController;


class LabelController extends BaseController
{

    /**
     * 分类
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabelCategoryList()
    {
        $where["admin_id"]=$this->adminId();
        $result = (new LabelCategoryServices())->getList($where,["label"]);
        return success($result);
    }

    /**
     * 详情
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabelCategoryInfo()
    {
        $param = getMore([
            ["id", ""]
        ]);
        $result = (new LabelCategoryServices())->get($param['id']);
        return success($result);
    }


    /**
     * 保存修改
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveLabelCategory()
    {
        $param = getMore([
            ["id", ""],
            ["name", ""]
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new LabelCategoryServices())->saveData($param);
        return success($result);
    }

    /**
     * 删除
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delLabelCategory()
    {
        $param = getMore([
            ["id", ""],
        ]);
        $count=(new LabelServices())->count(['category_id'=>$param['id']]);
        if ($count>0){
            return fail("请先删除分类下面的标签");
        }
        (new LabelCategoryServices())->delete($param["id"]);
        return success();
    }



    /**
     * 标签
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabelList()
    {
        $where["admin_id"]=$this->adminId();
        $result = (new LabelServices())->getList($where);
        return success($result);
    }

    /**
     * 详情
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabelInfo()
    {
        $param = getMore([
            ["id", ""]
        ]);
        $result = (new LabelServices())->get($param['id']);
        return success($result);
    }


    /**
     * 保存修改
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveLabel()
    {
        $param = getMore([
            ["id", ""],
            ["category_id", ""],
            ["name", ""]
        ]);
        $param["admin_id"] = $this->adminId();
        $result = (new LabelServices())->saveData($param);
        return success($result);
    }

    /**
     * 删除
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delLabel()
    {
        $param = getMore([
            ["id", ""],
        ]);
        (new LabelServices())->delete($param['id']);
        return success();
    }




}
