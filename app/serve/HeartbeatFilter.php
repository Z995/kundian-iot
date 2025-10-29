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

namespace app\serve;

use support\Redis;

class HeartbeatFilter {
    static $threshold=120;//心跳识别阈值（出现次数）
    static $tempExpire=80;//临时缓存过期时间（秒）
    static $permanentExpire=0;//永久缓存过期时间（秒，0表示永不过期）
    static $permanentPrefix="iot-heartbeat:permanent:";//永久缓存键前缀
    static $tempPrefix="iot-heartbeat:temp:";//临时缓存键前缀


    /**
     * 判断消息是否为心跳
     *
     * @param string $deviceId 设备ID
     * @param string $message 消息内容
     * @return bool 是否为心跳
     */
    public static function isHeartbeat(string $message): bool {
        // 优先检查永久缓存
        if (self::checkPermanentCache($message)) {
            return true;
        }
        return false;
    }


    /**
     * 检查永久缓存
     */
    private static function checkPermanentCache( string $message): bool {
        $key = self::$permanentPrefix;
        return Redis::hExists($key, $message);
    }


    /**
     * 处理临时缓存
     */
    public static function processTempCache(string $deviceId, string $message,$is_permanent): bool {
        if ($is_permanent){//直接永久缓存过滤参数
            self::addToPermanentCache($message);
            return true;
        }
        $key = self:: $tempPrefix . $deviceId;

        // 增加计数
        $count = Redis::hIncrBy($key, $message, 1);

        // 设置过期时间
        Redis::expire($key, self::$tempExpire);

        // 达到阈值则加入永久缓存
        if ($count >= self::$threshold) {
            self::addToPermanentCache($message);
            Redis::hDel($key, $message);
            return true;
        }

        return false;
    }

    /**
     * 添加到永久缓存（使用哈希表）
     */
    private static function addToPermanentCache(string $message) {
        $key = self::$permanentPrefix;
        $timestamp = time();

        // 设置哈希字段并更新过期时间
        Redis::hSet($key, $message, $timestamp);

        // 设置键过期时间（可选）
        if (self::$permanentExpire > 0) {
            Redis::expire($key, self::$permanentExpire);
        }
    }

}