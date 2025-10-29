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


namespace plugin\webman\gateway;

use app\model\Iot;
use app\serve\HeartbeatFilter;
use app\serve\RedisServices;
use \app\model\gateway\Gateway as GatewayModel;
use app\services\gateway\GatewayOnlineLogServices;
use extend\RedisQueue;
use GatewayWorker\Lib\Gateway;
use plugin\webman\gateway\servers\LoginServices;
use plugin\webman\gateway\servers\SortingDataServices;
use plugin\webman\gateway\servers\TimeServices;
use support\Log;
use support\Redis;
use Workerman\Timer;

class Events
{
    static $TCP_PORT = ''; //tcp端口
    static $WS_PORT = ''; //ws端口
    static $WSS_PORT = ''; //wss端口
    static $TEXT_PORT = ''; //TEXT端口
    static $UDP_PORT = ''; //TEXT端口
    static $MAIN_CODE = ''; //超级权限uid
    static $SUPER_ADMIN_ID = ''; //超级管理员ID

    public static function onWorkerStart($worker)
    {
        self::$TCP_PORT = config('plugin.webman.gateway-worker.app.tcp_port');
        self::$WS_PORT = config('plugin.webman.gateway-worker.app.ws_port');
        self::$WSS_PORT = config('plugin.webman.gateway-worker.app.wss_port');
        self::$TEXT_PORT = config('plugin.webman.gateway-worker.app.text_port');
        self::$MAIN_CODE = config('plugin.webman.gateway-worker.app.super_code');
        self::$SUPER_ADMIN_ID = config('plugin.webman.gateway-worker.app.super_admin_id');
    }

    public static function onMessage($client_id, $message)
    {
        try {
            if ($message=="0000"){
                return  false;
            }
            if ($_SERVER['GATEWAY_PORT'] == self::$UDP_PORT){
                var_dump(123133);
            }
            //TCP协议 websocket
            if (in_array($_SERVER['GATEWAY_PORT'], [self::$TCP_PORT, self::$WS_PORT, self::$WSS_PORT])) {
                //首先判断该clientId是否第一次链接
                $uid = $_SESSION ? $_SESSION['uid'] : null;

                //第一次连接
                if (!$uid) {
                    $type = LoginServices::judgeLogin($message);
                    if ($type == "admin") {
                        LoginServices::adminLogin($client_id, $message);
                        return true;
                    }
                    if ($type == "device") {
                        $message = LoginServices::getIotKey($message);
                        if ($message === false) return true;
                    }

                    if (RedisServices::isGatewayRedis($message)) {
                        $my = RedisServices::getGatewayRedis($message);
                        //单点登录还是多点登录
                        $myUid = $my['uid'];
                        //单点登录
                        if ((int)$my['login'] == 0) {
                            //查询所有客户端，先把其他的连接踢掉
                            $allClientId = Gateway::getClientIdByUid($myUid);
                            if ($allClientId) {
                                foreach ($allClientId as $k => $v) {
                                    Gateway::closeClient($v);
                                }
                            }
                        }
                        //绑定uid，上线
                        Gateway::bindUid($client_id, $myUid);
                        //存个session
                        $_SESSION['uid'] = $myUid;
                        //是否有自定义回复包，如果有的话，回复
                        $myRecode = $my['recode'];
                        if ($myRecode) {
                            //ASCII类型
                            if ($my['rtype'] == 0) {
                                Gateway::sendToClient($client_id, $myRecode);
                            }
                            //HEX类型
                            if ($my['rtype'] == 1) {
                                Gateway::sendToClient($client_id, self::myHexTobin($myRecode));
                            }
                        }
                        //kd 定时器
                        TimeServices::createTimer($client_id, $message,$my);
                        //推送在线状态
                        self::pushDeviceIsOnlineToMember($message, self::isOnlineByUid($message));
                        return true;
                    }
                    //设备不存在
                    Gateway::sendToClient($client_id, 'device not exist');
                    Gateway::closeClient($client_id);
                    return true;
                } else {
                    $myKey = str_replace('-Uid', '', $uid);
                    //TCP协议
                    if ($_SERVER['GATEWAY_PORT'] == self::$TCP_PORT) {
                        if (SortingDataServices::isAscii($message)) {
                            $message = bin2hex($message);
                        }
                        $gateway = RedisServices::getGatewayRedis($myKey);
                        $result=SortingDataServices::receive($message, $myKey,$gateway);
                        if (!$result){
                            SortingDataServices::sendGateway($gateway,$myKey,$message);
                        }
                    }
                    //被动回复
                    $directive = RedisServices::getGatewayDirective($myKey);
                    if (is_array($directive) && count($directive)) {
                        foreach ($directive as $k => $v) {
                            if (!trim($v['sval'])) {
                                continue;
                            }
                            //触发指令 //HEX类型
                            if ($v['stype'] == 1) {
                                $dirStr1 = strtoupper(bin2hex($message));
                            } else {
                                $dirStr1 = strtoupper($message);
                            }
                            //如果收到的消息==触发指令
                            if ($dirStr1 == strtoupper($v['sval'])) {
                                //回复指令//HEX类型
                                if ($v['rtype'] == 1) {
                                    Gateway::sendToClient($client_id, self::myHexTobin($v['rval']));
                                } else {
                                    Gateway::sendToClient($client_id, $v['rval']);
                                }
                                break;
                            }
                        }
                    }
                    //webSocket协议
                    if ($_SERVER['GATEWAY_PORT'] == self::$WS_PORT || $_SERVER['GATEWAY_PORT'] == self::$WSS_PORT) {
                        $message = jsonDecode($message, true);
                        if ($message) {
                            var_dump("websocket任务添加；".json_encode($message));
                            if (Gateway::isUidOnline($message["to"] . '-Uid')){
                                RedisServices::addExecuteList($message['to'], SortingDataServices::$WebSocket, ["command"=>$message['val'],"type"=>$message['type'],"eol"=>$message['eol']]);
                            }
//                            Gateway::sendToUid($message['to'] . '-Uid', $val . ($message['eol'] == 1 ? PHP_EOL : ''));
                        }
                    }
                }
            }
            //Text协议
            if ($_SERVER['GATEWAY_PORT'] == self::$TEXT_PORT) {
                $message = jsonDecode($message, true);
                if (!$message) {
                    return true;
                }
                $verification_status=RedisServices::verificationTextPortToken($message["token"]);
                if(!$verification_status){//验证未通过
                    return true;
                }
                //如果是super code
                $message['action'] = !isset($message['action']) ? 'sendMsg' : $message['action'];
                switch ($message['action']) {
                    case 'sendMsg':
                        if (Gateway::isUidOnline($message["to"] . '-Uid')){
                            Gateway::sendToUid($message['to'] . '-Uid', $message["val"] );
                        }
                        break;
                    case 'pushMqttDeviceOnline':
                        self::pushDeviceIsOnlineToMember($message['code'], 1);
                        break;
                    case 'pushMqttDeviceOffline':
                        self::pushDeviceIsOnlineToMember($message['code'], 0);
                        break;
                    case 'closeClient':
                        self::closeClientByUid($message['to']);
                        //推送下线
                        self::pushDeviceIsOnlineToMember($message['from'], 0);
                        break;
                    default:
                        break;
                }
            }
        } catch (\Throwable $e) {
            var_dump("异常：" . "file：" . $e->getFile() . " error：" . $e->getMessage() . " line：" . $e->getLine());
//            Gateway::closeClient($client_id);
            Log::info('onMessage error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
                'message' => $message
            ]);
        }
    }

    /**
     * Undocumented function 客户端断开连接
     *
     * @param [type] $client_id 客户端id
     * @return void
     */
    public static function onClose($client_id)
    {
        //需要测试删除定时器
        if (isset($_SESSION['timerId'])) {
            Timer::del($_SESSION['timerId']);
        }
//        if (isset($_SESSION['timerDefaultId'])) {
//            Timer::del($_SESSION['timerDefaultId']);
//        }
        if (isset($_SESSION['uid'])) {
            $myUid = $_SESSION['uid'];
            $myCode = str_replace('-Uid', '', $myUid);
            if (self::isOnlineByUid($myCode)==0){
                self::pushDeviceIsOnlineToMember($myCode, 0);
            }
        }
    }

    /**
     * Undocumented function HEX转bin
     *
     * @param [type] $str HEX字符串
     * @return string bin字符串
     */
    public static function myHexTobin($str)
    {
        $res = '';
        //去除空格
        $str = str_replace([' ', PHP_EOL, '　'], '', $str);
        if (!$str) {
            return $res;
        }
        if (strlen($str) % 2 != 0) {
            return $str;
        }
        return hex2bin($str);
    }

    /**
     * Undocumented function 设备上线下线的时候推送给最高权限
     *
     * @param [type] $code 设备信息
     * @param [type] $status 1上线,0下线
     * @return void
     */
    public static function pushDeviceIsOnlineToMember($code, $status)
    {
        GatewayModel::saveGatewayOnline($code, $status);
        $gateway=RedisServices::getGatewayRedis($code);
        GatewayOnlineLogServices::saveOnlineLog($gateway,$status);
        if (!empty($gateway['admin_id'])){
            Gateway::sendToUid($gateway['admin_id'] . '-Uid', json_encode([
                'k' => $code,
                'v' => ["type" => "Online", "code" => 200, "data" => ['online' => $status]],
                't' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

    }

    /**
     * Undocumented function 关闭指定uid的所有客户端
     *
     * @param [type] $uid uid
     * @return void
     */
    public static function closeClientByUid($uid)
    {
        if (is_array($uid)) {
            foreach ($uid as $v) {
                self::closeClient($v);
            }
        } else {
            self::closeClient($uid);
        }
    }

    /**
     * 关闭连接
     * @param $uid
     * @return void
     */
    public static function closeClient($uid)
    {
        //查询所有客户端
        $allClientId = Gateway::getClientIdByUid($uid . '-Uid');
        if ($allClientId) {
            foreach ($allClientId as $k => $v) {
                Gateway::closeClient($v);
            }
        }
    }

    /**
     * 根据uid判断设备是否在线
     *
     * @param [type] $code 设备code
     * @return int 1在线,0不在线
     */
    public static function isOnlineByUid($code)
    {
        //本方法可能用于控制器,所以需要设置registerAddress
        Gateway::$registerAddress = config('plugin.webman.gateway-worker.process.worker.constructor.config.registerAddress');
        return Gateway::isUidOnline($code . '-Uid');
    }
}
