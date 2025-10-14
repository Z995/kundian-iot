<?php
// 坤典开源物联网系统
// @link `https://www.cqkd.com` 
// @description 软件开发团队为 重庆坤典科技有限公司
// @description The software development team is Chongqing Kundian Technology Co., Ltd.
// @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
// @description 软件版权归 重庆坤典科技有限公司 所有
// @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
// @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
// @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
// @Date: 2025/10/9/上午10:09

namespace plugin\kundian\base;


use app\serve\RedisServices;
use app\validate\api\ApiValidate;
use extend\Request;
use think\exception\ValidateException;
use think\Validate;

/**
 * 插件控制器需继承
 */
class BaseController
{
    public function validate($class, $name="")
    {
        $data = Request::param();
        /***  @var Validate $validate */
        $validate = new $class;
        if (!$validate->scene($name)->check($data)) {
            throw new ValidateException($validate->getError());
        }
        return $data;
    }

    /**
     * 快速获取token中信息
     * @return array|string
     */
    public function getAdminInfo()
    {
        return (new RedisServices())->checkToken();
    }

    /**
     * 快速获取token中信息
     * @return array|string
     */
    public function adminId()
    {
        $result = (new RedisServices())->checkToken();
        return $result["id"];
    }


}