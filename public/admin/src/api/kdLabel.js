/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: kundian_iot_admin/src/api/kdLabel.js
 * @description File path and name: kundian_iot_admin/src/api/kdLabel.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-06-19 10:43:40
 */
import request from './request.js'
const api_url = '/admin/label/'
/**
 * 获取标签分类列表
 * @param data
 * @returns
 */
export function getLabelCateList(data){
    return request({
        url:`${api_url}getLabelCategoryList`,
        method:'get',
        params:data
    })
}
/**
 * 保存标签分类数据
 * @param data { name:分类名称 ,id:分类id }
 * @returns
 */
export function saveLabelCateData(data){
    return request({
        url:`${api_url}saveLabelCategory`,
        method:'post',
        data:data
    })
}
/**
 * 删除标签分类数据
 * @param data { id:分类id }
 * @returns
 */
export function deleteLabelCateData(data){
    return request({
        url:`${api_url}delLabelCategory`,
        method:'post',
        data:data
    })
}
/**
 * 获取标签列表
 * @param data
 * @returns
 */
export function getLabelDataList(data){
    return request({
        url:`${api_url}getLabelList`,
        method:'get',
        params:data
    })
}
/**
 * 保存标签数据
 * @param data { name:名称 ,id:标签id，category_id:分类id }
 * @returns
 */
export function saveLabelData(data){
    return request({
        url:`${api_url}saveLabel`,
        method:'post',
        data:data
    })
}
/**
 * 删除标签数据
 * @param data { id:标签id }
 * @returns
 */
export function deleteLabelData(data){
    return request({
        url:`${api_url}delLabel`,
        method:'post',
        data:data
    })
}