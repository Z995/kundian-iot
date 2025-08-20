<?php

namespace app\controller\admin\monitor\cloud;

use plugin\kundian\base\BaseController;
use plugin\kundian\base\cloud\OpenApi;

class AuthorizeController extends BaseController
{
    /**
     * 授权绑定
     * @return \support\Response
     */
    public function kunDianAuthorize()
    {
        $param = getMore([
            ["mobile", ""],
            ["password", ""],
        ]);
        (new OpenApi())->bindAccount($param["mobile"], $param["password"], $this->adminId());
        return success();
    }

}