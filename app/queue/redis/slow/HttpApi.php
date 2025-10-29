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


namespace app\queue\redis\slow;

use app\model\Iot;
use app\model\WebhookLogs;
use support\Log;
use think\facade\Db;
use Webman\RedisQueue\Consumer;
use \Workerman\Http\Client;

//请求第三方api队列
class HttpApi implements Consumer
{
    // 要消费的队列名
    public $queue = '';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';
    //日志文件
    public $logFile = 'queue-slow-HttpApi';

    public function __construct()
    {
        $this->queue = 'iots_redis_queue_http_api';
    }
    public function consume($data)
    {
        //组合日志数据
        $logs = [];
        $logs['id'] = genreId('http_logs');
        $logs['url'] = $data['url'];
        $param = [];
        if ($data['type'] == 2) {
            $param = [
                'topic' => $data['topic'],
                'msg' => $data['msg'],
                'time' => $data['time']
            ];
        } else {
            $param = [
                'vtype' => $data['vtype'],
                'msg' => $data['msg'],
                'from' => $data['from'],
                'time' => $data['time']
            ];
        }
        $logs['param'] = json_encode($param, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $logs['year'] = date('Y', strtotime($data['time']));
        $logs['month'] = date('m', strtotime($data['time']));
        $logs['day'] = date('d', strtotime($data['time']));
        $logs['date'] = date('Y-m-d', strtotime($data['time']));
        $logs['addtime'] = $data['time'];
        $logs['del'] = 0;
        $logs['del_id'] = 0;
        //开启事务
        Db::startTrans();
        try {
            //判断$url是否合法
            if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
                $logs['status'] = 1;
                $logs['result'] = 'url不合法';
                WebhookLogs::create($logs);
                Db::commit();
                Log::channel($this->logFile)->info('HttpApi消费捕获异常: ' . 'url不合法', ['data' => $data]);
                return;
            }
            $device = Iot::where([['code', '=', $data['from']], ['del', '=', 0]])->findOrEmpty()->toArray();
            if (!$device) {
                $logs['status'] = 1;
                $logs['result'] = '设备不存在';
                WebhookLogs::create($logs);
                Db::commit();
                Log::channel($this->logFile)->info('HttpApi消费捕获异常: ' . '设备不存在', ['data' => $data]);
                return;
            } else {
                $logs['device_id'] = $device['id'];
            }
            $http = new Client();
            $http->request($data['url'], [
                'method'  => 'POST',
                'version' => '1.1',
                'headers' => ['Connection' => 'keep-alive'],
                'data'    => $param,
                'success' => function ($response) use ($logs) {
                    $logs['status'] = 0;
                    $logs['msg'] = $response->getBody()->getContents();
                    WebhookLogs::create($logs);
                    Db::commit();
                },
                'error'   => function ($exception) use ($logs) {
                    $logs['status'] = 1;
                    $logs['msg'] = $exception->getMessage();
                    $logs['exception'] = $exception->getTraceAsString();
                    WebhookLogs::create($logs);
                    Db::commit();
                    // z('error', $exception);
                }
            ]);
        } catch (\Throwable $e) {
            Db::rollback();
            $logs['status'] = 1;
            $logs['msg'] = $e->getMessage();
            $logs['exception'] = $e->getTraceAsString();
            WebhookLogs::create($logs);
            Log::channel($this->logFile)->info('HttpApi消费捕获异常: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
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
            Log::channel($this->logFile)->info('HttpApi消费失败回调,达到最大重试次数: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'package' => $package,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
