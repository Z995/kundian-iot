<?php

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use support\view\Raw;
use support\view\Twig;
use support\view\Blade;
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
    'options' => [
        // 模板引擎类型使用Think
        'type'          => 'Think',
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
        'auto_rule'     => 1,
        // 模板目录名
        'view_dir_name' => 'view',
        // 模板后缀
        'view_suffix'   => 'html',
        // 模板文件名分隔符
        'view_depr'     => DIRECTORY_SEPARATOR,
        // 模板引擎普通标签开始标记
        'tpl_begin'     => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'       => '}',
        // 标签库标签开始标记
        'taglib_begin'  => '<',
        // 标签库标签结束标记
        'taglib_end'    => '>',
        // 输出替换
        'tpl_replace_string'  =>  [
            '__APP__'    => 'http://' . config('plugin.webman.gateway-worker.app.ip') . ':' . config('plugin.webman.gateway-worker.app.server_port'),
            '__STATIC__' => '/static',
            '__UPLOAD__' => '/Uploads'
        ] //注意:修改这里设置后,需要删除runtime目录下的模板缓存文件才可以生效
    ]
];
