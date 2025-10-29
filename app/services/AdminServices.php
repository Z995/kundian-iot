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

namespace app\services;

use app\model\Admin;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class AdminServices extends BaseServices
{
    const adminId=1;
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return Admin::class;
    }

    public function getList($where=[],$with=["device","gateway"],$field="*",$order="id desc"){
        [$page, $limit] = $this->getPageValue();
        $list= $this->search($where)->withCount($with)->field($field)->order($order)->page($page, $limit)->select()->toArray();
        $count = $this->search($where)->count("id");
        return ["list"=>$list,"count"=>$count];
    }


    /**
     * 添加用户
     * @param $param
     * @return void
     */
    public function saveUser($param){
        if (!empty($param["password"])){
            $param["password"]=password_hash($param["password"], PASSWORD_DEFAULT);
        }
        if (!empty($param["id"])){
            if (self::adminId==$param["id"]){
                throw new ValidateException('不能修改');
            }
            $this->update($param['id'],$param);
        }else{
            unset($param["id"]);
            $count=$this->count(["phone"=>$param["phone"],"is_del"=>0]);
            if ($count>0){
                throw new ValidateException('手机号已存在');
            }
            $this->save($param);
        }
    }

    /**
     * 用户登陆
     * @param $phone
     * @param $password
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login($phone,$password){
        $user=$this->get(["phone|name"=>$phone,"is_del"=>0]);
        if (!$user){
            throw new ValidateException('用户不存在');
        }
        if ($user['status']==0){
            throw new ValidateException('状态错误');
        }
        if (empty($user['password'])){
            throw new ValidateException('账号错误');
        }
        if (!password_verify($password, $user['password'])) {
            throw new ValidateException('密码错误');
        }
        $token=(new RedisServices())->setToken($user);
        return ["token"=>$token];
    }

}