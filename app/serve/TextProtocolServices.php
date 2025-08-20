<?php

namespace app\serve;

use support\Redis;
use think\exception\ValidateException;

class TextProtocolServices
{
    /**
     * 主动踢下线设备
     * @param $code
     * @param $token
     * @return void
     */
    public function closeClient($code,$token){
        //踢下线
        $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
        $my = [
            'from' => $token,
            'to' => $code,
            'action' => 'closeClient', //发送消息
        ];
        fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    }

    /**
     * 发送消息
     * @param $admin_id
     * @param $val
     * @return array|void
     * @throws \Exception
     */
    public static function sendMsg($admin_id,$val){
        $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
        if (!$client) {
            return ['code' => 1, 'msg' => '连接服务失败,请重试'];
        }
        //消息内容
        $my = [
            'to' => $admin_id,
            'action' => "sendMsg", //发送消息
            'val' => $val, //消息内容
            'token' => RedisServices::createTextPortToken() //消息内容
        ];
        fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    }
}