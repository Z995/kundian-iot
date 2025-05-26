<?php

/**
 * Here is your custom functions.
 */

use app\model\Wxqueue;
use support\Redis;
use support\Response;
use Symfony\Component\VarDumper\VarDumper;
use think\facade\Cache;

//格式化json_decode,异常情况全部返回空数组
if (!function_exists('jsonDecode')) {
    function jsonDecode($str, $type = true)
    {
        $arr = [];
        if (gettype($str) == 'string') {
            $arr = is_array(json_decode($str, $type)) ? json_decode($str, $type) : [];
        }
        return $arr;
    }
}

if (!function_exists('views')) {
    function views(string $template = null, array $vars = [], string $app = null, string $plugin = null): Response
    {
        if (!$template) {
            $controller = strtolower(str_replace(['app\\controller\\', 'Controller'], '', request()->controller));
            $action = request()->action;
            //如果$action有大写字母,那么就是驼峰命名,那么就要转换成下划线命名
            if (preg_match('/[A-Z]/', $action)) {
                $action = preg_replace_callback('/([A-Z])/', function ($matches) {
                    return '_' . strtolower($matches[0]);
                }, $action);
            }
            $template = $controller . DIRECTORY_SEPARATOR . $action;
        }
        $request = \request();
        $plugin = $plugin === null ? ($request->plugin ?? '') : $plugin;
        $handler = \config($plugin ? "plugin.$plugin.view.handler" : 'view.handler');
        return new Response(200, [], $handler::render($template, $vars, $app, $plugin));
    }
}

//判断是否是移动端
/**
 * @return bool true:移动端 false:pc端
 */
if (!function_exists('isMobile')) {
    function isMobile()
    {
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
        $userAgent = request()->header('user-agent');
        if ($userAgent) {
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile', 'MicroMessenger');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($userAgent))) {
                return true;
            }
        }
        return false;
    }
}

/**
 * 调试变量并且中断输出
 * @param mixed $vars 调试变量或者信息
 */
if (!function_exists('z')) {
    function z(...$vars)
    {
        foreach ($vars as $var) {
            VarDumper::dump($var);
        }
    }
}

//redis
if (!function_exists('myRedis')) {
    function myRedis()
    {
        $redis = Redis::connection('default');
        return $redis;
    }
}
/**
 * 生成表Id
 * @param $t_name 表名
 */
if (!function_exists('genreId')) {
    function genreId($t_name)
    {
        $redis = myRedis();
        $time = date('YmdHis');
        $key = $t_name . '_' . $time;
        $max_num = $redis->get($key);
        if (!$max_num) {
            $max_num = mt_rand(1000, 1100);
            $redis->setnx($key, $max_num);
            $redis->expire($key, 10);
        }
        return $time . $redis->incr($key);
    }
}

if (!function_exists('success')) {
    function success($msg = 'ok', ?array $data = null)
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg = 'ok';
        }
        $result=["status"=>200,'msg'=>$msg,"data"=>$data];
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}


if (!function_exists('fail')) {
    function fail($msg = 'fail', ?array $data = null)
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg = 'fail';
        }
        $result=["status"=>200,'msg'=>$msg,"data"=>$data];
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}
