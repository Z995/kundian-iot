/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: kundian_iot_admin/src/store/config.js
 * @description File path and name: kundian_iot_admin/src/store/config.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-07-16 14:52:44
 */
import { defineStore  } from 'pinia'
import { reactive } from 'vue'
export const useConfigStore = defineStore("config",()=>{
	const state = reactive({
		innerWidth:1920,	//可用屏幕宽度
		keepAlive:[],       //页面缓存
		tkToken:'68404e269217bb006fd5977410da694a',	//天地图tk
	})
	
	/**
	 * 设备缓存页面
	 * @param name      组件名称
	 * @param isPush    是否缓存
	 */
	function setKeepLive(name,isPush){
		let index = state.keepAlive.indexOf(name)
		if( isPush ){
			if( index ===-1 )  state.keepAlive.push(name)
			return
		}
		if( index>=0){
			state.keepAlive.splice(index,1)
		}
	}
	return { state,setKeepLive }
})