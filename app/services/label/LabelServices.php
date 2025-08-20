<?php

namespace app\services\label;

use app\model\Admin;
use app\model\gateway\Gateway;
use app\model\gateway\GatewayInstruct;
use app\model\label\Label;
use app\serve\RedisServices;
use plugin\kundian\base\BaseServices;
use think\exception\ValidateException;

class LabelServices extends BaseServices
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return Label::class;
    }

    /**
     * 获取标签
     * @param $ids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabelByIds($ids){
       return $this->getModel()->whereIn('id',$ids)->select()->toArray();
    }


}