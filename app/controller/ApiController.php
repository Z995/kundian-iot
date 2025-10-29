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

namespace app\controller;

use app\model\DeviceLogs;
use app\model\Iot;
use app\model\Keys;
use extend\Request;
use support\Log;
use extend\RedisQueue;
use app\validate\api\ApiValidate;
use plugin\webman\gateway\Events;

class ApiController
{
    //给设备发送消息
    public function sendMsg()
    {
        $data = Request::param();
        //验证参数
        $validate = new ApiValidate();
        if (!$validate->scene('sendMsg')->check($data)) {
            return json(['code' => 1, 'msg' => $validate->getError()]);
        }
        //准备入库
        $device = Iot::where([['code', '=', $data['device_code']], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$device) {
            return json(['code' => 1, 'msg' => '设备自定义注册包不存在']);
        }
        //如果设备类型是mqtt,不让发送
        if ($device['type'] == 2) {
            return json(['code' => 1, 'msg' => '设备类型为MQTT,不支持发送消息']);
        }
        $member = Keys::where([['api_key', '=', $data['api_key']], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$member) {
            return json(['code' => 1, 'msg' => 'api_key不存在']);
        }
        //判断设备是否在线
        if (!Events::isOnlineByUid($data['device_code'])) {
            return json(['code' => 1, 'msg' => '设备不在线']);
        }
        try {
            $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
            if (!$client) {
                return ['code' => 1, 'msg' => '连接服务失败,请重试'];
            }
            //消息内容
            $msg = $data['msg'];
            $my = [
                'from' => config('plugin.webman.gateway-worker.app.super_code'),
                'to' => $data['device_code'],
                'type' => $data['vtype'], //0:ASCII,1:HEX,2:GB2312
                'eol' => $data['eol'], //不加换行
                'action' => 'sendMsg', //发送消息
                'val' => $msg //消息内容
            ];
            // Log::info('sendMsg-api', $my);
            fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
            return json(['code' => 0, 'msg' => '发送成功']);
        } catch (\Throwable $e) {
            Log::channel('ApiController')->error('发送失败,' . $e->getMessage(), [
                'data' => $data,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return json(['code' => 1, 'msg' => '发送失败,' . $e->getMessage()]);
        }
    }
    //获取设备数据日志的接口
    public function getDeviceData()
    {
        $data = Request::param();
        //验证参数
        $validate = new ApiValidate();
        if (!$validate->scene('getDeviceData')->check($data)) {
            return json(['code' => 1, 'msg' => $validate->getError()]);
        }
        //准备入库
        $device = Iot::where([['code', '=', $data['device_code']], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$device) {
            return json(['code' => 1, 'msg' => '设备自定义注册包不存在']);
        }
        $keys = Keys::where([['api_key', '=', $data['api_key']], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$keys) {
            return json(['code' => 1, 'msg' => 'api_key不存在']);
        }
        try {
            $map = [];
            $map[] = ['device_id', '=', $device['id']];
            $map[] = ['del', '=', 0];
            if (isset($data['start_time']) && $data['start_time']) {
                $map[] = ['addtime', '>=', date('Y-m-d H:i:s', $data['start_time'])];
            }
            if (isset($data['end_time']) && $data['end_time']) {
                $map[] = ['addtime', '<=', date('Y-m-d H:i:s', $data['end_time'])];
            }
            $count = DeviceLogs::where($map)
                ->order('id', 'asc')
                ->count();
            $page = (int) $data['page'];
            $size = (int) $data['size'];
            $field = '';
            if ($device['type'] == 2) {
                $field = 'val,addtime,topic';
            } else {
                $field = 'vtype,val,addtime';
            }
            $list = DeviceLogs::field($field)
                ->where($map)
                ->order('id', 'asc')
                ->limit(($page - 1) * $size, $size)
                ->select()->toArray();
            return json([
                'code' => 0,
                'msg' => '获取成功',
                'data' => [
                    'count' => $count,
                    'list' => $list
                ]
            ]);
        } catch (\Throwable $e) {
            Log::channel('ApiController')->error('获取失败,' . $e->getMessage(), [
                'data' => $data,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return json(['code' => 1, 'msg' => '获取失败,' . $e->getMessage()]);
        }
    }
    public function mqtt()
    {
        //安全校验请自行设计


        //获取请求参数
        $param = Request::param();
        //success,normal
        //not_authorized bad_username_or_password
        if (isset($param['reason_code']) && in_array($param['reason_code'], ['not_authorized', 'bad_username_or_password'])) {
            return $param['reason_code'];
        }
        $param['index'] = genreId('mqtt');
        //发送mqtt消息
        RedisQueue::queue('iots_redis_queue_mqtt_api', $param);
    }
    //获取设备在线状态
    public function getDeviceOnline()
    {
        $data = Request::param();
        //验证参数
        $validate = new ApiValidate();
        if (!$validate->scene('getDeviceOnline')->check($data)) {
            return json(['code' => 1, 'msg' => $validate->getError()]);
        }
        //查询设备信息
        $iot = Iot::where([['code', '=', $data['device_code']], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$iot) {
            return json(['code' => 1, 'msg' => '设备自定义注册包不存在']);
        }
        //查询api_key
        $keys = Keys::where([['api_key', '=', $data['api_key']], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$keys) {
            return json(['code' => 1, 'msg' => 'api_key不存在']);
        }
        //判断设备是否在线
        $online = 0;
        if ($iot['type'] == 2) {
            $redis = myRedis();
            $online = $redis->hGet('HFiots-online', $data['device_code']) ? 1 : 0;
        } else {
            $online = Events::isOnlineByUid($data['device_code']);
        }
        return json(['code' => 0, 'msg' => $online ? '设备在线' : '设备不在线', 'data' => $online]);
    }
}
