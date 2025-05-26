<?php

namespace app\services;

use support\Redis;
use think\exception\ValidateException;

class RedisServices
{
    //HFiots表
    public function setHFiotsRedis($data)
    {
        //HFiots表
        $arr = array(
            'uid' => $data['code'] . '-Uid',
            'type' => (int)$data['type'],
            'login' => (int)$data['login'],
            'recode' => $data['recode'],
            'rtype' => (int)$data['rtype'],
            'val' => $data['val'],
            'vtype' => (int)$data['vtype'],
            'forward' => $data['forward'],
            'http' => $data['http'],
            'name' => $data['name'],
            'log' => (int)$data['log'],
        );
        Redis::hSet('HFiots', $data['code'], json_encode($arr, JSON_UNESCAPED_UNICODE));
    }

    //HFiots-filter表
    public function setHFiotsFilterRedis($code, $myFilter)
    {
        if (!is_array($myFilter)) {
            $myFilter = json_decode($myFilter, true);
        }
        //HFiots-filter表
        $arr = array(
            'is' => $myFilter['is'],
            'type' => $myFilter['type'],
            'lengType' => $myFilter['lengType'],
            'length' => $myFilter['length'],
            'before' => $myFilter['before'],
            'beforeVal' => $myFilter['beforeVal'],
            'heartVal' => $myFilter['heartVal']
        );
        Redis::hSet('HFiots-filter', $code, json_encode($arr, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取token中的信息
     * @param $token
     * @return bool|string
     */
    public function getUserInfoByToken($token)
    {
        $token = trim(ltrim($token, 'Bearer'));
        Redis::get($token);
        return ;
    }

    public function setToken(){

    }

}