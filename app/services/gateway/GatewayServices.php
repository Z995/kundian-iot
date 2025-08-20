<?php

namespace app\services\gateway;

use app\model\Admin;
use app\model\gateway\Gateway;
use app\model\Iot;
use app\serve\ModbusRTUServices;
use app\serve\RedisServices;
use app\serve\Snowflake;
use extend\Request;
use plugin\kundian\base\BaseServices;
use plugin\webman\gateway\Events;
use plugin\webman\gateway\servers\SortingDataServices;
use support\Redis;
use think\exception\ValidateException;

class GatewayServices extends BaseServices
{
    //协议类型
    const TYPE_LIST = [0 => 'TCP', 1 => 'WS/WSS', 2 => 'MQTT'];
    //数据类型
    const VTYPE_LIST = [0 => 'ASCII', 1 => 'HEX', 2 => 'GB2312'];
    const CRONTAB_TIMES_LIST = [1 => 1, 2 => 0.5, 3 => 60, 4 => 30, 5 => 3, 6 => 10, 7 => 600, 8 => 3600];
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return Gateway::class;
    }

    /**
     * 获取网关列表
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getGatewayList($where = [])
    {
        $result = (new self)->getPage($where);
        $list = $result["list"];
        foreach ($list as $k => $v) {
            $list[$k]['typeName'] = self::TYPE_LIST[$v['type']];
            if ($v['type'] == 2) {
                $list[$k]['status'] = Redis::hGet('HFiots-online', $v['code']) ? 1 : 2;
            } else {
                $list[$k]['status'] = $list[$k]["online"];//-1未初始化,0离线,1在线,2预警
                if ($list[$k]['is_warning'] == 1) {
                    $list[$k]['status'] = 2;//
                }
            }
        }
        $result["list"] = $list;
        return $result;
    }
    /**
     * 分页查询
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPage($where=[],$field="*",$order="id desc"){
        [$page, $limit] = $this->getPageValue();
        if (empty($page)||empty($limit)){
            $list=$this->search($where)->with( ["marque","device"])->withCount(["device"])->field($field)->order($order)->select()->toArray();
            return ["list"=>$list];
        }else{
            $list= $this->search($where)->with( ["marque","device"])->withCount(["device"])->field($field)->order($order)->page($page, $limit)->select()->toArray();
            $count = $this->search($where)->count();
            return ["list"=>$list,"count"=>$count];
        }
    }


    /**
     * 保存网关
     * @param $admin_id
     * @param $data
     * @return void
     */
    public function saveGateway($data, $admin_id)
    {
        if ($data['type'] == 2) {
            $data['password_md5'] = isset($data['password']) && $data['password'] ? md5($data['password']) : '';
            $data['action'] = 'all';
            $data['permission'] = 'allow';
        }
        $data["admin_id"] = $admin_id;
        if (!empty($data["password"])) {
            $data['password_md5'] = md5($data["password"]);
        }
        if (!empty($data["crontab"])){
            if (!empty($data["crontab"]["status"])&&$data["crontab"]["status"]==1){
                if (empty($data["crontab"]["expire"])) {
                    throw new ValidateException("定时间隔不能为空");
                }
                if (empty($data["crontab"]["val"])) {
                    throw new ValidateException("定时内容不能为空");
                }
                if (empty($data["crontab"]["val_type"])) {
                    throw new ValidateException("定时内容类型不能为空");
                }
            }

        }
        if (!empty($data["id"])) {
            $result = $this->getOne(["id" => $data["id"], "admin_id" => $admin_id]);
            if (!$result) {
                throw new ValidateException("数据不存在");
            }
            $this->update($data["id"], $data);
            $id = $data["id"];
        } else {
            $data["code"] = $data["mac"];
            $result = $this->save($data);
            $id = $result["id"];
        }

        if ($data['type'] != 2) {
            $data = $this->get($id);
            RedisServices::setGatewayRedis($data);
        }
    }

    /**
     * 获取网关
     * @param $code
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getGateway($code)
    {
        return (new self())->getOne(['code' => $code]);
    }

    /**
     * 获取网关
     * @param $code
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getIotKey($message, $original_message)
    {
        return (new self())->getModel()->where(function ($qure) use ($message,$original_message){
            $qure->where("code", $message)->whereOr('code', $original_message);
        })->where("del", 0)->find();
    }

    /**
     * 发送at命令
     * @param $gateway_id
     * @param $instruct_id
     * @param $type
     * @param $parameter
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sendAtInstruction($gateway_id, $instruct_id,$parameter = "")
    {
        $gateway = $this->get($gateway_id);
        if (!$gateway) {
            throw new ValidateException("设备不存在");
        }
        $instruct = (new GatewayMarqueInstructServices())->getOne(["marque_id" => $gateway["marque_id"], "instruct_id" => $instruct_id],"*",["instruct"]);
        if (!$instruct||empty($instruct["instruct"])){
            throw new ValidateException("命令未配置");
        }
        switch ($instruct["instruct"]["type"]){
            case 1:
                $command = $gateway["gateway_password"] . $instruct['command'] . "?".PHP_EOL;
                $type = "AtGet";
                break;
            case 2:
                if (empty($parameter)) $parameter=$instruct["parameter"];
                $command = $gateway["gateway_password"] . $instruct['command'] . "=" . $parameter.PHP_EOL;
                $type = "AtSet";
                break;
            case 3:
                $command = $gateway["gateway_password"] . $instruct['command'] .PHP_EOL;
                $type = "At";
                break;
            default;
                throw new ValidateException("指令错误");
        }

        RedisServices::addExecuteList($gateway["code"], SortingDataServices::$AtInstruction, ["command" => $command,"type"=>$type,"instruct"=>$instruct['command']]);
    }

    /**
     * 网关状态
     * @param $admin_id
     * @return array
     */
    public function gatewayStatusStatistics($admin_id){
        $online=$this->count(["admin_id"=>$admin_id,"online"=>1]);
        $not_online=$this->count(["admin_id"=>$admin_id,"online"=>0]);
        return compact("online","not_online");
    }


    /**
     * 新网关统计
     * @param $admin_id
     * @param $type
     * @return array
     */
    public function newGatewayStatistics($admin_id,$type=1){
        if ($type==1){
            $time=getDailyTimeRanges(7);
        }else{
            $time=getDailyTimeRanges(30);
        }
        $result=[];
        foreach ($time as &$v){
            $count=$this->count(["admin_id"=>$admin_id,"start_time"=>$v["start_time"],"end_time"=>$v["end_time"]]);
            $result[]=["date"=>$v["date"],"count"=>$count];
        }
        return $result;
    }


}