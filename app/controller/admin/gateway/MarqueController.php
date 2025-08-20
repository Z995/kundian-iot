<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-01-28 15:13:57
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-04-01 19:44:10
 * @FilePath: \yunxiao-webman\app\controller\IndexController.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\controller\admin\gateway;

use app\services\gateway\GatewayInstructServices;
use app\services\gateway\GatewayMarqueServices;
use app\services\gateway\GatewayServices;
use app\validate\admin\GatewayValidate;
use app\validate\IdValidate;
use plugin\kundian\base\BaseController;
use think\exception\ValidateException;


class MarqueController extends BaseController
{
    /**
     * 型号列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMarqueList(){
        $param=getMore([
            ["name",""]
        ]);
        $param["admin_id"]=$this->adminId();
        $result=(new GatewayMarqueServices())->getList($param);
        return success($result);
    }

    /**
     * 获取详情
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMarqueInfo(){
        $param=getMore([
            ["id",""]
        ]);
        $param["admin_id"]=$this->adminId();
        $result=(new GatewayMarqueServices())->getOne($param,'*',['instruct.instruct']);
        return success($result);
    }

    /**
     * 保存型号
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveMarque(){
        $param=getMore([
            ["id",""],
            ["name",""],
            ["instruct",""],
        ]);
        $param["admin_id"]=$this->adminId();
        (new GatewayMarqueServices())->saveMarque($param);
        return success();
    }


    /**
     * 删除
     * @return \support\Response
     */
    public function delMarque(){
        $param=getMore([
            ["id",""],
        ]);
        (new GatewayMarqueServices())->delMarque($param['id'],$this->adminId());
        return success();
    }

    /**
     * 命令课本
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInstruct(){
        $result=(new GatewayInstructServices())->getList();
        return success($result);
    }
}
