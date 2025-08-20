<?php


namespace process;

use app\model\Iot;
use app\services\gateway\GatewayServices;
use extend\RedisQueue;
use support\Log;
use Workerman\Connection\TcpConnection;
use Workerman\Timer;
use Workerman\Worker;

/**
 * Class MqttClient mqtt客户端,用于处理mqtt设备的订阅和发布
 * 修改后需要restart重启服务
 * @package process
 */
class MqttClient
{
    //mqtt客户端实例
    public static $mqtt = null;
    //http客户端实例
    public static $http = null;
    //topic-iot数组,key为topic,value为iot数组,用来根据topic找到对应设备
    public static $topic_iot = [];
    //定时器id数组
    public static $timerIds = [];
    public function __construct()
    {
        date_default_timezone_set('PRC');
    }
    public function onWorkerStart(Worker $worker)
    {
        //启动mqtt客户端
        self::$mqtt = new \Workerman\Mqtt\Client(
            'mqtt://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.mqtt_port'),
            [
                'username' => config('plugin.webman.gateway-worker.app.mqtt_client_username'), //用户名
                'password' => config('plugin.webman.gateway-worker.app.mqtt_client_password'), //密码
                'client_id' => config('plugin.webman.gateway-worker.app.mqtt_client_id'), //客户端id
                'clean_session' => true, //是否清除会话
                'keepalive' => 60, //心跳时间
            ]
        );
        self::$mqtt->onConnect = function ($mqtt) {
            $iot = Iot::field('id,code,type,username,password,topic,http,log,forward,crontab')
                ->where([['type', '=', 2], ['del', '=', 0]])
                ->select()->toArray();
            foreach ($iot as $k => $v) {
                self::start($v);
            }
        };
        self::$mqtt->onMessage = function ($topic, $content) {
            //处理mqtt消息
            self::handleMessage($topic, $content);
        };
        self::$mqtt->connect();
        //启动http客户端
        self::$http = new  \Workerman\Http\Client();
    }
    public function onMessage(TcpConnection $connection, $data)
    {
        //接收指令
        if (self::check($data)) {
            $data = json_decode($data, true);
            switch ($data['type']) {
                case 'start':
                    self::start($data['iot']);
                    break;
                case 'destroy':
                    self::destroy($data['iot']);
                    break;
                case 'disconnect':
                    self::disconnect($data['iot']['code']);
                    break;
                case 'publish':
                    self::publish($data['iot']['topic'], $data['iot']['content']);
                    break;
                default:
                    break;
            }
            // var_dump($data);

            return $connection->send('ok');
        }
    }

    public function onClose(TcpConnection $connection) {}
    /**
     * @description: 校验是否合法
     * @param 接收到的字符串
     * @return {*}
     * @author: suiyu
     */
    public static function check($data)
    {
        $data = jsonDecode($data, true);
        if (!isset($data['type']) || !isset($data['iot']) || !isset($data['sign']) || !isset($data['iot']['id'])) {
            return false;
        }
        if ($data['sign'] == md5($data['type'] . $data['iot']['id'] . config('plugin.webman.gateway-worker.app.mqtt_client_id'))) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @description:启动mqtt设备
     * @param $iot iot设备信息
     * @return {*}
     * @author: suiyu
     */
    public static function start($iot)
    {
        //订阅主题
        $topics = array_filter(array_unique(explode(',', $iot['topic'])));
        foreach ($topics as $topic) {
            //添加到topic-iot数组
            self::$topic_iot[$topic][] = $iot;
            self::$mqtt->subscribe($topic);
        }
        //订阅指定 client_id 的上线和下线事件
        self::$mqtt->subscribe('$SYS/brokers/+/clients/' . $iot['code'] . '/connected');
        self::$mqtt->subscribe('$SYS/brokers/+/clients/' . $iot['code'] . '/disconnected');
        //更新设备在线状态
        self::updateOnlineByApi($iot['code']);
        //判断是否有定时下发
        if (isset($iot['crontab']) && $iot['crontab'] != '') {
            $crontab = jsonDecode($iot['crontab'], true);
            self::crontab($iot, $crontab);
        }
    }
    /**
     * @description: 定时下发
     * @param $iot iot设备信息
     * @param $crontab 定时下发数组
     * @return {*}
     * @author: suiyu
     */
    public static function crontab($iot, $crontab)
    {
        if ($crontab['status'] == 0) {
            return false;
        }
        if ($crontab['val'] == '') {
            return false;
        }
        if (!isset(GatewayServices::CRONTAB_TIMES_LIST[$crontab['times']])) {
            return false;
        }
        //定时下发
        $timerId = Timer::add(GatewayServices::CRONTAB_TIMES_LIST[$crontab['times']], function () use ($iot, $crontab) {
            try {
                $publishTopic = array_filter(array_unique(explode(',', $crontab['publishTopic'])));
                foreach ($publishTopic as $topic) {
                    self::$mqtt->publish($topic, $crontab['val']);
                }
            } catch (\Throwable $e) {
                Log::channel('MqttClient')->error('定时下发失败：' . $e->getMessage(), [
                    'client_id' => $iot['code'],
                    'publishTopic' => $crontab['publishTopic'],
                    'val' => $crontab['val'],
                    'exception' => $e->getTraceAsString(),
                ]);
            }
        });
        self::$timerIds[$iot['code']] = $timerId;
    }
    /**
     * @description: 销毁mqtt设备
     * @param $iot iot设备信息
     * @return {*}
     * @author: suiyu
     */
    public static function destroy($iot)
    {
        //删除topic-iot数组中对应的设备
        $topics = array_filter(array_unique(explode(',', $iot['topic'])));
        foreach ($topics as $topic) {
            $iots = self::$topic_iot[$topic] ?? [];
            foreach ($iots as $k => $v) {
                if ($v['code'] == $iot['code']) {
                    unset(self::$topic_iot[$topic][$k]);
                }
            }
            if (empty(self::$topic_iot[$topic])) {
                unset(self::$topic_iot[$topic]);
                self::$mqtt->unsubscribe($topic);
            }
        }
        //取消订阅
        self::$mqtt->unsubscribe('$SYS/brokers/+/clients/' . $iot['code'] . '/connected');
        self::$mqtt->unsubscribe('$SYS/brokers/+/clients/' . $iot['code'] . '/disconnected');
        //更新设备在线状态
        self::updateOnlineByApi($iot['code']);
        //取消定时器
        if (isset(self::$timerIds[$iot['code']])) {
            Timer::del(self::$timerIds[$iot['code']]);
            unset(self::$timerIds[$iot['code']]);
        }
    }
    /**
     * @description: 通过api更新设备在线状态
     * @param $client_id 客户端id
     * @return {*}
     * @author: suiyu
     */
    public static function updateOnlineByApi($client_id)
    {
        try {
            $url = config('plugin.webman.gateway-worker.app.mqtt_client_api_url') . 'api/v5/clients/' . urlencode($client_id);
            self::$http->request($url, [
                'method'  => 'GET',
                'version' => '1.1',
                'headers' => [
                    'Connection' => 'keep-alive',
                    'Authorization' => 'Basic ' . base64_encode(config('plugin.webman.gateway-worker.app.mqtt_client_api_key') . ':' .
                        config('plugin.webman.gateway-worker.app.mqtt_client_api_secret')),
                ],
                'success' => function ($response) use ($client_id) {
                    $data = json_decode($response->getBody()->getContents(), true);
                    // z($data);
                    //判断是否在线
                    if (isset($data['connected']) && $data['connected'] == true) {
                        //设置设备在线
                        self::setOnline($client_id, 1);
                    } else {
                        //设置设备离线
                        self::setOnline($client_id, 0);
                    }
                },
                'error'   => function ($exception) use ($client_id) {
                    Log::channel('MqttClient')->error($exception->getMessage(), [
                        'client_id' => $client_id,
                        'exception' => $exception->getTraceAsString(),
                    ]);
                }
            ]);
        } catch (\Throwable $e) {
            Log::channel('MqttClient')->error($e->getMessage(), [
                'client_id' => $client_id,
                'exception' => $e->getTraceAsString(),
            ]);
        }
    }
    /**
     * 设置设备在线状态
     *
     * @param string $client_id 客户端id
     * @param int $online 在线状态 1在线 0离线
     * @return void
     */
    public static function setOnline($client_id, $online)
    {
        $redis = myRedis();
        $redis->hSet('HFiots-online', $client_id, $online);
        if ($online == 1) {
            $redis->hSet('HFiots-last-online', $client_id, date('Y-m-d H:i:s'));
        }
        //推送会员告知设备上线或离线
        $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
        if (!$client) {
            Log::channel('MqttClient')->error('推送会员告知设备上线或离线失败，stream_socket_client连接失败', [
                'client_id' => $client_id,
                'online' => $online,
            ]);
            return false;
        }
        $my = [
            'from' => config('plugin.webman.gateway-worker.app.super_code'),
            'action' => $online == 1 ? 'pushMqttDeviceOnline' : 'pushMqttDeviceOffline', //通知mqtt设备上线或离线
            'code' => $client_id, //上线的设备code
        ];
        fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    }
    /**
     * 处理mqtt消息
     *
     * @param string $topic 主题
     * @param string $content 内容
     * @return void
     */
    public static function handleMessage($topic, $content)
    {
        // z($topic);
        // z($content);
        if (strpos($topic, '/connected') !== false || strpos($topic, '/disconnected') !== false) {
            //获取client_id
            $content = jsonDecode($content, true);
            $client_id = $content['clientid'] ?? '';
            if (!$client_id) {
                return false;
            }
            //如果topic包含/connected,则设置设备在线
            if (strpos($topic, '/connected') !== false) {
                self::setOnline($client_id, 1);
            }
            //如果topic包含/disconnected,则设置设备离线
            if (strpos($topic, '/disconnected') !== false) {
                self::setOnline($client_id, 0);
            }
        }
        //判断是否是设备上报数据
        if (strpos($topic, '/connected') === false && strpos($topic, '/disconnected') === false) {
            //设备上报数据
            //根据topic找到对应设备
            $iots = self::$topic_iot[$topic] ?? [];
            foreach ($iots as $iot) {
                //添加队列存储数据
                if (isset($iot['log']) && $iot['log'] == 1) {
                    RedisQueue::queue('iots_redis_queue_device_logs', [
                        'type' => $iot['type'],
                        'from' => $iot['code'],
                        'topic' => $topic,
                        'msg' => $content,
                        'time' => date('Y-m-d H:i:s')
                    ]);
                }
                //该设备是否有http
                if (isset($iot['http']) && $iot['http'] != '') {
                    //需http发送,添加队列
                    $httpClient = array_filter(array_unique(explode(',', $iot['http'])));
                    foreach ($httpClient as $v) {
                        RedisQueue::queue('iots_redis_queue_http_api', [
                            'type' => $iot['type'],
                            'from' => $iot['code'],
                            'url' => $v,
                            'topic' => $topic,
                            'msg' => $content,
                            'time' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
                //如果有forward,那么转发
                if (isset($iot['forward']) && $iot['forward'] != '') {
                    $forward = array_filter(array_unique(explode(',', $iot['forward'])));
                    $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
                    if (!$client) {
                        // throw new \Exception('连接服务失败,请重试');
                        Log::channel('MqttClient')->error('转发其他设备失败，stream_socket_client连接失败', [
                            'client_id' => $iot['code'],
                            'forward' => $v,
                            'topic' => $topic,
                            'content' => $content,
                        ]);
                        continue;
                    }
                    foreach ($forward as $v) {
                        $my = [
                            'from' => config('plugin.webman.gateway-worker.app.super_code'),
                            'action' => 'sendMsg', //转发ws设备
                            'to' => $v, //转发的设备code
                            'type' => 0, //0:ASCII,1:HEX,2:GB2312
                            'val' => json_encode([
                                'k' => $iot['code'],
                                'v' => $content,
                                't' => date('Y-m-d H:i:s')
                            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), //消息内容
                            'eol' => 0 //0不加换行 1加换行
                        ];
                        fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
                    }
                }
                if ($content == 'disconnect') {
                    self::disconnect($iot['code']);
                }
            }
        }
        return true;
    }
    /**
     * 断开设备连接
     *
     * @param string $client_id 客户端id
     * @return void
     */
    public static function disconnect($client_id)
    {
        try {
            $url = config('plugin.webman.gateway-worker.app.mqtt_client_api_url') . 'api/v5/clients/' . urlencode($client_id);
            self::$http->request($url, [
                'method'  => 'DELETE',
                'version' => '1.1',
                'headers' => [
                    'Connection' => 'keep-alive',
                    'Authorization' => 'Basic ' . base64_encode(config('plugin.webman.gateway-worker.app.mqtt_client_api_key') . ':' .
                        config('plugin.webman.gateway-worker.app.mqtt_client_api_secret')),
                ],
                'success' => function ($response) use ($client_id) {
                    // z($client_id);
                },
                'error'   => function ($exception) use ($client_id) {
                    // z($exception);
                    Log::channel('MqttClient')->error($exception->getMessage(), [
                        'client_id' => $client_id,
                        'exception' => $exception->getTraceAsString(),
                    ]);
                }
            ]);
        } catch (\Throwable $e) {
            Log::channel('MqttClient')->error($e->getMessage(), [
                'client_id' => $client_id,
                'exception' => $e->getTraceAsString(),
            ]);
        }
    }
    /**
     * 发布mqtt消息
     *
     * @param string $topic 主题
     * @param string $content 内容
     * @return void
     */
    public static function publish($topic, $content)
    {
        try {
            self::$mqtt->publish($topic, $content);
        } catch (\Throwable $e) {
            Log::channel('MqttClient')->error('发布mqtt消息失败：' . $e->getMessage(), [
                'topic' => $topic,
                'content' => $content,
                'exception' => $e->getTraceAsString(),
            ]);
        }
    }
}
