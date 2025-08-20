<?php

namespace app\controller\admin\system;

use extend\Request;
use plugin\kundian\base\BaseController;

class ConfigController extends BaseController
{
    /**
     * 获取配置
     * @return \support\Response
     */
    public function getConfig(){
        $param = getMore([
            ["key", ""],
        ]);
        return success(get_system_config($param["key"]));
    }

    /**
     * 配置
     * @return \support\Response
     */
    public function setConfig(){
        return success(set_system_config(Request::param()));
    }
}