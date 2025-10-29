/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: kundian_iot_admin/src/api/kdStatistics.js
 * @description File path and name: kundian_iot_admin/src/api/kdStatistics.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-07-23 09:31:55
 */

import request from './request.js'
const api_url = '/admin/data/'
/**
 * 获取总览统计数据
 * @param data
 * @returns
 */
export function getHomeTotal(){
    return request({
        url:`${api_url}generalSituation`,
        method:'get',
    })
}
/**
 * 获取网关上下线记录
 * @param data
 * @returns
 */
export function getGatewayOnlineLogList(data){
    return request({
        url:`${api_url}gatewayOnlineLog`,
        method:'get',
        params:data
    })
}

/**
 * 获取设备上下线记录
 * @param data
 * @returns
 */
export function getDeviceOnlineLogList(data){
    return request({
        url:`${api_url}deviceOnlineLog`,
        method:'get',
        params:data
    })
}

/**
 * 获取变量报警记录
 * @param data
 * @returns
 */
export function getDataAlarmRecord(data){
    return request({
        url:`${api_url}warningLog`,
        method:'get',
        params:data
    })
}


/**
 * 处理变量报警
 * @param data
 * @returns
 */
export function dealDataAlarmStatus(data){
    return request({
        url:`${api_url}dealWarningLog`,
        method:'post',
        data:data
    })
}


/**
 * 删除变量报警
 * @param data
 * @returns
 */
export function deleteDataAlarm(data){
    return request({
        url:`${api_url}delWarningLog`,
        method:'post',
        data:data
    })
}


/**
 * 获取设备联动记录
 * @param data
 * @returns
 */
export function getDeviceLinkRecord(data){
    return request({
        url:`${api_url}linkageLog`,
        method:'get',
        params:data
    })
}


/**
 * 删除设备联动记录
 * @param data
 * @returns
 */
export function deleteDeviceLinkLog(data){
    return request({
        url:`${api_url}delLinkageLog`,
        method:'get',
        params:data
    })
}
/**
 * 获取设备状态统计
 * @param data
 * @returns
 */
export function getTotalByDeviceStatus(){
    return request({
        url:`${api_url}deviceStatusStatistics`,
		method:'get',
    })
}

/**
 * 获取设备标签统计
 * @param data
 * @returns
 */
export function getTotalByDeviceLabel(){
    return request({
        url:`${api_url}deviceLabelStatistics`,
		method:'get',
    })
}

/**
 * 获取网关在线状态统计
 * @param data
 * @returns
 */
export function getTotalByGatewayOnlineStatus(){
    return request({
        url:`${api_url}gatewayStatusStatistics`,
		method:'get',
    })
}

/**
 * 获取设备报警记录统计
 * @param data
 * @returns
 */
export function getTotalByDeviceAlarm(){
    return request({
        url:`${api_url}getDeviceWarningRecord`,
		method:'get',
    })
}

/**
 * 获取新增设备统计图数据
 * @param data
 * @returns
 */
export function getLineByDeviceNew(data){
    return request({
        url:`${api_url}newDeviceStatistics`,
		method:'get',
		params:data
    })
}
/**
 * 获取新增网关统计图数据
 * @param data
 * @returns
 */
export function getLineByGatewayNew(data){
    return request({
        url:`${api_url}newGatewayStatistics`,
		method:'get',
		params:data
    })
}
