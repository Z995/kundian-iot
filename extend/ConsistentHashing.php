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

namespace extend;

// 一致性哈希算法
class ConsistentHashing
{
    protected $nodes = array();    //真实节点
    protected $position = array();  //虚拟节点
    protected $mul = 64;  // 每个节点对应64个虚拟节点

    /**
     * 把字符串转为32位符号整数
     */
    public function hash($str)
    {
        return sprintf('%u', crc32($str));
    }

    /**
     * 核心功能
     */
    public function lookup($key)
    {
        $point = $this->hash($key);

        //先取圆环上最小的一个节点,当成结果
        $node = current($this->position);

        // 循环获取相近的节点
        foreach ($this->position as $key => $val) {
            if ($point <= $key) {
                $node = $val;
                break;
            }
        }

        reset($this->position);    //把数组的内部指针指向第一个元素，便于下次查询从头查找

        return $node;
    }

    /**
     * 添加节点
     */
    public function addNode($node)
    {
        if (isset($this->nodes[$node])) return;

        // 添加节点和虚拟节点
        for ($i = 0; $i < $this->mul; $i++) {
            $pos = $this->hash($node . '-' . $i);
            $this->position[$pos] = $node;
            $this->nodes[$node][] = $pos;
        }

        // 重新排序
        $this->sortPos();
    }

    /**
     * 删除节点
     */
    public function delNode($node)
    {
        if (!isset($this->nodes[$node])) return;

        // 循环删除虚拟节点
        foreach ($this->nodes[$node] as $val) {
            unset($this->position[$val]);
        }

        // 删除节点
        unset($this->nodes[$node]);
    }

    /**
     * 排序
     */
    public function sortPos()
    {
        ksort($this->position, SORT_REGULAR);
    }
}
