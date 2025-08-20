<?php

/**
 * Here is your custom functions.
 */

use app\serve\RedisServices;
use extend\Request;
use support\Redis;
use support\Response;
use \app\services\system\SystemConfigServices;

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

        if (is_string($msg)) {
            $result=["code"=>200,'msg'=>$msg,"data"=>$data];
        }else{
            $result=["code"=>200,'msg'=>"ok","data"=>$msg];
        }
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}


if (!function_exists('fail')) {
    function fail($msg = 'fail', ?array $data = null, $code = 10001)
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg = 'fail';
        }
        $result=["code"=>$code,'msg'=>$msg,"data"=>$data];
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}

if (!function_exists('generateRandomString')) {
    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}
if (!function_exists('getMore')) {

    /**
     * 获取请求的数据
     * @param array $params
     * @param bool $suffix
     * @param bool $filter
     * @return array
     */
     function getMore(array $params, bool $suffix = false, bool $filter = true): array
    {
        /**
         * 不过滤变量名
         * @var array
         */
        $except = ['menu_path', 'api_url', 'unique_auth', 'description', 'custom_form', 'product_detail_diy', 'value'];
        $p = [];
        $i = 0;
        foreach ($params as $param) {
            if (!is_array($param)) {
                $p[$suffix == true ? $i++ : $param] =filterWord(is_string(Request::param($param)) ? trim(Request::param($param)) : Request::param($param), $filter && !in_array($param, $except));
            } else {
                if (!isset($param[1])) $param[1] = null;
                if (!isset($param[2])) $param[2] = '';
                if (is_array($param[0])) {
                    $name = is_array($param[1]) ? $param[0][0] . '/a' : $param[0][0] . '/' . $param[0][1];
                    $keyName = $param[0][0];
                } else {
                    $name = is_array($param[1]) ? $param[0] . '/a' : $param[0];
                    $keyName = $param[0];
                }
                $request_param=Request::param($name);
                $data=($request_param===""||$request_param===[])?$param[1]:$request_param;
                $p[$suffix == true ? $i++ :$keyName] = filterWord(is_string($data) ? trim($data) :$data, $filter && !in_array($keyName, $except));
            }
        }
        return $p;
    }

}


if (!function_exists('filterWord')) {

    /**
     * 过滤接受的参数
     * @param $str
     * @param bool $filter
     * @return array|mixed|string|string[]
     */
     function filterWord($str, bool $filter = true)
    {
        if (!$str || !$filter) return $str;
        // 把数据过滤
        $farr = [
            "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
            "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
            "/select|join|where|drop|like|modify|rename|insert|update|table|database|alter|truncate|\'|\/\*|\.\.\/|\.\/|union|into|load_file|outfile/is"
        ];
        if (is_array($str)) {
            foreach ($str as &$v) {
                if (is_array($v)) {
                    foreach ($v as &$vv) {
                        if (!is_array($vv)&&!empty($vv)) $vv = preg_replace($farr, '', $vv);
                    }
                } else {
                    $v = preg_replace($farr, '', $v);
                }
            }
        } else {
            $str = preg_replace($farr, '', $str);
        }
        return $str;
    }


}


if (!function_exists('get_order_sn')) {

    /**
     * 获取唯一号.
     * @param mixed $prefix
     * @param mixed $num
     * @throws Exception
     */
    function get_order_sn($prefix = '',$num=10)
    {
        return $prefix . (new \app\serve\SnowflakeRandomGenerator())->generate($num);
    }

}
if (!function_exists('generateRandomString')) {

    /**
     * 随机字符串
     * @param mixed $prefix
     * @throws Exception
     */
    function generateRandomString($length) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        $charCount = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, $charCount - 1);
            $str .= $chars[$randomIndex];
        }
        return $str;
    }
}
if (!function_exists('myHexTobin')) {

    /**
     * 随机字符串
     * @param mixed $prefix
     * @throws Exception
     */
    function myHexTobin($str) {
        $res = '';
        //去除空格
        $str = str_replace([' ', PHP_EOL, '　'], '', $str);
        if (!$str) {
            return $res;
        }
        if (strlen($str) % 2 != 0) {
            return $str;
        }
        return hex2bin($str);
    }
}
if (!function_exists('getDailyTimeRanges')) {
    /**
     * 获取最近 N 天的每日时间范围
     *
     * @param int $days 天数（如 7 或 30）
     * @return array 返回二维数组，每项包含当天开始和结束时间戳
     */
    function getDailyTimeRanges($days) {
        $result = [];
        // 获取当前时间戳（精确到秒）
        $currentTime = time();
        // 计算起始日期（当前时间减去 N-1 天）
        $startTimestamp = $currentTime - (($days - 1) * 86400);
        // 循环生成每一天的时间范围
        for ($i = 0; $i < $days; $i++) {
            // 计算当前天的开始时间戳（00:00:00）
            $dayStart = strtotime(date('Y-m-d 00:00:00', $startTimestamp + ($i * 86400)));
            // 计算当前天的结束时间戳（23:59:59）
            $dayEnd = strtotime(date('Y-m-d 23:59:59', $startTimestamp + ($i * 86400)));
            $result[] = [
                'start_time' => $dayStart,
                'end_time' => $dayEnd,
                'date' => date('Y-m-d', $dayStart) // 可选：添加日期字符串
            ];
        }
        return $result;
    }
}


if (!function_exists('get_system_config')) {
    function get_system_config($key,$default="",$admin_id="") {
        if (empty($admin_id)){
            $result = (new RedisServices())->checkToken();
            $admin_id= $result["id"];
        }
        return SystemConfigServices::getSystemConfig($key,$admin_id,$default);
    }
}


if (!function_exists('set_system_config')) {
    function set_system_config($data,$admin_id="") {
        if (empty($admin_id)){
            $result = (new RedisServices())->checkToken();
            $admin_id= $result["id"];
        }
        return (new SystemConfigServices())->setSystemConfig($data,$admin_id);
    }
}
if (!function_exists('httpsRequest')) {
    /**
     * curl请求
     * @param $url
     * @param $data
     * @param $type
     * @param $res
     * @return mixed|void
     */
    function httpsRequest($url, $data = "", $type = "post", $res = "json", $header = [])
    {
        //1.初始化curl
        $curl = curl_init();
        //2.设置curl的参数
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if ($type == "post") {
            curl_setopt($curl, CURLOPT_POST, true);
            if (!empty($data)){
                $jsonData = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData); // 直接传递JSON字符串
                if (empty($header)){
                    curl_setopt($curl, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($jsonData)
                    ]);
                }
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            }
            if (!empty($header)){
                curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            }


        }
        //3.采集
        $output = curl_exec($curl);
        //4.关闭
        curl_close($curl);
        if ($res == 'json') {
            return json_decode($output, true);
        }
        return $output;
    }

}
