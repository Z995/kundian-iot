/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: kundian_iot_admin/src/api/kdConfig.js
 * @description File path and name: kundian_iot_admin/src/api/kdConfig.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-07-29 09:36:25
 */
import request from './request.js'
const api_url = '/admin/system/'
const auth_url = 'admin/authorize/'

/**
 * 保存配置信息
 * @param {Object} data
 */
export function getConfigData(data){
    return request({
        url:`${api_url}getConfig`,
        method:'post',
        data:{
			key:data
		}
    })
}
/**
 * 获取配置信息
 * @param {Object} data
 */
export function setConfigData(data){
    return request({
        url:`${api_url}setConfig`,
        method:'post',
        data:data
    })
}


/**
 * 坤典平台授权
 * @param {Object} data
 */
export function authKdPlatform(data){
    return request({
        url:`${auth_url}kunDianAuthorize`,
        method:'post',
        data:data
    })
}

/**
 * 坤典平台发送验证码
 * @param {Object} data
 */
export function sendLoginCode(data){
    return request({
        url:`${auth_url}kunDianSmsLoginCode`,
        method:'post',
        data:data
    })
}

