<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-02-24 08:30:53
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-03-25 17:36:14
 * @FilePath: \yunxiao-app-iot\plugin\webman\gateway\Events.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace plugin\webman\gateway;

use app\model\Iot;
use app\services\RedisServices;
use extend\RedisQueue;
use GatewayWorker\Lib\Gateway;
use support\Log;
use support\Redis;
use Workerman\Timer;

class Events
{
    static $TCP_PORT = ''; //tcp端口
    static $WS_PORT = ''; //ws端口
    static $WSS_PORT = ''; //wss端口
    static $TEXT_PORT = ''; //TEXT端口
    static $MAIN_CODE = ''; //超级权限uid
    public static function onWorkerStart($worker)
    {
        self::$TCP_PORT = config('plugin.webman.gateway-worker.app.tcp_port');
        self::$WS_PORT = config('plugin.webman.gateway-worker.app.ws_port');
        self::$WSS_PORT = config('plugin.webman.gateway-worker.app.wss_port');
        self::$TEXT_PORT = config('plugin.webman.gateway-worker.app.text_port');
        self::$MAIN_CODE = config('plugin.webman.gateway-worker.app.super_code');
    }
    public static function onMessage($client_id, $message)
    {
        try {
            //TCP协议 websocket
            if (in_array($_SERVER['GATEWAY_PORT'], [self::$TCP_PORT, self::$WS_PORT, self::$WSS_PORT])) {
                //首先判断该clientId是否第一次链接
                $uid = $_SESSION ? $_SESSION['uid'] : null;

                //第一次连接
                if (!$uid) {
                    //如果是MAIN_CODE,那么给他绑定uid
                    if ($message == self::$MAIN_CODE) {
                        if (!in_array($_SERVER['GATEWAY_PORT'], [self::$WS_PORT, self::$WSS_PORT])) {
                            Gateway::sendToClient($client_id, 'connect fail,please use ws protocol or wss protocol');
                            Gateway::closeClient($client_id);
                            return;
                        }
                        $myUid = self::$MAIN_CODE . '-Uid';
                        Gateway::bindUid($client_id, $myUid);
                        Gateway::sendToClient($client_id, 'connet success');
                        //存个session
                        $_SESSION['uid'] = $myUid;
                        return;
                    }
                    //有些设备的注册包是HEX类型,所以需要转换
                    if (!Redis::hExists('HFiots', $message)) {
                        $original_message=$message;
                        $message = bin2hex($message);

                        if (!Redis::hExists('HFiots', $message)){ //重新缓存
                            $iot = Iot::where([['code', '=',$original_message]])->find()->toArray();
                            if (!$iot){
                                $iot = Iot::where([['code', '=',$message]])->find()->toArray();
                            }else{
                                $message=$original_message;
                            }
                            if (!empty($iot)&&$iot['type'] != 2) {
                                $redis=new RedisServices();
                                $redis->setHFiotsRedis($iot);
                                $redis->setHFiotsFilterRedis($iot['code'], $iot["filter"]);
                            }
                        }

                    }


                    if (Redis::hExists('HFiots', $message)) {
                        $my = jsonDecode(Redis::hGet('HFiots', $message), true);
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
                        //定时器
                        //定时任务是否存在
                        $crontab = jsonDecode(Redis::hGet('HFiots-crontab', $message), true);
                        if ($crontab && $crontab['val'] && $crontab['status'] == 1) {
                            //1 => 1秒1次, 2 => 0.5秒1次, 3 => 60秒1次, 4 => 30秒1次, 5 => 3秒1次, 6 => 10秒1次, 7 => 10分钟一次, 8 => 60分钟1次
                            $timeArr = array(1 => 1, 2 => 0.5, 3 => 60, 4 => 30, 5 => 3, 6 => 10, 7 => 600, 8 => 3600);
                            if (isset($timeArr[$crontab['times']])) {
                                $_SESSION['timerId'] = Timer::add($timeArr[$crontab['times']], function () use ($client_id, $crontab, $my) {
                                    //数据来源,自定义数据类型,指令是按顺序依次发送,防止指令频繁发送导致设备无法响应
                                    //自定义数据
                                    if ($crontab['type'] == 0) {
                                        $rval = explode(',', $crontab['val']);
                                        if ($rval) {
                                            $rkey = 0; //当前应该发送的排序
                                            $myMessage = str_replace('-Uid', '', $my['uid']);
                                            if (Redis::exists('HFiotskey-' . $myMessage)) {
                                                $rkey = Redis::get('HFiotskey-' . $myMessage);
                                                if ($rkey > count($rval) - 1) {
                                                    $rkey = 0;
                                                }
                                            }
                                            $va = $crontab['vtype'] == 0 ? $rval[$rkey] : self::myHexTobin($rval[$rkey]);
                                            Gateway::sendToClient($client_id, $va);
                                            $rkey += 1;
                                            Redis::set('HFiotskey-' . $myMessage, $rkey);
                                        }
                                    }
                                    //redis队列
                                    if ($crontab['type'] == 1) {
                                        //内容
                                        $myVal = explode(',', $crontab['val']);
                                        if ($myVal) {
                                            ////查看本连接是TCP还是websocket,如果TCP,那么依次透传.如果是websocket,那么组成数组后传输.
                                            //发送的内容
                                            $res = array();
                                            foreach ($myVal as $ke => $va) {
                                                $myRes = '';
                                                $nva = explode(':', $va); //根据:分割数组
                                                //string类型
                                                if (count($nva) == 1) {
                                                    $myRes = Redis::get($nva[0]);
                                                }
                                                //list类型
                                                if (count($nva) == 2) {
                                                    $myRes = Redis::hGet($nva[0], $nva[1]);
                                                }
                                                //HEX类型
                                                if ($crontab['vtype'] == 1) {
                                                    $myRes = self::myHexTobin($myRes);
                                                }
                                                //TCP
                                                if ($my['type'] == 0) {
                                                    Gateway::sendToClient($client_id, $myRes);
                                                }
                                                //websocket
                                                if ($my['type'] == 1) {
                                                    $res[] = array($va => $myRes);
                                                }
                                            }
                                            //websocket
                                            if ($my['type'] == 1) {
                                                $arr = array('k' => 'crontab', 'v' => $res, 't' => date('Y-m-d H:i:s'));
                                                Gateway::sendToClient($client_id, json_encode($arr, JSON_UNESCAPED_UNICODE));
                                            }
                                        }
                                    }
                                });
                            }
                        }
                        //增加一个默认的定时器,用于给TCP客户端发送消息,使用redis队列,每秒执行一次
                        if ($_SERVER['GATEWAY_PORT'] == self::$TCP_PORT) {
                            //先清空之前的记录
                            Redis::del('HFiots-' . $message . '-Default-Crontab');
                            $_SESSION['timerDefaultId'] = Timer::add(1, function () use ($client_id, $message) {
                                $myVal = Redis::lindex('HFiots-' . $message . '-Default-Crontab', 0);
                                if ($myVal) {
                                    $myVal = json_decode($myVal, true);
                                    if (is_array($myVal)) {
                                        $myRes = $myVal['val'];
                                        ////查看本连接是TCP还是websocket,如果TCP,那么依次透传.如果是websocket,那么组成数组后传输.
                                        //发送的内容//HEX类型
                                        if ($myVal['vtype'] == 1) {
                                            $myRes = self::myHexTobin($myRes);
                                        }
                                        if ($myRes) {
                                            Gateway::sendToClient($client_id, $myRes);
                                        }
                                    }
                                    Redis::lPop('HFiots-' . $message . '-Default-Crontab');
                                }
                            });
                        }
                        //设置在线状态
                        Redis::hSet('HFiots-online', $message, 1);
                        //推送在线状态
                        self::pushDeviceIsOnlineToMember($message, 1);

                        return;
                    }
                    //设备不存在
                    Gateway::sendToClient($client_id, 'device not exist');
                    Gateway::closeClient($client_id);
                    return;
                } else {
                    //TCP协议
                    if ($_SERVER['GATEWAY_PORT'] == self::$TCP_PORT) {
                        //查询该链接的UID
                        $myStr = '';
                        $myKey = str_replace('-Uid', '', $uid);
                        $my = jsonDecode(Redis::hGet('HFiots', $myKey), true);
                        //存储数据 HEX类型
                        if ($my['vtype'] == 1) {
                            $myStr = bin2hex($message);
                        } else {
                            $myStr = $message;
                        }
                        //数据过滤
                        $ok = self::checkFilter($myKey, $myStr);
                        if ($ok) {
                            //需要存储
                            if ($my['val']) {
                                $myVal = explode(':', $my['val']);
                                //string类型
                                if (count($myVal) == 1) {
                                    Redis::set($my['val'], $myStr);
                                    //记录时间
                                    Redis::set($my['val'] . '-time', date('Y-m-d H:i:s'));
                                }
                                //list类型
                                if (count($myVal) == 2) {
                                    $myArr = jsonDecode($myStr, true);
                                    if ($myArr) {
                                        foreach ($myArr as $k => $v) {
                                            $childArr = jsonDecode($v, true);
                                            if ($childArr) {
                                                $myArr[$k] = $childArr;
                                            }
                                        }
                                    }
                                    $myArr = $myArr ? $myArr : $myStr;
                                    $arr = array('val' => $myArr, 'time' => date('Y-m-d H:i:s'));
                                    Redis::rpush($myVal[1], json_encode($arr, JSON_UNESCAPED_UNICODE));
                                }
                            }
                            //客户端需转发
                            $forward = explode(',', $my['forward']);

                            $forward[] = self::$MAIN_CODE; //web前端链接
                            $uidArray = [];
                            //获取对应的clientId
                            foreach ($forward as $k => $v) {
                                if (!empty($v)){
                                    $uidArray[] = $v . '-Uid';
                                }
                            }
                            $arr = array('k' => $myKey, 'v' => $myStr, 't' => date('Y-m-d H:i:s'));
                            Gateway::sendToUid($uidArray, json_encode($arr, JSON_UNESCAPED_UNICODE));
                            //需http发送,添加队列
                            $httpClient = array_filter(array_unique(explode(',', $my['http'])));
                            foreach ($httpClient as $v) {
                                RedisQueue::queue('iots_redis_queue_http_api', [
                                    'type' => $my['type'],
                                    'url' => $v,
                                    'vtype' => $my['vtype'],
                                    'msg' => $myStr,
                                    'from' => $myKey,
                                    'time' => date('Y-m-d H:i:s')
                                ]);
                            }
                            //添加队列存储数据
                            $my['log'] = $my['log'] ?? 1;
                            if ($my['log'] == 1) {
                                RedisQueue::queue('iots_redis_queue_device_logs', [
                                    'type' => $my['type'],
                                    'from' => $myKey,
                                    'vtype' => $my['vtype'],
                                    'msg' => $myStr,
                                    'time' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }
                    //被动回复
                    $directive = jsonDecode(Redis::hGet('HFiots-directive', str_replace('-Uid', '', $uid)), true);
                    if (is_array($directive) && count($directive)) {
                        foreach ($directive as $k => $v) {
                            if (!trim($v['sval'])) {
                                continue;
                            }
                            $dirStr1 = '';
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
                            $val = $message['val'];
                            //HEX类型
                            if (strtoupper($message['type']) == '1') {
                                $val = self::myHexTobin($val);
                            }
                            //GB2312
                            if (strtoupper($message['type']) == '2') {
                                $val = iconv("UTF-8", "gb2312//IGNORE", $val);
                            }
                            Gateway::sendToUid($message['to'] . '-Uid', $val . ($message['eol'] == 1 ? PHP_EOL : ''));
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
                //如果是super code
                if ($message['from'] == self::$MAIN_CODE) {
                    $message['action'] = !isset($message['action']) ? 'sendMsg' : $message['action'];
                    switch ($message['action']) {
                        case 'sendMsg':
                            $val = $message['val'];
                            //HEX类型
                            if (strtoupper($message['type']) == '1') {
                                $val = self::myHexTobin($val);
                            }
                            //GB2312
                            if (strtoupper($message['type']) == '2') {
                                $val = iconv("UTF-8", "gb2312//IGNORE", $val);
                            }
                            Gateway::sendToUid($message['to'] . '-Uid', $val . ($message['eol'] == 1 ? PHP_EOL : ''));
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
            }
        } catch (\Throwable $e) {
            Gateway::closeClient($client_id);
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
        if (isset($_SESSION['timerId'])) {
            Timer::del($_SESSION['timerId']);
        }
        if (isset($_SESSION['timerDefaultId'])) {
            Timer::del($_SESSION['timerDefaultId']);
        }
        if (isset($_SESSION['uid'])) {
            $myUid = $_SESSION['uid'];
            $myCode = str_replace('-Uid', '', $myUid);
            Redis::hDel('HFiots-online', $myCode);
            self::pushDeviceIsOnlineToMember($myCode, 0);
        }
    }
    /**
     * Undocumented function 数据过滤
     *
     * @param [type] $my 设备信息
     * @param [type] $message 设备code
     * @return integer 1通过过滤,0未通过
     */
    public static function checkFilter($myKey, $message)
    {
        //是否需要过滤
        $filter = jsonDecode(Redis::hGet('HFiots-filter', $myKey), true);
        $ok = 1; //1通过过滤,0未通过
        //需要过滤
        if ($filter && $filter['is'] == 1) {
            //字符长度
            if (in_array(0, $filter['type'])) {
                switch ($filter['lengType']) {
                    case '1': // >
                        if (strlen($message) <= $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    case '2': // ＜
                        if (strlen($message) >= $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    case '3': // ＝
                        if (strlen($message) != $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    case '4': // ≥
                        if (strlen($message) < $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    case '5': // ≤
                        if (strlen($message) > $filter['length']) {
                            $ok = 0;
                        }
                        break;

                    default:
                        # code...
                        break;
                }
            }
            //前N位
            if (in_array(1, $filter['type'])) {
                if (!in_array(substr($message, 0, $filter['before']), explode(',', $filter['beforeVal']))) {
                    $ok = 0;
                }
            }
            //忽略心跳包
            if (in_array(2, $filter['type'])) {
                if (in_array($message, explode(',', $filter['heartVal']))) {
                    $ok = 0;
                }
            }
        }
        return $ok;
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
        Gateway::sendToUid(self::$MAIN_CODE . '-Uid', json_encode([
            'k' => $code,
            'v' => ['online' => $status],
            't' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    /**
     * Undocumented function 关闭指定uid的所有客户端
     *
     * @param [type] $uid uid
     * @return void
     */
    public static function closeClientByUid($uid)
    {
        //查询所有客户端
        $allClientId = Gateway::getClientIdByUid($uid . '-Uid');
        if ($allClientId) {
            foreach ($allClientId as $k => $v) {
                Gateway::closeClient($v);
            }
        }
    }
}
