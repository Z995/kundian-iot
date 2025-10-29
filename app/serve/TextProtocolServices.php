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