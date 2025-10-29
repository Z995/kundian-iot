/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: kundian_iot_admin/src/api/request.js
 * @description File path and name: kundian_iot_admin/src/api/request.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-06-18 16:37:22
 */
import axios from 'axios';
import { Modal } from '@arco-design/web-vue';
import router from '../router';
//请求地址
let base_url = import.meta.env.VITE_API_URL  	//正式环境
// console.log('接口域名：',import.meta.env.VITE_API_URL);
// console.log('scoket地址：',import.meta.env.VITE_SOCKET_URL);

//正式环境
if( import.meta.env.MODE !='development'){
	base_url = 'https://'+window.location.hostname
}

console.log('接口地址',base_url);

const service = axios.create({
    baseURL:base_url,
    responseType:'json',
    timeout: 10000000, // 单位为毫秒，表示10秒
    headers: {
        'token': localStorage.getItem("_IOT_TOKEN_") || "",
        'content-type': 'application/json; charset=utf-8;', //转换为key=value的格式必须增加content-type
    }
});

// 添加请求拦截器
service.interceptors.request.use(
    (config) => {
        // 在发送请求之前做些什么
        if( config.url ==='/login' ){
            config.headers.token = ''
            config.headers.pid = 0
        }else{
            config.headers.token = localStorage.getItem("_IOT_TOKEN_")
        }
        return config;
    },
    (error) => {
        // 对请求错误做些什么
        return Promise.reject(error);
    }
);


// 添加响应拦截器
service.interceptors.response.use(
    (response) => {
        // 对响应数据做点什么
        let result = response.data
        
        let code = result.code ,msg = result.msg
        if( code !==200 ){
            if( msg ==='token 错误' ){
				Modal.error("登录已过期")
                localStorage.setItem('_IOT_TOKEN_','')
                router.push('/login')
                return false
            }
            return result
        }else{
            return result
        }
    },
    (error) => {
        // 对响应错误做点什么
        return Promise.reject(error);
    }
)
export default service