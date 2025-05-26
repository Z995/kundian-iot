<?php
/*
 * @Author: duyingjie duyingjie@qq.com
 * @Date: 2023-01-29 15:05:32
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-03-10 21:16:45
 * @FilePath: \erpd:\phpstudy_pro\webman\app\queue\redis\MyMailSend.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\queue\redis\fast;

use app\model\Iot;
use extend\RedisQueue;
use Webman\RedisQueue\Consumer;
use support\Log;
use support\Redis;

class MqttApi implements Consumer
{
    // 要消费的队列名
    public $queue = '';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    //日志文件
    public $logFile = 'queue-fast-MqttApi';
    public function __construct()
    {
        $this->queue = 'iots_redis_queue_mqtt_api';
    }
    // 消费 
    // {"username":"test","timestamp":1721199847396,"sockname":"172.31.251.178:1883","reason":"takenover","proto_ver":3,"proto_name":"MQIsdp",
    // "peername":"36.113.29.209:59433","node":"emqx@127.0.0.1","metadata":{"rule_id":"241webhook_WH_D"},"event":"client.disconnected",
    // "disconnected_at":1721199847396,"disconn_props":{"User-Property":[]},"clientid":"02500524062400002415_01"}
    public function consume($param)
    {
        if (!isset($param['event']) || !isset($param['username']) || !isset($param['clientid'])) {
            return;
        }

        $redis = myRedis();
        try {
            $username = $param['username'];
            $clientid = $param['clientid'];
            $info = Iot::where([['username', '=', $username], ['code', '=', $clientid], ['del', '=', 0]])->findOrEmpty()->toArray();
            if (!$info) {
                Log::channel($this->logFile)->error('MqttApi消费失败：', ['package' => $param, 'error' => '设备不存在']);
                return;
            }
            switch ($param['event']) {
                    //设备连接
                case 'client.connack':
                    //获取上次的index
                    $lastCodeIndex = $redis->get('HFiots-Code-Index-' . $info['code']);
                    //如果有上次的index,并且当前的index小于等于上次的index,则不处理
                    if ($lastCodeIndex && $param['index'] <= $lastCodeIndex) {
                        return;
                    }
                    //设置sid,并且设置过期时间60s
                    $redis->setex('HFiots-Code-Index-' . $info['code'], $param['index'], 5);
                    //设置设备在线
                    $redis->hSet('HFiots-online', $info['code'], 1);
                    //设置最后一次在线时间
                    Redis::hSet('HFiots-last-online', $info['code'], date('Y-m-d H:i:s'));
                    //推送会员告知设备上线
                    $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
                    if (!$client) {
                        throw new \Exception('连接服务失败,请重试');
                    }
                    $my = [
                        'from' => config('plugin.webman.gateway-worker.app.super_code'),
                        'action' => 'pushMqttDeviceOnline', //通知mqtt设备上线
                        'code' => $info['code'], //上线的设备code
                    ];
                    fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
                    break;
                    //设备断开连接
                case 'client.disconnected':
                    //获取上次的index
                    $lastCodeIndex = $redis->get('HFiots-Code-Index-' . $info['code']);
                    //如果有上次的index,并且当前的index小于等于上次的index,则不处理
                    if ($lastCodeIndex && $param['index'] <= $lastCodeIndex) {
                        return;
                    }
                    //设置sid,并且设置过期时间60s
                    $redis->setex('HFiots-Code-Index-' . $info['code'], $param['index'], 5);
                    //设置设备离线
                    $redis->hDel('HFiots-online', $info['code']);
                    //推送会员告知设备离线
                    $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
                    if (!$client) {
                        throw new \Exception('连接服务失败,请重试');
                    }
                    $my = [
                        'from' => config('plugin.webman.gateway-worker.app.super_code'),
                        'action' => 'pushMqttDeviceOffline', //通知mqtt设备离线
                        'code' => $info['code'], //离线的设备code
                    ];
                    fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
                    break;
                    //发布消息
                case 'message.publish':
                    //添加队列存储数据
                    RedisQueue::queue('iots_redis_queue_device_logs', [
                        'type' => $info['type'],
                        'from' => $info['code'],
                        'topic' => $param['topic'],
                        'msg' => $param['payload'],
                        'time' => date('Y-m-d H:i:s')
                    ]);
                    //该设备是否有http
                    //需http发送,添加队列
                    $httpClient = array_filter(array_unique(explode(',', $info['http'])));
                    foreach ($httpClient as $v) {
                        RedisQueue::queue('iots_redis_queue_http_api', [
                            'type' => $info['type'],
                            'from' => $info['code'],
                            'url' => $v,
                            'topic' => $param['topic'],
                            'msg' => $param['payload'],
                            'time' => date('Y-m-d H:i:s')
                        ]);
                    }
                    //如果有forward,那么转发
                    $forward = array_filter(array_unique(explode(',', $info['forward'])));
                    foreach ($forward as $v) {
                        $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
                        if (!$client) {
                            throw new \Exception('连接服务失败,请重试');
                        }
                        $my = [
                            'from' => config('plugin.webman.gateway-worker.app.super_code'),
                            'action' => 'sendMsg', //转发ws设备
                            'to' => $v, //转发的设备code
                            'type' => 0, //0:ASCII,1:HEX,2:GB2312
                            'val' => json_encode([
                                'k' => $info['code'],
                                'v' => $param['payload'],
                                't' => date('Y-m-d H:i:s')
                            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), //消息内容
                            'eol' => 0 //0不加换行 1加换行
                        ];
                        fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
                    }
                    break;
            }
        } catch (\Throwable $e) {
            Log::channel($this->logFile)->error('MqttApi消费失败：', [
                'package' => $param,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * 消费失败回调
     * @param \Throwable $e
     * @param array $package 消息包
     * @return void
     */
    /* 
    $package = [
        'id' => 1357277951, // 消息ID
        'time' => 1709170510, // 消息时间
        'delay' => 0, // 延迟时间
        'attempts' => 2, // 消费次数
        'queue' => 'redis_queue_test_queue1', // 队列名
        'data' => ['content' => '2024-02-29 09:35:10'], // 消息内容
        'max_attempts' => 2, // 最大重试次数
        'error' => '错误信息' // 错误信息
    ]
    */
    public function onConsumeFailure(\Throwable $e, $package)
    {
        // 达到最大重试次数
        if ($package['attempts'] >= $package['max_attempts']) {
            Log::channel($this->logFile)->error('MqttApi消费失败：', ['package' => $package, 'error' => $e->getMessage()]);
        }
    }
}
// EMQX作为一个高性能的MQTT代理，提供了多种事件类型，用于监控和响应MQTT客户端与服务器之间的各种交互。以下是一些常见的EMQX事件类型及其含义：

// 1. **`client.connected`**: 客户端成功与EMQX服务器建立连接时触发。这个事件表明一个新的客户端连接已经建立，但还没有完成MQTT的`CONNACK`流程。

// 2. **`client.disconnected`**: 客户端与EMQX服务器断开连接时触发。这个事件表明一个客户端连接已经关闭。

// 3. **`client.subscribe`**: 客户端发送订阅请求时触发。这个事件表明客户端正在尝试订阅一个或多个主题。

// 4. **`client.unsubscribe`**: 客户端发送取消订阅请求时触发。这个事件表明客户端正在尝试取消订阅一个或多个主题。

// 5. **`session.created`**: 当一个新的会话被创建时触发。这通常发生在客户端首次连接并创建一个新的会话时。

// 6. **`session.subscribed`**: 当会话中添加了新的订阅时触发。

// 7. **`session.unsubscribed`**: 当会话中的订阅被取消时触发。

// 8. **`session.terminated`**: 当会话被终止时触发。这可能是由于客户端断开连接或会话过期。

// 9. **`message.publish`**: 当消息被发布到服务器时触发。这个事件表明有新的消息被发送到一个主题。

// 10. **`message.delivered`**: 当消息被投递给至少一个订阅者时触发。这个事件表明消息已经成功送达至少一个客户端。

// 11. **`message.acked`**: 当消息被订阅者确认时触发。这个事件表明QoS 1或QoS 2的消息已经被接收方确认。

// 12. **`client.connack`**: 当服务器发送`CONNACK`响应给客户端时触发。这个事件表明客户端的连接请求已经被服务器处理，并且客户端已经成功连接到服务器。

// 这些事件为EMQX提供了强大的监控和响应能力，使得开发者可以在客户端和服务器之间的交互发生时执行自定义逻辑。请注意，EMQX可能还支持其他事件，具体取决于EMQX的版本和配置。