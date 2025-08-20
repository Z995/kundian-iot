<?php


namespace app\controller\admin;

use plugin\kundian\base\BaseController;
use think\exception\ValidateException;


class IotController extends BaseController
{
    public function index()
    {
        var_dump($this->getAdminInfo());
        return success([122]);
    }

}
