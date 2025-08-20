<?php

use Webman\GatewayWorker\Gateway;
use Webman\GatewayWorker\BusinessWorker;
use Webman\GatewayWorker\Register;

return [
    'gateway_tcp' => [
        'handler'     => Gateway::class,
        'listen'      => 'tcp://0.0.0.0:' . config('plugin.webman.gateway-worker.app.tcp_port'),
        'count'       => cpu_count(),
        'reloadable'  => false,
        'constructor' => ['config' => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 2300,
            'pingInterval'    => 61,
            'pingData'        => '',
            'pingNotResponseLimit' => 1,
            'registerAddress' => '127.0.0.1:1236'
        ]]
    ],
    'gateway_ws' => [
        'handler'     => Gateway::class,
        'listen'      => 'websocket://0.0.0.0:' . config('plugin.webman.gateway-worker.app.ws_port'),
        'count'       => cpu_count(),
        'reloadable'  => false,
        'constructor' => ['config' => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 2500,
            'pingInterval'    => 61,
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
        'count'       => cpu_count(),
        'reloadable'  => false,
        'constructor' => ['config' => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 2700,
            'pingInterval'    => 61,
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
            'pingInterval'    => 61,
            'pingData'        => '',
            'pingNotResponseLimit' => 1,
            'registerAddress' => '127.0.0.1:1236',
            'onConnect'       => function () {
            },
        ]]
    ],
    'worker' => [
        'handler'     => BusinessWorker::class,
        'count'       => cpu_count() * 4,
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
