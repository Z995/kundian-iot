<?php

/**
 * 坤典智慧农场V6
 * @link https://www.cqkd.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing KunDian Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing KunDian Technology Co., Ltd.
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-www.cqkd.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 */


namespace app\validate\api;

use app\model\Iot;
use app\model\Keys;
use think\Validate;

class ApiValidate extends Validate
{
    //1分钟最多请求10次
    const RATE_LIMIT = 10;
    //只允许出现的字段
    protected $forbidFields = ['device_code', 'api_key', 'sign', 'start_time', 'end_time', 'page'];

    protected $rule =   [
        'device_code'  => 'require',
        'api_key' => 'require',
        'page' => 'require|number',
        'size' => 'require|number',
        'start_time' => 'number',
        'end_time' => 'number',
        'msg' => 'require',
        'vtype' => 'require|in:0,1',
        'eol' => 'require|in:0,1,2',
        'sign' => 'require|checkSign',
    ];

    protected $message  =   [
        'device_code.require' => '设备自定义注册包不能为空',
        'api_key.require' => 'api_key不能为空',
        'sign.require' => '签名不能为空',
        'page.require' => '页码不能为空',
        'page.number' => '页码必须是数字',
        'size.require' => '每页数量不能为空',
        'size.number' => '每页数量必须是数字',
        'msg.require' => '消息不能为空',
        'vtype.require' => '数据类型不能为空',
        'vtype.in' => '数据类型不正确,0:ASCII,1:HEX,2:GB2312',
        'eol.require' => '换行符不能为空',
        'eol.in' => '换行符不正确,0:不加换行,1:加换行',
    ];

    //场景
    protected $scene = [
        'getDeviceData' => ['device_code', 'api_key', 'page', 'size', 'start_time', 'end_time', 'sign'],
        'sendMsg' => ['device_code', 'api_key', 'msg', 'vtype', 'eol', 'sign'],
        'getDeviceOnline' => ['device_code', 'api_key', 'sign'],
    ];
    //签名验证
    protected function checkSign($value, $rule, $data = [])
    {
        //根据api_key查询会员信息
        $keys = Keys::field('id,api_key,api_secret,status,expire,expire_time')
            ->where([['api_key', '=', $data['api_key']], ['del', '=', 0]])
            ->findOrEmpty()->toArray();
        if (empty($keys)) {
            return 'api_key不存在';
        }
        //检测是否启用
        if ($keys['status'] == 0) {
            return 'api_key未启用';
        }
        //检测是否过期
        if ($keys['expire'] == 1 && strtotime($keys['expire_time']) < time()) {
            return 'api_key已过期';
        }
        //检测设备自定义注册包
        $device = Iot::field('id,code')
            ->where([['code', '=', $data['device_code']], ['del', '=', 0]])
            ->findOrEmpty()->toArray();
        if (!$device) {
            return '设备自定义注册包不存在';
        }
        //检测签名
        $param = [];
        $scene = $this->currentScene;
        switch ($scene) {
            case 'getDeviceData':
                $param = [
                    'device_code' => $data['device_code'],
                    'api_key' => $data['api_key'],
                    'page' => $data['page'],
                    'size' => $data['size']
                ];
                if (isset($data['start_time'])) {
                    //判断时间戳转化为时间是否正确
                    if (date('Y-m-d H:i:s', (int)$data['start_time']) == false) {
                        return '开始时间格式错误';
                    }
                    //如果时间是3个月前,那么提示错误
                    if ($data['start_time'] < strtotime('-3 month')) {
                        return '开始时间不正确，平台只保存3个月的数据';
                    }
                    $param['start_time'] = $data['start_time'];
                }
                if (isset($data['end_time'])) {
                    //判断时间戳转化为时间是否正确
                    if (date('Y-m-d H:i:s', $data['end_time']) == false) {
                        return '结束时间格式错误';
                    }
                    $param['end_time'] = $data['end_time'];
                }
                //加上api_secret
                $param['api_secret'] = $keys['api_secret'];
                break;
            case 'sendMsg':
                $param = [
                    'device_code' => $data['device_code'],
                    'api_key' => $data['api_key'],
                    'msg' => $data['msg'],
                    'vtype' => $data['vtype'],
                    'eol' => $data['eol']
                ];
                //加上api_secret
                $param['api_secret'] = $keys['api_secret'];
                break;
            case 'getDeviceOnline':
                $param = [
                    'device_code' => $data['device_code'],
                    'api_key' => $data['api_key']
                ];
                //加上api_secret
                $param['api_secret'] = $keys['api_secret'];
                break;
        }
        ksort($param);
        $str = '';
        foreach ($param as $k => $v) {
            $str .= $k . $v;
        }
        // z($str);
        $sign = md5($str);
        // z($sign);
        if ($sign != $value) {
            return '签名错误';
        }
        //验证通过后,限制请求频率,一分钟最多请求10次
        $redis = myRedis();
        $key = 'rate_limit:{' . $data['api_key'] . '-' . $scene . '}:{' . date('YmdHi') . '}';
        $currentCount = $redis->get($key);
        if ($currentCount === false) {
            $redis->setex($key, 60, 1); // 设置初始值为1，过期时间为60秒
        } else if ($currentCount < self::RATE_LIMIT) {
            $redis->incr($key); // 如果当前分钟的请求次数小于10，就增加1
        } else {
            return '请求频率过快,1分钟最多请求' . self::RATE_LIMIT . '次';
        }
        return true;
    }
}
