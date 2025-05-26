<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-01-28 15:13:57
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-04-01 19:44:10
 * @FilePath: \yunxiao-webman\app\controller\IndexController.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\controller;

use app\model\DeviceLogs;
use app\model\Iot;
use app\model\WebhookLogs;
use app\services\ModbusRTUServices;
use app\services\RedisServices;
use extend\Debug;
use extend\Helper;
use support\Redis;
use extend\Page;
use extend\Request;
use support\View;

class IotController
{
    public function index()
    {
        $map = array();
        $map[] = ['del', '=', 0];
        $count = Iot::where($map)->count();
        $page = new Page();
        $page->setAttr($count, 100);
        $show = $page->show();
        View::assign('page', $show);
        return views();
    }
    public function getList()
    {
        $map = array();
        $map[] = ['del', '=', 0];
        $count = Iot::where($map)->count();
        $page = new Page();
        $page->setAttr($count, 100);
        $list = Iot::where($map)
            ->order('id', 'asc')
            ->limit($page->getFirstRow(), $page->getListRows())
            ->select()
            ->toArray();
        foreach ($list as $k => $v) {
            $list[$k]['typeName'] = Iot::TYPE_LIST[$v['type']];
            $crontab = jsonDecode($v['crontab'], true);
            $list[$k]['isCrontab'] = 0;
            if ($crontab && $crontab['status'] == 1) {
                $list[$k]['isCrontab'] = 1;
            }
            $list[$k]['online'] = Redis::hExists('HFiots-online', $v['code']) ? 1 : 0;
        }
        return json(['code' => 0, 'msg' => '', 'data' => $list]);
    }
    public function getInfo()
    {
        $result = array();
        $info = Iot::where([['id', '=', Request::param('id')], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$info) {
            $info['login'] = 0;
            $info['type'] = 0;
            $info['name'] = '';
            $info['remark'] = '';
            $info['code'] = '';
            $info['recode'] = '';
            $info['rtype'] = 0;
            $info['val'] = '';
            $info['vtype'] = 1;
            $info['code'] = $this->getCode();
            $info['forward'] = '';
            $info['http'] = '';
            $info['log'] = 1;
        }
        //查询过滤情况
        $myFilter = array('is' => 0, 'type' => array(), 'lengType' => '', 'length' => '', 'before' => '', 'beforeVal' => '', 'heartVal' => '');
        if ($info) {
            if (Redis::hExists('HFiots-filter', $info['code'])) {
                $myFilter = jsonDecode(Redis::hGet('HFiots-filter', $info['code']), true);
            }
        }
        $result['status'] = 'success';
        $result['info'] = $info;
        $result['myFilter'] = $myFilter;
        return json($result);
    }
    public function addAjax(Request $request)
    {
        $result = array();
        $member = session('member');
        $info = jsonDecode(Request::param('info'), true);
        if (!$info) {
            $result['status'] = 'error';
            $result['info'] = '请填写完整信息';
            return json($result);
        }
        //过滤null
        foreach ($info as $k => $v) {
            $info[$k] = $v == null ? '' : $v;
        }
        $myFilter = jsonDecode(Request::param('myFilter'), true);
        $data = array();
        $data['type'] = $info['type'] ?? 0;
        $data['login'] = $info['login'] ?? 0;
        $data['name'] = $info['name'] ?? '';
        $data['remark'] = $info['remark'] ?? '';
        $data['code'] = $info['code'] ?? '';
        $data['recode'] = $info['recode'] ?? '';
        $data['rtype'] = $info['rtype'] ?? 0;
        $data['val'] = $info['val'] ?? '';
        $data['vtype'] = $info['vtype'] ?? 1;
        $data['forward'] = $info['forward'] ?? '';
        $data['http'] = $info['http'] ?? '';
        $data['person'] = $member['id'];
        $data['filter'] = json_encode($myFilter);
        $data['log'] = $info['log'] ?? 1;
        if ($info['type'] == 2) {
            $data['username'] = $info['username'] ?? '';
            $data['password'] = $info['password'] ?? '';
            $data['password_md5'] = isset($info['password']) && $info['password'] ? md5($info['password']) : '';
            $data['topic'] = $info['topic'] ?? '';
            $data['action'] = 'all';
            $data['permission'] = 'allow';
        }
        $data['del'] = 0;
        $data['del_id'] = 0;
        $redis=new RedisServices();
        if (Request::param('id') != 'undefined' && Request::param('id') != '') {
            Iot::update($data, ['id' => Request::param('id')]);
            if ($info['type'] != 2) {
                $redis->setHFiotsRedis($data);
                $redis->setHFiotsFilterRedis($data['code'], $myFilter);
            }
            $result['status'] = 'success';
            $result['info'] = '修改成功';
        } else {
            $iot = Iot::where([['code', '=', $data['code']]])->findOrEmpty()->toArray();
            if ($iot) {
                $result['status'] = 'error';
                $result['info'] = '该注册码已存在';
            } else {
                $data['year'] = date('Y');
                $data['month'] = date('m');
                $data['day'] = date('d');
                $data['date'] = date('Y-m-d');
                $data['addtime'] = date('Y-m-d H:i:s');
                Iot::create($data);
                if ($info['type'] != 2) {
                    $redis->setHFiotsRedis($data);
                    $redis->setHFiotsFilterRedis($data['code'], $myFilter);
                }
                $result['status'] = 'success';
                $result['info'] = '操作成功';
            }
        }
        return json($result);
    }

    public function getCode($length = 12)
    {
        //字符组合
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($str) - 1;
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }
    public function addcrontab()
    {
        View::assign('id', request()->get('id'));
        return view('iot/addcrontab');
    }
    public function getCrontabInfo(Request $request)
    {
        $result = array();
        $result['status'] = 'success';
        $info = Iot::where([['id', '=', Request::param('id')], ['del', '=', 0]])->findOrEmpty()->toArray();
        if ($info) {
            if (Redis::hExists('HFiots-crontab', $info['code'])) {
                $crontab = jsonDecode(Redis::hGet('HFiots-crontab', $info['code']), true);
                $result['info'] = $crontab;
            } else {
                $result['info'] = array('type' => '0', 'vtype' => '0', 'val' => '', 'rtype' => '0', 'rval' => '', 'status' => 0, 'times' => 3);
            }
        } else {
            $result['info'] = array('type' => '0', 'vtype' => '0', 'val' => '', 'rtype' => '0', 'rval' => '', 'status' => 0, 'times' => 3);
        }
        return json($result);
    }
    public function addCrontabAjax(Request $request)
    {
        $result = array();
        $info = jsonDecode(Request::param('info'), true);
        $iot = Iot::where([['id', '=', Request::param('id')], ['del', '=', 0]])->findOrEmpty()->toArray();


        $arr = array('type' => $info['type'], 'vtype' => $info['vtype'], 'val' => $info['val'], 'rtype' => $info['rtype'], 'rval' => $info['rval'], 'status' => $info['status'], 'times' => $info['times']);
        //更新数据库
        Iot::update(array('crontab' => json_encode($arr)), ['id' => Request::param('id')]);

        Redis::hSet('HFiots-crontab', $iot['code'], json_encode($arr, JSON_UNESCAPED_UNICODE));
        //踢下线
        $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
        $my = [
            'from' => config('plugin.webman.gateway-worker.app.super_code'),
            'to' => $iot['code'],
            'action' => 'closeClient', //发送消息
        ];
        fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        $result['status'] = 'success';
        $result['info'] = '操作成功';
        return json($result);
    }
    public function delAjax(Request $request)
    {
        $result = array();
        $member = session('member');
        $info = Iot::findOrEmpty(Request::param('id'))->toArray();
        Iot::update(array('del' => 1, 'del_id' => $member['id'], 'del_time' => date('Y-m-d H:i:s')), ['id' => Request::param('id')]);
        Redis::hDel('HFiots', $info['code']);
        Redis::hDel('HFiots-filter', $info['code']);
        Redis::hDel('HFiots-crontab', $info['code']);
        Redis::hDel('HFiots-directive', $info['code']);
        $result['status'] = 'success';
        $result['info'] = '删除成功';
        $result['id'] = Request::param('id');
        $result['index'] = Request::param('index');
        return json($result);
    }
    public function response()
    {
        View::assign('id', request()->get('id'));
        return view('iot/response');
    }
    public function getResponseInfo(Request $request)
    {
        $result = array();
        $result['status'] = 'success';
        $info = Iot::where([['id', '=', Request::param('id')], ['del', '=', 0]])->findOrEmpty()->toArray();
        $arr = array();
        if ($info) {
            if (Redis::hExists('HFiots-directive', $info['code'])) {
                $directive = jsonDecode(Redis::hGet('HFiots-directive', $info['code']), true);
                $result['info'] = $directive;
            } else {
                $arr = array(
                    array('stype' => '0', 'sval' => '', 'rtype' => '0', 'rval' => ''),
                    array('stype' => '0', 'sval' => '', 'rtype' => '0', 'rval' => '')
                );
                $result['info'] = $arr;
            }
        } else {
            $result['info'] = $arr;
        }
        return json($result);
    }
    public function addResponseAjax(Request $request)
    {
        $result = array();
        $list = jsonDecode(Request::param('list'), true);
        foreach ($list as $k => $v) {
            //触发指令和回复指令必须成对出现
            if (($v['sval'] != '' && $v['rval'] == '') || ($v['sval'] == '' && $v['rval'] != '')) {
                $result['status'] = 'error';
                $result['info'] = '触发指令和回复指令必须成对出现';
                return json($result);
            }
            //如果全部为空,清空该条数据
            if ($v['sval'] == '' && $v['rval'] == '') {
                unset($list[$k]);
            }
        }
        //如果list数量小于2,则补齐
        if (count($list) < 2) {
            for ($i = 0; $i <= 1; $i++) {
                if (!isset($list[$i])) {
                    $list[$i] = ['stype' => '0', 'sval' => '', 'rtype' => '0', 'rval' => ''];
                }
            }
        }

        $iot = Iot::where([['id', '=', Request::param('id')], ['del', '=', 0]])->findOrEmpty()->toArray();
        //更新数据库
        Iot::update(array('directive' => json_encode($list)), ['id' => Request::param('id')]);
        Redis::hSet('HFiots-directive', $iot['code'], json_encode($list, JSON_UNESCAPED_UNICODE));
        $result['status'] = 'success';
        $result['info'] = '操作成功';
        return json($result);
    }
    public function add()
    {
        View::assign('id', request()->get('id'));
        return view('iot/add');
    }
    public function flow()
    {
        View::assign([
            'code' => request()->get('code'),
            'ip' => config('plugin.webman.gateway-worker.app.ip'),
            'port' => str_replace('websocket://0.0.0.0:', '', config('plugin.webman.gateway-worker.process.gateway_ws.listen')),
            'super_code' => config('plugin.webman.gateway-worker.app.super_code')
        ]);
        return view('iot/flow');
    }
    //获取websocket
    public function getWebSocket()
    {

        $data = [
            'wsStr' => 'ws://' . config('plugin.webman.gateway-worker.app.ip') . ':' . str_replace('websocket://0.0.0.0:', '', config('plugin.webman.gateway-worker.process.gateway_ws.listen')),
            'code' => config('plugin.webman.gateway-worker.app.super_code')
        ];
        return json(['code' => 0, 'msg' => '查询成功', 'data' => $data]);
    }
    public function look()
    {
        return views();
    }
    public function getLook()
    {
        $deviceId = (int)Request::param('id');
        $info = Iot::where([['id', '=', $deviceId], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$info) {
            return json(['code' => 1, 'msg' => '设备不存在']);
        }
        if ($info['type'] == 0) {
            $info['typeName'] = 'TCP协议';
            $info['ip'] = config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.tcp_port');
            $info['codeType'] = '自定义注册包';
        } elseif ($info['type'] == 1) {
            $info['typeName'] = 'Websocket协议';
            $info['ip'] = config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.ws_port');
            $info['codeType'] = '自定义注册包';
        } else {
            $info['typeName'] = 'Mqtt协议';
            $info['ip'] = config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.mqtt_port');
            $info['codeType'] = '客户端ID';
        }
        $info['filter'] = jsonDecode($info['filter'], true);
        return json(['code' => 0, 'msg' => '查询成功', 'info' => $info]);
    }
    //webhook日志
    public function logs()
    {
        $deviceId = (int)Request::param('device_id');
        //设置年月日
        //昨天
        if (!Request::param('year')) {
            $date = explode('-', date("Y-m-d"));
            return redirect('http://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.server_port') . '/' . 'iot/logs?year=' . $date[0] . '&month=' . $date[1] . '&day=all&device_id=' . $deviceId);
        }
        View::assign([
            'search_year' => Helper::search_year('http://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.server_port') . '/' . 'iot/logs', Request::param()),
            'search_month' => Helper::search_month('http://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.server_port') . '/' . 'iot/logs', Request::param()),
            'search_day' => Helper::search_day('http://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.server_port') . '/' . 'iot/logs', Request::param()),
        ]);
        //搜索条件
        $map = [];
        $map[] = ['del', '=', 0];
        $map[] = ['device_id', '=', $deviceId];
        if (Request::param('year') != 'all' && Request::param('year') != '') {
            $map[] = ['year', '=', Request::param('year')];
        }
        if (Request::param('month') != 'all' && Request::param('month') != '') {
            $map[] = ['month', '=', Request::param('month')];
        }
        if (Request::param('day') != 'all' && Request::param('day') != '') {
            $map[] = ['day', '=', Request::param('day')];
        }
        $count = DeviceLogs::where($map)->count();
        $page = new Page();
        $page->setAttr($count, 50);
        $show = $page->show();
        $list = DeviceLogs::field('id,type,vtype,val,topic,addtime')
            ->where($map)
            ->order(['id' => 'desc'])
            ->limit($page->getFirstRow(), $page->getListRows())
            ->select()->toArray();
        foreach ($list as $k => $v) {
            $list[$k]['typeName'] = Iot::TYPE_LIST[$v['type']];
            $list[$k]['vtypeName'] = Iot::VTYPE_LIST[$v['vtype']];
        }
        return views('', [
            'list' => $list,
            'page' => $show
        ]);
    }
    //webhook日志
    public function webhook()
    {
        $deviceId = (int)Request::param('device_id');
        //设置年月日
        //昨天
        if (!Request::param('year')) {
            $date = explode('-', date("Y-m-d"));
            return redirect('http://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.server_port') . '/' . 'iot/webhook?year=' . $date[0] . '&month=' . $date[1] . '&day=all&device_id=' . $deviceId);
        }
        View::assign([
            'search_year' => Helper::search_year('http://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.server_port') . '/' . 'iot/webhook', Request::param()),
            'search_month' => Helper::search_month('http://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.server_port') . '/' . 'iot/webhook', Request::param()),
            'search_day' => Helper::search_day('http://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.server_port') . '/' . 'iot/webhook', Request::param()),
        ]);
        //搜索条件
        $map = [];
        $map[] = ['del', '=', 0];
        $map[] = ['device_id', '=', $deviceId];
        if (Request::param('year') != 'all' && Request::param('year') != '') {
            $map[] = ['year', '=', Request::param('year')];
        }
        if (Request::param('month') != 'all' && Request::param('month') != '') {
            $map[] = ['month', '=', Request::param('month')];
        }
        if (Request::param('day') != 'all' && Request::param('day') != '') {
            $map[] = ['day', '=', Request::param('day')];
        }
        $count = WebhookLogs::where($map)->count();
        $page = new Page();
        $page->setAttr($count, 50);
        $show = $page->show();
        $list = WebhookLogs::field('id,url,status,param,msg,addtime')
            ->where($map)
            ->order(['id' => 'desc'])
            ->limit($page->getFirstRow(), $page->getListRows())
            ->select()->toArray();
        foreach ($list as $k => $v) {
            $list[$k]['param'] = Debug::toHtml(jsonDecode($v['param'], true));
            $list[$k]['statusName'] = WebhookLogs::STATUS_LIST[$v['status']];
        }
        return views('', [
            'list' => $list,
            'page' => $show
        ]);
    }
    //下线设备
    public function offlineAjax()
    {
        $deviceId = (int)Request::param('id');
        $info = Iot::where([['id', '=', $deviceId], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$info) {
            return json(['status' => 'error', 'info' => '设备不存在']);
        }
        if ($info['type'] != 0) {
            return json(['status' => 'error', 'info' => '只有TCP协议的设备才支持下线']);
        }
        $client = stream_socket_client('tcp://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.text_port'));
        $my = [
            'from' => config('plugin.webman.gateway-worker.app.super_code'),
            'to' => $info['code'],
            'action' => 'closeClient', //发送消息
        ];
        fwrite($client, json_encode($my, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        return json(['status' => 'success', 'info' => '下线成功']);
    }
}
