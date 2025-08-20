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

use app\serve\ModbusRTUServices;
use app\serve\RedisServices;
use app\services\device\DeviceServices;
use app\services\gateway\GatewayLogServices;
use app\services\gateway\GatewayServices;
use app\services\label\LabelServices;
use app\validate\admin\GatewayValidate;
use app\validate\IdValidate;
use extend\RedisQueue;
use plugin\kundian\base\BaseController;


class GatewayController extends BaseController
{
    /**
     * 网关列表
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGatewayList(){
        $where=getMore([
            ["gateway_status",''],
            ["marque_id",''],
            ["name",''],
        ]);
        $where["del"]=0;
        $where["admin_id"]=$this->adminId();
        $result=GatewayServices::getGatewayList($where);
        return success($result);
    }

    /**
     * 保存配置
     * @return \support\Response
     */
    public function saveGateway(){
        $param=$this->validate(GatewayValidate::class,"saveGateway");
        (new GatewayServices())->saveGateway($param,$this->adminId());
        return success();
    }

    /**
     * 保存配置
     * @return \support\Response
     */
    public function getGatewayInfo(){
        $param=$this->validate(IdValidate::class);
        $result=(new GatewayServices())->getOne(["id"=>$param['id'],"admin_id"=>$this->adminId()],"*",["marque","device"]);
        $result["label"]=(new LabelServices())->getLabelByIds($result["label_ids"]);
        return success($result);
    }


    /**
     * 保存配置
     * @return \support\Response
     */
    public function delGateway(){
        $param=$this->validate(IdValidate::class);
        $result=(new GatewayServices())->getOne(["id"=>$param['id'],"admin_id"=>$this->adminId()]);
        if (!$result){
            return  fail("网关不存在");
        }
        RedisServices::delGatewayRedis($result["code"]);
        (new GatewayServices())->update($param['id'],["del"=>1,'del_id'=>$this->adminId(),"del_time"=>date("Y-m-d H:i:s",time())]);
        return success();
    }

    /**
     * 返回mac值
     * @return \support\Response
     * @throws \Exception
     */
    public function getMac(){
        return success(["mac"=>get_order_sn("KD",12)]);
    }

    /**
     * 发送at命令
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sendAtInstruction(){
        $param=getMore([
            ["gateway_id",''],
            ["instruct_id",''],
            ["parameter",''],
        ]);
        (new GatewayServices())->sendAtInstruction($param['gateway_id'],$param["instruct_id"],$param["instruct_id"]);
        return success();
    }

    /**
     * 获取设备日志
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGatewayLog(){
        $param=getMore([
            ["gateway_id",0],
            ["year",''],
            ["month",''],
            ["day",''],
        ]);
        $param["del"]=0;
        $param["admin_id"]=$this->adminId();
        ModbusRTUServices::verificationModbusRTUS("07030200f37081");
        $result=(new GatewayLogServices())->getList($param);
        return success($result);
    }

}
