<?php

namespace app\services\gateway;

use app\model\Admin;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayLog;
use app\model\Iot;
use app\serve\ModbusRTUServices;
use app\serve\RedisServices;
use app\serve\Snowflake;
use extend\Request;
use plugin\kundian\base\BaseServices;
use plugin\webman\gateway\Events;
use plugin\webman\gateway\servers\SortingDataServices;
use support\Redis;
use think\exception\ValidateException;

class GatewayLogServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return GatewayLog::class;
    }

    /**
     * 分页查询
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($where=[],$with=[],$field="*",$order="id desc"){
        [$page, $limit] = $this->getPageValue();
        $list= $this->search($where)->with($with)->field($field)->order($order)->page($page, $limit)->select()->toArray();
        $count = $this->search($where)->count("id");
        return ["list"=>$list,"count"=>$count];
    }
}