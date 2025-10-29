/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: kundian_iot_admin/src/api/kdLive.js
 * @description File path and name: kundian_iot_admin/src/api/kdLive.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-07-07 16:11:57
 */
import request from './request.js'
const api_url = '/admin/monitor/'
/**
 * 获取监控列表
 * @param data
 * @returns
 */
export function getLiveList(data){
    return request({
        url:`${api_url}getMonitorList`,
        method:'get',
        params:data
    })
}


/**
 * 删除监控数据
 * @param data { id }
 * @returns
 */
export function deleteLiveData(data){
    return request({
        url:`${api_url}delMonitor`,
        method:'post',
        data:data
    })
}

/**
 * 保存监控数据
 * @param data 
 * @returns
 */
export function saveLiveData(data){
    return request({
        url:`${api_url}addMonitor`,
        method:'post',
        data:data
    })
}

/**
 * 保存修改监控数据
 * @param data 
 * @returns
 */
export function saveUpdateLiveData(data){
    return request({
        url:`${api_url}updateMonitor`,
        method:'post',
        data:data
    })
}

/**
 * 获取监控详情数据
 * @param data 
 * @returns
 */
export function getLiveDetail(data){
    return request({
        url:`${api_url}getMonitorInfo`,
        method:'get',
        params:data
    })
}
/**
 * 获取监控通道列表
 * @param data 
 * @returns
 */
export function getLiveChanngeList(data){
    return request({
        url:`${api_url}getDeviceChannels`,
        method:'post',
        data:data
    })
}

/**
 * 获取监控截图列表
 * @param data 
 * @returns
 */
export function getLiveSnapshotsList(data){
    return request({
        url:`${api_url}snapshots`,
        method:'post',
        data:data
    })
}

/**
 * 监控截图
 * @param data 
 * @returns
 */
export function saveLiveSnapshots(data){
    return request({
        url:`${api_url}snap`,
        method:'post',
        data:data
    })
}
/**
 * 监控截图-删除
 * @param data 
 * @returns
 */
export function deleteLiveSnapshots(data){
    return request({
        url:`${api_url}delSnapshots`,
        method:'post',
        data:data
    })
}

/**
 * 监控开始拉流
 * @param data 
 * @returns
 */
export function startLivePullStearms(data){
    return request({
        url:`${api_url}deviceStart`,
        method:'post',
        data:data
    })
}

/**
 * 监控停止拉流
 * @param data 
 * @returns
 */
export function stopLivePullStearms(data){
    return request({
        url:`${api_url}deviceStop`,
        method:'post',
        data:data
    })
}
/**
 * 获取监控预置位列表
 * @param data 
 * @returns
 */
export function getLivePresetList(data){
    return request({
        url:`${api_url}devicePresetsList`,
        method:'post',
        data:data
    })
}
/**
 * 监控预置位-保存，删除，调用
 * @param data 
 * @returns
 */
export function changeLivePresetData(data){
    return request({
        url:`${api_url}devicePresets`,
        method:'post',
        data:data
    })
}

/**
 * 监控云台控制
 * @param data 
 * @returns
 */
export function controlLiveDevice(data){
    return request({
        url:`${api_url}deviceControl`,
        method:'post',
        data:data
    })
}

/**
 * 监控录制-获取列表数据
 * @param data 
 * @returns
 */
export function getLiveRecordingList(data){
    return request({
        url:`${api_url}getRecordingList`,
        method:'post',
        data:data
    })
}

/**
 * 监控录制- 开始录制
 * @param data 
 * @returns
 */
export function startLiveRecording(data){
    return request({
        url:`${api_url}startRecording`,
        method:'post',
        data:data
    })
}

/**
 * 监控录制 - 停止录制
 * @param data 
 * @returns
 */
export function stopLiveRecording(data){
    return request({
        url:`${api_url}stopRecording`,
        method:'post',
        data:data
    })
}

/**
 * 监控录制 - 删除录制
 * @param data 
 * @returns
 */
export function deleteLiveRecordingData(data){
    return request({
        url:`${api_url}delRecording`,
        method:'post',
        data:data
    })
}

/**
 * 监控 - 修改监控信息
 * @param data 
 * @returns
 */
export function updateLiveData(data){
    return request({
        url:`${api_url}updateDevice`,
        method:'post',
        data:data
    })
}
/**
 * 监控 - 获取监控空间网关信息
 * @param data 
 * @returns
 */
export function getNamespaceInfo(data){
    return request({
        url:`${api_url}namespacesInfo`,
        method:'post',
        data:data
    })
}
/**
 * 监控 - 获取自动截图，自动录制配置
 * @param data 
 * @returns
 */
export function getLiveAutoShotAndRecordData(data){
    return request({
        url:`${api_url}getMonitorAuto`,
        method:'post',
        data:data
    })
}
/**
 * 监控 - 保存自动截图，自动录制配置
 * @param data 
 * @returns
 */
export function saveLiveAutoShotAndRecordSet(data){
    return request({
        url:`${api_url}saveMonitorAuto`,
        method:'post',
        data:data
    })
}
