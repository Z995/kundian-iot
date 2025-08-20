<?php

namespace plugin\kundian\base\cloud;


use http\Client;
use think\exception\ValidateException;

class Base
{


    protected $ak;

    protected $ac;

    protected $url;

    protected $client;

    public function __construct()
    {
        $config = get_system_config(['kun_dian_cloud_key', 'kun_dian_cloud_secret']);
        if (empty($config['kun_dian_cloud_key'])||empty($config['kun_dian_cloud_secret'])){
            throw new ValidateException("请先配置坤典云");
        }
        $this->ak = $config['kun_dian_cloud_key'];
        $this->ac = $config['kun_dian_cloud_secret'];
        $this->url = getenv('PASS_DOMAIN', 'https://cloud.cqkundian.com')."/index.php";
        $this->client = new \Workerman\Http\Client();
    }

    /**
     * @param array $params
     * @return string
     */
    protected function signMake($params)
    {
        if (isset($params['sign'])) {
            unset($params['sign']);
        }
        ksort($params);
        return md5(http_build_query($params) . '&secret=' . $this->ac);
    }

    /**
     * @param array $params
     * @return mixed
     */
    protected function package($params)
    {
        $params['_key_'] = $this->ak;
        $params['_timestamp_'] = time();
        $params['_nonce_str_'] = generateRandomString(16);
        $params['_sign_'] = $this->signMake($params);
        return $params;
    }
}