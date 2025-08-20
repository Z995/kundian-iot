<?php

namespace plugin\webman\gateway\servers;

use app\model\Admin;
use app\model\Iot;
use app\serve\RedisServices;
use app\services\gateway\GatewayServices;
use GatewayWorker\Lib\Gateway;
use plugin\kundian\base\BaseServices;
use support\Redis;
use think\exception\ValidateException;

class LoginServices
{
    /**
     * 验证token
     * @param $message
     * @return false|mixed
     */
    public static function check($message, $client_id)
    {
        if (!strpos($message, RedisServices::$token_bearer) === 0) {
            return false;
        }
        $info = RedisServices::getUserInfoByToken($message);
        if ($info) {
            self::bindUid($info["id"],$client_id);
            return true;
        } else {
            return false;
        }

    }

    public static function bindUid($key,$client_id)
    {
        $myUid =$key . "-Uid";
        Gateway::bindUid($client_id,$myUid);
        $_SESSION['uid'] = $myUid;
    }


    /**
     * 通过token登陆websocket
     * @param $client_id
     * @param $message
     * @param $super_code
     * @param $super_admin_id
     * @return bool
     */
    public static function adminLogin($client_id, $message)
    {
        $result = self::check($message, $client_id);
        if (!$result) {//可能是登陆设备
            return false;
        }
        if (!in_array($_SERVER['GATEWAY_PORT'], [config('plugin.webman.gateway-worker.app.ws_port'), config('plugin.webman.gateway-worker.app.wss_port')])) {
            Gateway::sendToClient($client_id, 'connect fail,please use ws protocol or wss protocol');
            Gateway::closeClient($client_id);
            return false;
        }
        return true;
    }

    /**
     * 获取设备的key
     * @param $message
     * @return false|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getIotKey($message)
    {
        //有些设备的注册包是HEX类型,所以需要转换
        if (!RedisServices::isGatewayRedis($message)) {
            $original_message = $message;
            $message = bin2hex($message);
            if (!RedisServices::isGatewayRedis($message)) { //重新缓存
                $iot = GatewayServices::getIotKey($message, $original_message);
                if (!empty($iot) && $iot['type'] != 2) {
                    RedisServices::setGatewayRedis($iot);
                    return $iot["code"];
                } else {
                    return false;
                }
            }
        }
        //查询到设备
        return $message;
    }

    /**
     * 判断连接方
     * @param $message
     * @param $super_code
     * @return string
     */
    public static function judgeLogin($message)
    {
        if (strpos($message, RedisServices::$token_bearer) !== false) {
            return "admin";
        } else {
            return "device";
        }

    }

    /**
     * 判断text协议中发送的任务是否执行
     * @param $from
     * @param $super_code
     * @return bool
     */
    public static function judgeTextToken($from, $super_code)
    {
        if ($from == $super_code) {
            return true;
        }
        $info = RedisServices::getUserInfoByToken($from);
        if ($info) {
            $iot = jsonDecode(Redis::hGet('HFiots', $from), true);
            if ($iot["admin_id"] == $info["id"]) {
                return true;
            }
        }
        return false;
    }

}