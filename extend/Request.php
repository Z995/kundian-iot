<?php
/*
 * @Author: your name
 * @Date: 2022-02-06 08:41:47
 * @LastEditTime: 2023-04-15 16:36:02
 * @LastEditors: 冷丶秋秋秋秋秋
 * @Description: 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 * @FilePath: \tp6\think\app\common\Excels.php
 */

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
