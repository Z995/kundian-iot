<?php

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