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

        $this->ak = $config['kun_dian_cloud_key'];
        $this->ac = $config['kun_dian_cloud_secret'];
        $this->url = getenv('PASS_DOMAIN')??"https://cloud.cqkundian.com"."/index.php";
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
        if (empty($this->ak)||empty($this->ac)){
            throw new ValidateException("请先配置坤典云");
        }
        $params['_key_'] = $this->ak;
        $params['_timestamp_'] = time();
        $params['_nonce_str_'] = generateRandomString(16);
        $params['_sign_'] = $this->signMake($params);
        return $params;
    }
}