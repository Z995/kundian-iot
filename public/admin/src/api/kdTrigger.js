/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: kundian_iot_admin/src/api/kdTrigger.js
 * @description File path and name: kundian_iot_admin/src/api/kdTrigger.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-07-10 09:53:16
 */
import request from './request.js'
const api_url = '/admin/alarm/'
/**
 * 获取模板触发器数据列表
 * @param data { name:名称 ,template_name:模板名称 }
 * @returns
 */
export function getMouldTriggerList(data){
    return request({
        url:`${api_url}getTemplateTriggerList`,
        method:'get',
        params:data
    })
}
/**
 * 获取模板触发器详情
 * @param data { id }
 * @returns
 */
export function getMouldTriggerDetail(data){
    return request({
        url:`${api_url}getTemplateTriggerInfo`,
        method:'get',
        params:data
    })
}
/**
 * 保存模板触发器数据
 * @param data
 * @returns
 */
export function saveMouldTriggerData(data){
    return request({
        url:`${api_url}saveTemplateTrigger`,
        method:'post',
        data:data
    })
}
/**
 * 修改模板触发器状态
 * @param data
 * @returns
 */
export function updateMouldTriggerStatus(data){
    return request({
        url:`${api_url}saveTemplateTriggerStatus`,
        method:'post',
        data:data
    })
}
/**
 * 删除模板触发器数据
 * @param data {id}
 * @returns
 */
export function deleteMouldTriggerData(data){
    return request({
        url:`${api_url}delTemplateTrigger`,
        method:'post',
        data:data
    })
}

/**
 * 获取独立触发器数据列表
 * @param data { name:名称 ,device_name:设备名称 }
 * @returns
 */
export function getIndependentTriggerList(data){
    return request({
        url:`${api_url}getIndependenceTriggerList`,
        method:'get',
        params:data
    })
}
/**
 * 获取独立触发器详情
 * @param data { id }
 * @returns
 */
export function getIndependentTriggerDetail(data){
    return request({
        url:`${api_url}getIndependenceTriggerInfo`,
        method:'get',
        params:data
    })
}
/**
 * 保存独立触发器数据
 * @param data
 * @returns
 */
export function saveIndependentTriggerData(data){
    return request({
        url:`${api_url}saveIndependenceTrigger`,
        method:'post',
        data:data
    })
}
/**
 * 修改独立触发器状态
 * @param data
 * @returns
 */
export function updateIndependentTriggerStatus(data){
    return request({
        url:`${api_url}saveIndependenceTriggerStatus`,
        method:'post',
        data:data
    })
}
/**
 * 删除独立触发器数据
 * @param data {id}
 * @returns
 */
export function deleteIndependentTriggerData(data){
    return request({
        url:`${api_url}delIndependenceTrigger`,
        method:'post',
        data:data
    })
}