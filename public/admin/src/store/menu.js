/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称:  kundian_iot_admin/src/store/menu.js
 * @description File path and name:  kundian_iot_admin/src/store/menu.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-07-28 09:18:38
 */

import { defineStore } from "pinia";
import { reactive } from "vue";
export const useMenuStore = defineStore("menu",()=>{
	const state = reactive({
		menu:[],		//当前菜单
		openKey:[],		//默认展开的菜单[]
		selectedKeys:[],//默认选择的菜单
		activeMenu:"",	//当前选择的父级菜单
	})
	
	//获取当前路由的菜单展开情况
	function getOpenMenu(path){
		console.log(path,state.menu);
		state.openKey =[]
		for (let i = 0; i < state.menu.length; i++) {
			if( state.menu[i].path === path && !state.menu[i].children?.length ){
				state.selectedKeys = [state.menu[i].id]
				state.activeMenu = state.menu[i].id
				break;
			}
			
			for (let j = 0; j < state.menu[i].children?.length; j++) {
				if(state.menu[i].children[j].path === path){
					state.openKey.push(state.menu[i].id)
					state.activeMenu = state.menu[i].id
					state.selectedKeys = [state.menu[i].children[j].id]
					break;
				}
			}
		}
		// console.log(state.openKey,state.selectedKeys,state.activeMenu);
	}
	
	return { state,getOpenMenu }
})