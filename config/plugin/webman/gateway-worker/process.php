<?php
/*
 * @Author: 冷丶秋秋秋秋秋 
 * @Date: 2023-02-23 19:47:42
 * @LastEditors: 冷丶秋秋秋秋秋 
 * @LastEditTime: 2023-03-25 16:26:54
 * @FilePath: \yunxiao-app-iot\config\plugin\webman\gateway-worker\process.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

use Webman\GatewayWorker\Gateway;
use Webman\GatewayWorker\BusinessWorker;
use Webman\GatewayWorker\Register;

return [
    'gateway_tcp' => [
        'handler'     => Gateway::class,
        'listen'      => 'tcp://0.0.0.0:' . config('plugin.webman.gateway-worker.app.tcp_port'),
        'count'       => cpu_count() * 4,
        'reloadable'  => false,
        'constructor' => ['config' => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 2300,
            'pingInterval'    => 55,
            'pingData'        => '',
            'pingNotResponseLimit' => 1,
            'registerAddress' => '127.0.0.1:1236'
        ]]
    ],
    'gateway_ws' => [
        'handler'     => Gateway::class,
        'listen'      => 'websocket://0.0.0.0:' . config('plugin.webman.gateway-worker.app.ws_port'),
        'count'       => cpu_count() * 4,
        'reloadable'  => false,
        'constructor' => ['config' => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 2500,
            'pingInterval'    => 55,
            'pingData'        => '',
            'pingNotResponseLimit' => 1,
            'registerAddress' => '127.0.0.1:1236',
            'onConnect'       => function () {
            },
        ]]
    ],
    'gateway_wss' => [
        'handler'     => Gateway::class,
        'listen'      => 'websocket://0.0.0.0:' . config('plugin.webman.gateway-worker.app.wss_port'),
        'count'       => cpu_count() * 4,
        'reloadable'  => false,
        'constructor' => ['config' => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 2700,
            'pingInterval'    => 55,
            'pingData'        => '',
            'pingNotResponseLimit' => 1,
            'registerAddress' => '127.0.0.1:1236',
            'onConnect'       => function () {
            },
        ]],
        'transport'   => 'ssl',
        'context'     => [
            'ssl' => [
                'local_cert'  => config('plugin.webman.gateway-worker.app.ssl_cert'),
                'local_pk'    => config('plugin.webman.gateway-worker.app.ssl_key'),
                'verify_peer' => false,
            ],
        ],
    ],
    'gateway_text' => [
        'handler'     => Gateway::class,
        'listen'      => 'text://0.0.0.0:' . config('plugin.webman.gateway-worker.app.text_port'),
        'count'       => cpu_count(),
        'reloadable'  => false,
        'constructor' => ['config' => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 2800,
            'pingInterval'    => 55,
            'pingData'        => '',
            'pingNotResponseLimit' => 1,
            'registerAddress' => '127.0.0.1:1236',
            'onConnect'       => function () {
            },
        ]]
    ],
    'worker' => [
        'handler'     => BusinessWorker::class,
        'count'       => cpu_count() * 2,
        'constructor' => ['config' => [
            'eventHandler'    => plugin\webman\gateway\Events::class,
            'name'            => 'ChatBusinessWorker',
            'registerAddress' => '127.0.0.1:1236',
        ]]
    ],
    'register' => [
        'handler'     => Register::class,
        'listen'      => 'text://0.0.0.0:1236',
        'count'       => 1, // Must be 1
        'constructor' => []
    ],
];
