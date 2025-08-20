<?php
return [
    'enable' => true,
    'ip' => '127.0.0.1', // 服务器公网IP地址
    'server_port' => 6767, // HTTP协议监听端口
    'tcp_port' => 6262, // TCP协议监听端口
    'ws_port' => 6161, // WebSocket协议监听端口(ws)
    'wss_port' => 6363, // WebSocket协议监听端口(wss)
    'mqtt_port' => 1883, // MQTT协议监听端口,该端口是你安装的emqx或其他MQTT服务器的端口
    'mqtt_client_port' => 5103, // MQTT客户端所在进程的监听端口,用于管理mqtt设备的订阅、发布和销毁
    'mqtt_client_username' => 'admin', // MQTT客户端的管理员用户名,需跟emqx的配置保持一致:访问控制-客户端认证
    'mqtt_client_password' => '123456', // MQTT客户端的管理员密码,需跟emqx的配置保持一致:访问控制-客户端认证
    'mqtt_client_id' => 'HiWADmy42NB4', //MQTT客户端的管理员client_id(不能跟其他client_id重复),需跟emqx的配置保持一致:访问控制-客户端认证
    'mqtt_client_api_url' => 'http://60.247.225.87:18083/', //MQTT客户端的管理员api_url,带/结尾.EMQX的api端口默认18083,如果修改了端口,需要修改这里.如果不是本机,需要修改为实际的公网ip地址
    'mqtt_client_api_key' => 'c7ad44cbad762a5d', //MQTT客户端的管理员api_key,在emqx中创建:系统设置-API密钥
    'mqtt_client_api_secret' => 'j7SGdQpKIO3sUnTFJDJjhgVl5eyv2PpWoS2HFSYEdBP', //MQTT客户端的管理员api_secret,在emqx中创建:系统设置-API密钥
    //管理员的设置是为了接收所有mqtt设备的上报,为了安全上线前最好进行修改，如果更改(mqtt_client_username、mqtt_client_password、mqtt_client_id)的话,
    //需要同步修改emqx的配置的sql语句,路径在访问控制-客户端认证-mysql里,否则无法连接
    //mysql的sql语句如下:
    // SELECT
    //     CASE
    //         WHEN ${username} = "admin" AND ${clientid} = "HiWADmy42NB4" THEN "123456"
    //         ELSE password
    //     END as password
    // FROM qf_iot
    // WHERE
    //     (username = ${username} AND code = ${clientid} AND del = 0)
    //     OR (${username} = "admin" AND ${clientid} = "HiWADmy42NB4")
    // LIMIT 1
    //注意不要启用客户端授权
    //更改后需restart重启项目
    'text_port' => 6868, // Text协议监听端口
    'super_admin_id' => 1, // 超级管理员ID
    'ssl_cert' => getenv('ssl_cert'), // SSL证书
    'ssl_key' =>getenv('ssl_key'), // SSL证书密钥
    'super_code' => 'YS8GSOzeFXd9TTT', // 超级权限注册包,记得修改
    'version' => '2.1.0' // 版本号
];
