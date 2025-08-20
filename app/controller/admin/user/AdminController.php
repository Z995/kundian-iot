<?php


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
