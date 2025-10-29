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

namespace plugin\kundian\base;



use extend\Request;
use think\Model;
use think\helper\Str;



/**
 * 插件Service需继承
 */
abstract class BaseServices
{
    /**
     * 当前表名别名
     * @var string
     */
    protected $alias;


    /**
     * join表别名
     * @var string
     */
    protected $joinAlis;


    /**
     * 获取当前模型
     * @return string
     */
    abstract protected function setModel(): string;


    /**
     * 读取数据条数
     * @param array $where
     * @return int
     */
    public function count(array $where = []): int
    {
        return $this->search($where)->count();
    }

    /**
     * 获取某些条件总数
     * @param array $where
     * @return int
     */
    public function getCount(array $where)
    {
        return $this->getModel()->where($where)->count();
    }

    /**
     * 获取某些条件去重总数
     * @param array $where
     * @param $field
     */
    public function getDistinctCount(array $where, $field)
    {
        return $this->getModel()->where($where)->field('COUNT(distinct(' . $field . ')) as count')->select()->toArray()[0]['count'] ?? 0;

    }

    /**
     * 获取模型
     * @return \support\think\Model
     */
    public function getModel()
    {
        $model=$this->setModel();
        return new $model();
    }

    /**
     * 获取主键
     * @return mixed
     */
    protected function getPk()
    {
        return $this->getModel()->getPk();
    }

    /**
     * 获取一条数据
     * @param int|array $id
     * @param array|null $field
     * @param array|null $with
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get($id, ?array $field = [], ?array $with = [])
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [$this->getPk() => $id];
        }
        return $this->getModel()::where($where)->when(count($with), function ($query) use ($with) {
            $query->with($with);
        })->field($field ?? ['*'])->find();
    }

    /**
     * 查询一条数据是否存在
     * @param $map
     * @param string $field
     * @return bool 是否存在
     */
    public function be($map, string $field = '')
    {
        if (!is_array($map) && empty($field)) $field = $this->getPk();
        $map = !is_array($map) ? [$field => $map] : $map;
        return 0 < $this->getModel()->where($map)->count();
    }

    /**
     * 根据条件获取一条数据
     * @param array $where
     * @param string|null $field
     * @param array $with
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOne(array $where, ?string $field = '*', array $with = [])
    {
        $field = explode(',', $field);
        return $this->get($where, $field, $with);
    }

    /**
     * 获取单个字段值
     * @param array $where
     * @param string|null $field
     * @return mixed
     */
    public function value(array $where, ?string $field = '')
    {
        $pk = $this->getPk();
        return $this->search($where)->value($field ?: $pk);
    }

    /**
     * 获取某个字段数组
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getColumn(array $where, string $field, string $key = '')
    {
        return $this->search($where)->column($field, $key);
    }

    /**
     * 删除
     * @param int|string|array $id
     * @return mixed
     */
    public function delete($id, ?string $key = null)
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }
        return $this->getModel()::where($where)->delete();
    }

    /**
     * 更新数据
     * @param int|string|array $id
     * @param array $data
     * @param string|null $key
     * @return mixed
     */
    public function update($id, array $data, ?string $key = null)
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }
        return $this->getModel()::update($data, $where);
    }

    /**
     * 批量更新数据
     * @param array $ids
     * @param array $data
     * @param string|null $key
     * @return BaseModel
     */
    public function batchUpdate(array $ids, array $data, ?string $key = null)
    {
        return $this->getModel()::whereIn(is_null($key) ? $this->getPk() : $key, $ids)->update($data);
    }

    /**
     * 批量更新数据,增加条件
     * @param array $ids
     * @param array $data
     * @param array $where
     * @param string|null $key
     * @return BaseModel
     */
    public function batchUpdateAppendWhere(array $ids, array $data, array $where, ?string $key = null)
    {
        return $this->getModel()::whereIn(is_null($key) ? $this->getPk() : $key, $ids)->where($where)->update($data);
    }

    /**
     * 插入数据
     * @param array $data
     * @return BaseModel|Model
     */
    public function save(array $data)
    {
        return $this->getModel()::create($data);
    }

    /**
     * 插入数据
     * @param array $data
     * @return mixed
     */
    public function saveAll(array $data)
    {
        return $this->getModel()::insertAll($data);
    }

    /**
     * 获取某个字段内的值
     * @param $value
     * @param string $filed
     * @param string $valueKey
     * @param array|string[] $where
     * @return mixed
     */
    public function getFieldValue($value, string $filed, ?string $valueKey = '', ?array $where = [])
    {
        return $this->getModel()->getFieldValue($value, $filed, $valueKey, $where);
    }



    /**
     * 求和
     * @param array $where
     * @param string $field
     * @return float
     */
    public function sum(array $where, string $field)
    {
        return $this->getModel()::where($where)->sum($field);
    }

    /**
     * 高精度加法
     * @param int|string $key
     * @param string $incField
     * @param string $inc
     * @param string|null $keyField
     * @param int $acc
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bcInc($key, string $incField, string $inc, string $keyField = null, int $acc = 2)
    {
        return $this->bc($key, $incField, $inc, $keyField, 1);
    }

    /**
     * 高精度 减法
     * @param int|string $uid id
     * @param string $decField 相减的字段
     * @param float|int $dec 减的值
     * @param string $keyField id的字段
     * @param bool $minus 是否可以为负数
     * @param int $acc 精度
     * @param bool $dec_return_false 减法 不够减是否返回false ｜｜ 减为0
     * @return bool
     */
    public function bcDec($key, string $decField, string $dec, string $keyField = null, int $acc = 2, bool $dec_return_false = true)
    {
        return $this->bc($key, $decField, $dec, $keyField, 2, $acc, $dec_return_false);
    }

    /**
     * 高精度计算并保存
     * @param $key
     * @param string $incField
     * @param string $inc
     * @param string|null $keyField
     * @param int $type
     * @param int $acc
     * @param bool $dec_return_false 减法 不够减是否返回false ｜｜ 减为0
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bc($key, string $incField, string $inc, string $keyField = null, int $type = 1, int $acc = 2, bool $dec_return_false = true)
    {
        if ($keyField === null) {
            $result = $this->get($key);
        } else {
            $result = $this->getOne([$keyField => $key]);
        }
        if (!$result) return false;
        if ($type === 1) {
            $new = bcadd($result[$incField], $inc, $acc);
        } else if ($type === 2) {
            if ($result[$incField] < $inc) {
                if ($dec_return_false) return false;
                $new = 0;
            } else {
                $new = bcsub($result[$incField], $inc, $acc);
            }
        }
        $result->{$incField} = $new;
        return false !== $result->save();
    }



    /**
     * 软删除
     * @param $id
     * @param string|null $key
     * @return bool
     */
    public function destroy($id, ?string $key = null)
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }
        return $this->getModel()::destroy($where);
    }

    /**
     * 自增单个数据
     * @param $where
     * @param string $field
     * @param int $number
     * @return mixed
     */
    public function incUpdate($where, string $field, int $number = 1)
    {
        return $this->getModel()->where(is_array($where) ? $where : [$this->getPk() => $where])->inc($field, $number)->update();
    }

    /**
     * 自减单个数据
     * @param $where
     * @param string $field
     * @param int $number
     * @return mixed
     */
    public function decUpdate($where, string $field, int $number = 1)
    {
        return $this->getModel()->where(is_array($where) ? $where : [$this->getPk() => $where])->dec($field, $number)->update();
    }

    /**
     * 调试
     * @param $where
     * @param string $field
     * @param int $number
     * @return mixed
     */
    public function getLastSql()
    {
        return $this->getModel()->getLastSql();
    }

    /**
     * 获取分页配置
     * @param bool $isPage
     * @param bool $isRelieve
     * @return int[]
     */
    public function getPageValue(bool $isPage = true, bool $isRelieve = true)
    {
        $page = $limit = 0;
        if ($isPage) {
            $page = Request::param('page', 0);
            $limit = Request::param('limit',  0);
        }

        return [(int)$page, (int)$limit, 15];
    }

    /**
     * 搜索
     * @param array $where
     */
    protected function search(array $where = [])
    {
        if ($where) {
            [$with,$whereKey] = $this->withSearchSelect(array_keys($where), $this->setModel());
            $query=$this->getModel()->withSearch($with, $where);
            foreach ($whereKey as $v){
                if ($where[$v]!==''){
                    $param[$v]=$where[$v];
                }
            }
            if (!empty($param)){
                $query->where($param);
            }
            return $query;
        } else {
            return $this->getModel();
        }
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
        if (empty($page)||empty($limit)){
            $list=$this->search($where)->with($with)->field($field)->order($order)->select()->toArray();
            return ["list"=>$list];
        }else{
            $list= $this->search($where)->with($with)->field($field)->order($order)->page($page, $limit)->select()->toArray();
            $count = $this->search($where)->count();
            return ["list"=>$list,"count"=>$count];
        }
    }

    /**
     * 保存修改
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveData($param){
        $pk=$this->getPk();
        if (!empty($param[$pk])){
            $this->update($param[$pk],$param);
        }else{
            $this->save($param);
        }
    }


    /**
     * 获取列表
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSelect($where,$with=[],$field="*",$order="id desc"){
        return $this->search($where)->with($with)->field($field)->order($order)->select()->toArray();
    }


  public  function withSearchSelect (array $withSearch, string $model) {
        $with = [];
        $whereKey = [];
        $respones = new \ReflectionClass($model);
        foreach ($withSearch as $fieldName) {
            $method = 'search' . Str::studly($fieldName) . 'Attr';
            if ($respones->hasMethod($method)) {
                $with[] = $fieldName;
            } else {
                $whereKey[] = $fieldName;
            }
        }
        return [$with, $whereKey];
    }



    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getModel(), $name], $arguments);
    }

}