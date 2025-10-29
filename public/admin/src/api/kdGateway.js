/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: kundian_iot_admin/src/api/kdGateway.js
 * @description File path and name: kundian_iot_admin/src/api/kdGateway.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-06-18 17:06:40
 */
import request from './request.js'
const api_url = '/admin/gateway/'
/**
 * 获取网关型号列表
 * @param data { name:型号名称 }
 * @returns
 */
export function getGatewayMouldList(data){
    return request({
        url:`${api_url}getMarqueList`,
        method:'get',
        params:data
    })
}
/**
 * 获取网关型号详情
 * @param data { id:型号id }
 * @returns
 */
export function getGatewayMouldDetail(data){
    return request({
        url:`${api_url}getMarqueInfo`,
        method:'get',
        params:data
    })
}
/**
 * 保存网关型号信息
 * @param data 
 * @returns
 */
export function saveGatewayMouldData(data){
    return request({
        url:`${api_url}saveMarque`,
        method:'post',
        data:data
    })
}
/**
 * 删除网关型号信息
 * @param data 
 * @returns
 */
export function deleteGatewayMouldData(data){
    return request({
        url:`${api_url}delMarque`,
        method:'post',
        data:data
    })
}
/**
 * 获取网关命令列表
 * @param data 
 * @returns
 */
export function getGatewayCommandList(data){
    return request({
        url:`${api_url}getInstruct`,
        method:'get',
        params:data
    })
}

/**
 * 获取网关设备列表
 * @param data{
	 * gateway_status  状态-1等待初始上线 1 在线 0 离线，2预警
	 * marque_id 型号
	 * name 名称}
 * 
 * @returns
 */
export function getGatewayListData(data){
    return request({
        url:`${api_url}getGatewayList`,
        method:'get',
        params:data
    })
}
/**
 * 删除网关设备
 * @param data
 * @returns
 */
export function deleteGatewayData(data){
    return request({
        url:`${api_url}delGateway`,
        method:'post',
        data:data
    })
}
/**
 * 获取网关设备详情
 * @param data
 * @returns
 */
export function getGatewayDetailData(data){
    return request({
        url:`${api_url}getGatewayInfo`,
        method:'get',
        params:data
    })
}
/**
 * 保存网关数据
 * @param data
 * @returns
 */
export function saveGatewayData(data){
    return request({
        url:`${api_url}saveGateway`,
        method:'post',
        data:data
    })
}
/**
 * 获取初始化网关的mac值
 * @returns
 */
export function getGatewayMac(){
    return request({
        url:`${api_url}getMac`,
        method:'get',
    })
}
/**
 * 获取网关日志数据
 * @returns
 */
export function getGatewayLog(data){
    return request({
        url:`${api_url}getGatewayLog`,
        method:'get',
		params:data
    })
}