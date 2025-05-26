<?php

namespace app\controller;

use app\model\DeviceLogs;
use app\model\Iot;
use app\model\Keys;
use extend\Request;
use support\Log;
use extend\RedisQueue;
use extend\Page;
use app\validate\api\ApiValidate;

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
        $redis = myRedis();
        if (!$redis->hExists('HFiots-online', $data['device_code'])) {
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
        $param = Request::param();
        //success,normal
        //not_authorized bad_username_or_password
        if (isset($param['reason_code']) && in_array($param['reason_code'], ['not_authorized', 'bad_username_or_password'])) {
            return $param['reason_code'];
        }
        $param['index'] = genreId('mqtt');
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
        //准备入库
        $iot = Iot::where([['code', '=', $data['device_code']], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$iot) {
            return json(['code' => 1, 'msg' => '设备自定义注册包不存在']);
        }
        $keys = Keys::where([['api_key', '=', $data['api_key']], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$keys) {
            return json(['code' => 1, 'msg' => 'api_key不存在']);
        }
        //判断设备是否在线
        $redis = myRedis();
        if ($redis->hExists('HFiots-online', $data['device_code'])) {
            return json(['code' => 0, 'msg' => '设备在线', 'data' => 1]);
        } else {
            return json(['code' => 0, 'msg' => '设备不在线', 'data' => 0]);
        }
    }
}
