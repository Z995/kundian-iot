<?php

/**
 * 坤典智慧农场V6
 * @link https://www.cqkd.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing KunDian Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing KunDian Technology Co., Ltd.
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-www.cqkd.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 */

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