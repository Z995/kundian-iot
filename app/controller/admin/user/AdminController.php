<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-01-28 15:13:57
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-04-01 19:44:10
 * @FilePath: \yunxiao-webman\app\controller\IndexController.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
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
