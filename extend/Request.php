<?php


namespace extend;

/**
 * Request
 */
class Request
{
    /**
     * 获取请求的参数
     * $key 返回的关键词.
     */
    public static function param($key = '')
    {
        $all = [];
        if (!empty(request())) {
            $all = request()->all();
            $body = request()->rawBody();
            if (!empty($body)) {
                $body = jsonDecode($body, true);
                if (!empty($body)) {
                    $all = array_merge($all, $body);
                }
            }
        }
        if (empty($key)) {
            return $all;
        } else {
            return $all[$key] ?? '';
        }
    }

    /**
     * 判断是否是手机端.
     *
     * @return bool true:手机端 false:pc端
     */
    public static function isMobile()
    {
        return isMobile();
    }

    public static function isAjax()
    {
        return request()->isAjax();
    }
    //判断是否是post提交
    public static function isPost()
    {
        return strtoupper(request()->method()) == 'POST';
    }
    //判断是否是get提交
    public static function isGet()
    {
        return strtoupper(request()->method()) == 'GET';
    }
    //获取控制器
    public static function controller(): string
    {
        if (!empty(request())) {
            return request()->controller;
        } else {
            return '';
        }
    }
    //获取方法
    public static function action(): string
    {
        if (!empty(request())) {
            return request()->action;
        } else {
            return '';
        }
    }
}
