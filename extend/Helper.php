<?php

namespace extend;


class Helper
{
    /**
     * Undocumented function 防止重复提交
     *
     * @param [type] $key key
     * @param integer $seconds 秒数
     * @return boolean true可以提交,false不可以提交
     */
    public static function isAllowSubmit($key, $seconds = 3)
    {
        $redis = myRedis();
        if (!$redis->exists('Iot-isAllowSubmit' . $key)) { //不存在
            $redis->setex('Iot-isAllowSubmit' . $key, $seconds, true); //设置有效期为$seconds秒的键值
            return true; //不存在，可以提交
        } else { //存在
            return false; //存在，不可提交
        }
    }
    /**
     * [年份搜索]
     * @param  [type] $url    [链接]
     * @param string $request [当前url里的参数]
     * @return [type]         [description]
     */
    public static function search_year($url, $request = [], $btn_all = false)
    {
        //当前请求的控制器
        $controller_name = Request::controller();
        $controller_name = strtolower(str_replace(['app\\controller\\', 'Controller'], '', $controller_name));
        //当前请求的方法
        $action_name = Request::action();

        //年份数组
        $year = [];
        for ($i = 2024; $i <= date('Y', strtotime('+1 year')); $i++) {
            $year[] = $i;
        }
        //结果
        $result = '<b>年份：</b>';
        //url里的参数
        $where = '';
        //循环$_REQUEST
        foreach ($request as $k => $v) {
            //排除年份
            if ($k != '_URL_' && $k != 'year' && $v != '') {
                $where .= $k . '=' . $v . '&';
            }
        }
        //拼接参数url
        $url .= '?' . $where . 'year';
        //是否显示全部按钮
        if ($btn_all) //显示
        {
            if (($request['year'] ?? '') == 'all') {
                $result .= '<a href="' . $url . '=all" class="select_a">全部</a>';
            } else {
                $result .= '<a href="' . $url . '=all">全部</a>';
            }
        }
        //循环年份
        foreach ($year as $k => $v) {
            if (($request['year'] ?? '') == $v) //将url参数里的年份选中
            {
                $result .= '<a href="' . $url . '=' . $v . '" class="select_a">' . $v . '年</a>';
            } else {
                $result .= '<a href="' . $url . '=' . $v . '">' . $v . '年</a>';
            }
        }
        return $result;
    }

    /**
     * [月份搜索]
     * @param  [type] $url [链接,不带月份参数的链接]
     * @param  [type] $request [获取url参数]
     * @return [type]      [结果字符串]
     */
    public static function search_month($url, $request = [])
    {
        //获取月份
        $month = (int)Request::param('month');
        //结果
        $result = '<b>月份：</b>';
        //循环$_REQUEST
        $where = '';
        foreach ($request as $k => $v) {
            //排除月份
            if ($k != '_URL_' && $k != 'month') {
                $where .= $k . '=' . $v . '&';
            }
        }
        //拼接参数
        $url .= '?' . $where . 'month';
        //是否显示全部按钮
        if ($month == 'all') {
            $result .= '<a href="' . $url . '=all" class="select_a">全部</a>';
        } else {
            $result .= '<a href="' . $url . '=all">全部</a>';
        }
        $x = 0; //移动端显示几个
        $date = new Date();
        for ($i = 12; $i >= 1; $i--) {
            if (isMobile()) //移动端
            {
                if ($i == $month) {
                    if ($x >= 4) {
                        $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '" class="select_a hide">' . $date->numberToCh($i) . '月</a>';
                    } else {
                        $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '" class="select_a">' . $date->numberToCh($i) . '月</a>';
                    }
                } else {
                    if ($x >= 4) {
                        $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '" class="hide">' . $date->numberToCh($i) . '月</a>';
                    } else {
                        $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '">' . $date->numberToCh($i) . '月</a>';
                    }
                }
            } else {
                if ($i == $month) {
                    $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '" class="select_a">' . $date->numberToCh($i) . '月</a>';
                } else {
                    $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '">' . $date->numberToCh($i) . '月</a>';
                }
            }
            $x++;
        }
        if (isMobile()) {
            $result .= '<a href="javascript:;" class="more">更多<i class="icon-angle-down"></i></a>';
        }
        return $result;
    }

    /**
     * [日期搜索]
     * @param  [type]  $url     [链接]
     * @param string $request [url参数]
     * @param boolean $all [是否显示全部按钮]
     * @return [type]           [description]
     */
    public static function search_day($url, $request = [], $btn_all = true)
    {
        //获取日期
        $day = (int)Request::param('day');
        //结果
        $result = '<b>日期：</b>';
        //循环$_REQUEST
        $where = '';
        foreach ($request as $k => $v) {
            //排除月份
            if ($k != '_URL_' && $k != 'day') {
                $where .= $k . '=' . $v . '&';
            }
        }
        //拼接参数
        $url .= '?' . $where . 'day';
        //是否显示全部按钮
        if ($btn_all) //显示
        {
            if ($day == 'all') {
                $result .= '<a href="' . $url . '=all" class="select_a">全部</a>';
            } else {
                $result .= '<a href="' . $url . '=all">全部</a>';
            }
        }
        $allDays = 31;
        if (Request::param('month') != '' && Request::param('month') != 'all') {
            $allDays = (new Date())->maxDayOfMonth(Request::param('year') . '-' . Request::param('month'));
            if ($allDays < 28) {
                $allDays = 31;
            }
        }
        for ($i = $allDays; $i >= 1; $i--) {
            if (isMobile()) //移动端
            {
                if ($i == $day) {
                    if ($i <= 25) {
                        $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '" class="select_a hide">' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '</a>';
                    } else {
                        $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '" class="select_a">' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '</a>';
                    }
                } else {
                    if ($i <= 25) {
                        $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '" class="hide">' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '</a>';
                    } else {
                        $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '">' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '</a>';
                    }
                }
            } else {
                if ($i == $day) {
                    $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '" class="select_a">' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '</a>';
                } else {
                    $result .= '<a href="' . $url . '=' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '">' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '</a>';
                }
            }
        }
        if (isMobile()) {
            $result .= '<a href="javascript:;" class="more">更多<i class="icon-angle-down"></i></a>';
        }
        return $result;
    }
    //如果是移动端,那么检测模板里是否有m,如果有用m
    public static function mobile_tpl()
    {
        //app\controller\IndexController
        $controller = request()->controller;
        //解析出来的控制器名称
        $controller = strtolower(str_replace(['app\\controller\\', 'Controller'], '', $controller));
        $result = ''; //返回模板名称
        $action = request()->action;
        //如果$action有大写字母,那么就是驼峰命名,那么就要转换成下划线命名
        if (preg_match('/[A-Z]/', $action)) {
            $action = preg_replace_callback('/([A-Z])/', function ($matches) {
                return '_' . strtolower($matches[0]);
            }, $action);
        }
        if (isMobile())  //移动端
        {
            $result = $controller . DIRECTORY_SEPARATOR . $action . '_m';
        } else {
            $result = $controller . DIRECTORY_SEPARATOR . $action; //PC端的话 就返回他的同名的html
        }
        return $result;
    }
}
