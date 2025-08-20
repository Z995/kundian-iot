<?php


namespace app\controller\admin;

use app\services\AdminServices;
use app\validate\admin\AdminValidate;
use plugin\kundian\base\BaseController;
use think\exception\ValidateException;


class LoginController extends BaseController
{
    /**
     * 登陆
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login()
    {
        $param=$this->validate(AdminValidate::class,"login");
        $result=(new AdminServices())->login($param["phone"],$param['password']);
        return success($result);
    }

}
