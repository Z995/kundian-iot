<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-01-28 15:13:57
 * @LastEditors: 冷丶秋秋秋秋秋
 * @LastEditTime: 2023-02-24 17:33:11
 * @FilePath: \yunxiao-webman\app\controller\IndexController.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace app\controller;

use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        return view('index/index', ['name' => 'webman']);
    }

    public function login(Request $request)
    {
        $session = $request->session();
        $session->set('member', ['id' => 1, 'name' => 'admin']);
        return json(['code' => 0, 'msg' => '登录成功']);
    }
    public function login_out()
    {
        $session = request()->session();
        $session->delete('member');
        return json(['code' => 0, 'msg' => '退出成功']);
    }
}
