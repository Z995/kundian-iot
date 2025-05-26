<?php
return [
    'enable' => true,
    'ip' => '127.0.0.1', // 服务器公网IP地址
    'server_port' => 6767, // HTTP协议监听端口
    'tcp_port' => 6262, // TCP协议监听端口
    'ws_port' => 6161, // WebSocket协议监听端口(ws)
    'wss_port' => 6363, // WebSocket协议监听端口(wss)
    'mqtt_port' => 1883, // MQTT协议监听端口,该端口是你安装的emqx或其他MQTT服务器的端口
    'text_port' => 6868, // Text协议监听端口
    'ssl_cert' => '/www/wwwroot/app_iot/cert/wss.crt', // SSL证书
    'ssl_key' => '/www/wwwroot/app_iot/cert/wss.key', // SSL证书密钥
    'super_code' => 'YS8GSOzeFXd9' // 超级权限注册包,记得修改
];
