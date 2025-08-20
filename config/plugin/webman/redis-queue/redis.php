<?php
return [
    'default' => [
        'host' => 'redis://'.getenv('REDIS_HOST').":". getenv('REDIS_PORT'),
        'options' => [
            'auth' => getenv('REDIS_PWD'),       // 密码，字符串类型，可选参数
            'db' => getenv('REDIS_SELECT')??0,            // 数据库
            'prefix' => '',       // key 前缀
            'max_attempts'  => 3, // 消费失败后，重试次数
            'retry_seconds' => 3, // 重试间隔，单位秒
        ]
    ],
];
