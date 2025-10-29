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
