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
use app\model\Keys;
use app\model\WebhookLogs;
use extend\Debug;
use extend\Helper;
use support\Redis;
use extend\Page;
use extend\Request;
use support\View;

class KeysController
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
        $count = Keys::where($map)->count();
        $page = new Page();
        $page->setAttr($count, 100);
        $list = Keys::where($map)
            ->order('id', 'asc')
            ->limit($page->getFirstRow(), $page->getListRows())
            ->select()
            ->toArray();
        foreach ($list as $k => $v) {
            if ($v['expire'] == 0) {
                $list[$k]['expire_time'] = '永不过期';
            }
            $list[$k]['statusName'] = Keys::STATUS_NAME[$v['status']];
        }
        return json(['code' => 0, 'msg' => '', 'data' => $list]);
    }
    public function getInfo()
    {
        $result = array();
        $info = Keys::where([['id', '=', Request::param('id')], ['del', '=', 0]])->findOrEmpty()->toArray();
        if (!$info) {
            $info = [
                'name' => '',
                'api_key' => '',
                'api_secret' => '',
                'expire' => 0,
                'expire_time' => '',
                'status' => 1,
                'remark' => ''
            ];
        }
        $result['status'] = 'success';
        $result['info'] = $info;
        return json($result);
    }
    public function addAjax(Request $request)
    {
        $result = array();
        $member = session('member');
        $info = jsonDecode(Request::param('info'), true);
        //过滤null
        foreach ($info as $k => $v) {
            $info[$k] = $v == null ? '' : $v;
        }
        $data = array();
        $data['name'] = $info['name'];
        $data['expire'] = $info['expire'];
        $data['expire_time'] = $info['expire_time'] ? $info['expire_time'] : null;
        $data['status'] = $info['status'];
        $data['remark'] = $info['remark'];
        $data['del'] = 0;
        $data['del_id'] = 0;
        if (Request::param('id') != 'undefined' && Request::param('id') != '') {
            Keys::update($data, ['id' => Request::param('id')]);
            $result['status'] = 'success';
            $result['info'] = '修改成功';
        } else {
            $data['api_key'] = md5(uniqid() . rand(1000, 9999));
            $data['api_secret'] = md5(uniqid() . rand(1000, 9999));
            $data['year'] = date('Y');
            $data['month'] = date('m');
            $data['day'] = date('d');
            $data['date'] = date('Y-m-d');
            $data['addtime'] = date('Y-m-d H:i:s');
            Keys::create($data);
            $result['status'] = 'success';
            $result['info'] = '操作成功';
        }
        return json($result);
    }
    public function delAjax(Request $request)
    {
        $result = array();
        $member = session('member');
        Keys::update(array('del' => 1, 'del_id' => $member['id'], 'del_time' => date('Y-m-d H:i:s')), ['id' => Request::param('id')]);
        $result['status'] = 'success';
        $result['info'] = '删除成功';
        $result['id'] = Request::param('id');
        $result['index'] = Request::param('index');
        return json($result);
    }
    public function add()
    {
        View::assign('id', request()->get('id'));
        return view('keys/add');
    }
}
