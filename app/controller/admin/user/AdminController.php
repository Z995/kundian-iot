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


namespace app\controller\admin\user;

use app\services\AdminServices;
use app\validate\admin\AdminValidate;
use plugin\kundian\base\BaseController;
use think\exception\ValidateException;


class AdminController extends BaseController
{
    /**
     * 列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminList(){
        $param = getMore([
            ["keyword", ""],
        ]);
        $param["is_del"]=0;
        $result=(new AdminServices())->getList($param);
        return success($result);
    }

    /**
     * 添加修改
     * @return \support\Response
     */
    public function saveAdmin(){
        $param = getMore([
            ["id", ""],
            ["name", ""],
            ["phone", ""],
            ["status", ""],
            ["password", ""],
        ]);
        (new AdminServices())->saveUser($param);
        return success();
    }

    /**
     * 详情
     * @return array|string|\support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminInfo(){
        $param = getMore([
            ["id", ""],
        ]);
        $param["is_del"]=0;
        $result=(new AdminServices())->get($param);
        return success($result);
    }

    /**
     * 删除
     * @return \support\Response
     */
    public function delAdmin(){
        $param = getMore([
            ["id", ""],
        ]);
        if ($param["id"]=AdminServices::adminId){
            throw new ValidateException('无法删除');
        }
        $result=(new AdminServices())->update($param,["is_del"=>1]);
        return success($result);
    }

}
