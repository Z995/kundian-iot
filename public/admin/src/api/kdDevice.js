/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: kundian_iot_admin/src/api/kdDevice.js
 * @description File path and name: kundian_iot_admin/src/api/kdDevice.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-06-20 11:59:12
 */
import request from './request.js'
const api_url = '/admin/device/'
const api_product_url = '/admin/product/'
/**
 * 获取设备模板列表
 * @param data { name:名称 }
 * @returns
 */
export function getDeviceMouldList(data){
    return request({
        url:`${api_url}getDeviceTemplateList`,
        method:'get',
        params:data
    })
}
/**
 * 获取设备模板详情
 * @param data { id }
 * @returns
 */
export function getDeviceMouldDetail(data){
    return request({
        url:`${api_url}getDeviceTemplateInfo`,
        method:'get',
        params:data
    })
}
/**
 * 保存设备模板信息
 * @param data
 * @returns
 */
export function saveDeviceMouldData(data){
    return request({
        url:`${api_url}saveDeviceTemplate`,
        method:'post',
        data:data
    })
}
/**
 * 删除设备模板信息
 * @param data { id }
 * @returns
 */
export function deleteDeviceMouldData(data){
    return request({
        url:`${api_url}delDeviceTemplate`,
        method:'post',
        data:data
    })
}
/**
 * 获取设备列表
 * @param data { name:名称 ,device_status：设备状态： 0:在线 1:离线 2:预警}
 * @returns
 */
export function getDeviceList(data){
    return request({
        url:`${api_url}getDeviceList`,
        method:'get',
        params:data
    })
}
/**
 * 获取设备详情
 * @param data { id }
 * @returns
 */
export function getDeviceDetail(data){
    return request({
        url:`${api_url}getDeviceInfo`,
        method:'get',
        params:data
    })
}
/**
 * 保存设备信息
 * @param data
 * @returns
 */
export function saveDeviceData(data){
    return request({
        url:`${api_url}saveDevice`,
        method:'post',
        data:data
    })
}
/**
 * 删除设备信息
 * @param data
 * @returns
 */
export function deleteDevice(data){
    return request({
        url:`${api_url}delDevice`,
        method:'delete',
        data:data
    })
}
/**
 * 获取设备变量数据
 * @param data
 * @returns
 */
export function getDeviceVariableList(data){
    return request({
        url:`${api_url}getVariable`,
        method:'get',
        params:data
    })
}
/**
 * 主动采集变量数据
 * @param data
 * @returns
 */
export function getDeviceVariableValue(data){
    return request({
        url:`${api_url}getDeviceData`,
        method:'get',
        params:data
    })
}
/**
 * 获取设备变量历史数据
 * @param data
 * @returns
 */
export function getDeviceDataHistoryRecord(data){
    return request({
        url:`${api_url}getStatisticalChart`,
        method:'get',
        params:data
    })
}
/**
 * 修改设备变量值
 * @param data
 * @returns
 */
export function updateDeviceDataValue(data){
    return request({
        url:`${api_url}setDeviceData`,
        method:'post',
        data:data
    })
}
/**
 * 获取产品库列表数据
 * @param data
 * @returns
 */
export function getProductList(data){
    return request({
        url:`${api_product_url}getProductList`,
        method:'get',
        params:data
    })
}
/**
 * 保存产品库信息
 * @param data
 * @returns
 */
export function saveProductData(data){
    return request({
        url:`${api_product_url}saveProduct`,
        method:'post',
        data:data
    })
}
/**
 * 删除产品库信息
 * @param data
 * @returns
 */
export function deleteProductData(data){
    return request({
        url:`${api_product_url}delProduct`,
        method:'post',
        data:data
    })
}
/**
 * 修改产品库状态信息
 * @param data
 * @returns
 */
export function updateProductStatus(data){
    return request({
        url:`${api_product_url}updateStatus`,
        method:'post',
        data:data
    })
}
/**
 * 获取产品库详情
 * @param data
 * @returns
 */
export function getProductDetail(data){
    return request({
        url:`${api_product_url}getProductInfo`,
        method:'get',
        params:data
    })
}