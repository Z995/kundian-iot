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
import router from "@/router";
import themeConfig from "@/util/common/kdTheme";
export const useMenuStore = defineStore("menu",()=>{
	const state = reactive({
		layoutStyle:"left-right",	//布局方式 left-right左右布局 top-bottom上下布局
		theme:"blue-black",			//页面主题
		menu:[],					//当前菜单
		openKey:[],					//默认展开的菜单[]
		selectedKeys:[],			//默认选择的菜单
		activeMenu:"",				//当前选择的父级菜单
		breadcrumb:[],				//菜单面包屑
	})
	
	//获取当前路由的菜单展开情况
	function getOpenMenu(path){
		state.openKey =[]
		let breadcrumb = []
		for (let i = 0; i < state.menu.length; i++) {
			if( state.menu[i].path === path && !state.menu[i].children?.length ){
				state.selectedKeys = [state.menu[i].id]
				state.activeMenu = state.menu[i].id
				breadcrumb.push(state.menu[i].name)
				state.breadcrumb = breadcrumb
				localStorage.setItem("__KD_MENU_ACTIVE__",JSON.stringify({
					activeMenu:state.activeMenu,
					selectedKeys:state.selectedKeys,
					breadcrumb:state.breadcrumb
				}))
				break;
			}
			
			for (let j = 0; j < state.menu[i].children?.length; j++) {
				if(state.menu[i].children[j].path === path){
					state.openKey.push(state.menu[i].id)
					state.activeMenu = state.menu[i].id
					state.selectedKeys = [state.menu[i].children[j].id]
					breadcrumb.push(state.menu[i].name,state.menu[i].children[j].name)
					state.breadcrumb = breadcrumb
					localStorage.setItem("__KD_MENU_ACTIVE__",JSON.stringify({
						activeMenu:state.activeMenu,
						selectedKeys:state.selectedKeys,
						breadcrumb:state.breadcrumb
					}))
					break;
				}
			}
		}
	}
	
	//一级菜单切换
	function jumpOnePath(menuData){
		state.activeMenu = menuData.id
		//判断当前菜单是否有二级菜单, 有二级菜单就跳转二级菜单的第一个菜单
		if( menuData.children?.length ){
			router.push({path:menuData.children[0].path})
			getOpenMenu(menuData.children[0].path)
			return;
		}
		jumpTwoPath(menuData)
	}
	
	//二级菜单跳转
	function jumpTwoPath(menuData){
		router.push({path:menuData.path})
		getOpenMenu(menuData.path)
	}
	
	//设置当前主题样式
	function setPageTheme(){
		try{
			const menuStore = useMenuStore()
			let themeStr = localStorage.getItem("__KD_IOT_THEMECONFIG__")
			let style_str = '' , theme = 'blue-black'
			if( themeStr ){
				let data = JSON.parse(themeStr)
				state.theme = data?.theme || 'blue-black'			//设置主题颜色
				state.layoutStyle = data?.layout || 'left-right'	//设置布局方式
			}
			let colors = themeConfig.themeList[state.theme]?.color || themeConfig.themeList['blue-black']
			style_str = `
				--slide-bg:${colors['--slide-bg']};
				--primary-color:${colors['--primary-color']};
				--primary-color-active:${colors['--primary-color-active']};
				--ment-text-color:${colors['--ment-text-color']};
			`
			document.getElementsByTagName('html')[0].setAttribute("style",style_str)
		}catch(e){
			console.log('主题设置错误',e);
		}
	}
	
	//刷新页面获取当前定位导航数据
	function getRefreshPage(){
		if( !state.activeMenu ){
			let historyStr = localStorage.getItem("__KD_MENU_ACTIVE__")
			if( !historyStr ) return;
			let obj = JSON.parse(historyStr)
			state.activeMenu = obj.activeMenu
			state.selectedKeys = obj.selectedKeys
			state.breadcrumb = obj.breadcrumb
		}
	}
	
	return { state,getOpenMenu,jumpOnePath,jumpTwoPath,setPageTheme ,getRefreshPage}
})