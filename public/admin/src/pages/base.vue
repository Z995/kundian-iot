<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/base.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-27 18:02:29  -->
<template>
<a-layout class="kundian-layout">
	<a-layout-header class="kundian-header">
		<div class="kundian-header-box flex-cb">
			<div class="logo-box">
				<img class="kd-logo" src="@/assets/kd/logo.png" alt="" />
			</div>
			<div class="right-box flex-c">
				<a-avatar :size="32" :style="{ backgroundColor: 'rgba(225,225,225,.2)' }">
				    <IconUser />
				</a-avatar>
				<a-dropdown>
				    <span class="ml5">{{state.loginUser}} <icon-down /></span>
					<template #content>
						<a-doption>修改密码</a-doption>
						<a-doption><span @click="logout">退出登录</span></a-doption>
					</template>
				</a-dropdown>
			</div>
		</div>
	</a-layout-header>
	<a-layout class="kundian-content">
		<a-layout-sider theme="dark" class="kundian-menu"
			:collapsed="state.collapsed"
			:collapsible="true"
			@collapse="onCollapse"
		>
			<a-menu
				:style="{ width: '180px', height: '100%' }"
				v-model:open-keys="menuStore.state.openKey"
				v-model:selected-keys="menuStore.state.selectedKeys"
				:collapsed="state.collapsed"
			>
				<template v-for="(item,index) in menuStore.state.menu" :key="index">
					<a-menu-item :key="item.id" v-if="!item.children || !item.children.length" @click="jumpPath(item)">
						<template #icon>
							<img class="menu-icon" :src="`/static/img/menu/${item.id !== menuStore.state.activeMenu ?item.icon:item.icon+'-active'}.png`" alt="">
						</template>
						<span>{{item.name}}</span>
					</a-menu-item>
					<a-sub-menu :key="item.id" v-else>
						<template #icon>
							<img class="menu-icon" :src="`/static/img/menu/${item.id !== menuStore.state.activeMenu ?item.icon:item.icon+'-active'}.png`" alt="">
						</template>
						<template #title>{{item.name}}</template>
						<a-menu-item :key="val.id" v-for="(val,ind) in item.children"  @click="jumpPath(val,item)">
							{{ val.name }}
						</a-menu-item>
					</a-sub-menu>
				</template>
			</a-menu>
		</a-layout-sider>
		<a-layout-content class="kundian-main">
			<router-view v-slot="{ Component }">
				<transition name="fade" mode="out-in">
					<keep-alive :include="configStore.state.keepAlive">
						<component :is="Component" />
					</keep-alive>
				</transition>
			</router-view>
		</a-layout-content>
	</a-layout>
</a-layout>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import menuData from '../router/menu';
import router from '../router';
import { Modal } from '@arco-design/web-vue';
import { useDeviceStore } from '@/store/device'
import { useConfigStore } from '@/store/config'
import { useMenuStore } from '@/store/menu';
import { useRoute } from 'vue-router';
const routerData = useRoute()
const state = reactive({
	collapsed:false,
	loginUser:localStorage.getItem('_IOT_USER_'),
})
const deviceStore = useDeviceStore()
const configStore = useConfigStore()
const menuStore = useMenuStore()
onMounted(()=>{
	//设置可用屏幕宽度
	configStore.state.innerWidth = window.innerWidth-220
	menuStore.state.menu = Object.assign(menuData)
	menuStore.getOpenMenu(routerData.fullPath)
	
	//链接socket
	deviceStore.connect()
})
function onCollapse(val){
	state.collapsed = val
	configStore.state.innerWidth = val ?window.innerWidth-220 : window.innerWidth-680
}

/**
 * 页面跳转
 * @param {Object} menu 当前点击的菜单
 * @param {Object} parentMenu	上级菜单
 */
function jumpPath(menu,parentMenu=null){
	if( !menu.path ) return
	router.push({path:menu.path})
	menuStore.getOpenMenu(menu.path)
}

//退出登录
function logout(){
	Modal.confirm({
		title: '提示',
		content: '确认要退出登录吗？',
		titleAlign:"start",
		onOk:()=>{
			localStorage.removeItem("_IOT_TOKEN_")
			localStorage.removeItem("_IOT_USER_")
			router.push("/login")
		},
	});
}
</script>

<style lang="scss" scoped>
.kundian-layout{
	width:100%;
	height: 100%;
	border: 1px solid ;
	position: fixed;
	left: 0;
	top: 0;
	.kundian-header{
		width:100%;
		height: 50px;
		background: #1c202b;
		
		.logo-box{
			width: 175px;
			padding: 5px 20px;
			.kd-logo{
				width: 100%;
				max-height: 40px;
			}
		}
		.right-box{
			padding-right: 20px;
			color: #fff;
			cursor: pointer;
		}
	}
	.kundian-content{
		height: calc(100% - 50px);
		background: #1c202b;
	}
	.kundian-menu{
		background: #1c202b;
		overflow: hidden;
		overflow-y: auto;
		&::-webkit-scrollbar{
			display: none;
		}
		.arco-menu-dark{
			background: #1c202b;
		}
		
		.menu-icon{
			width: 16px;
			height: 15px;
		}
	}
	.kundian-main{
		background: #f2f2f2;
		padding: 10px;
		border-radius: 10px 0 0 0;
	}
}
// 样式重构
:deep(.arco-menu-inline .arco-menu-inline-header){
	background: #1c202b;
}
:deep(.arco-menu-dark .arco-menu-item){
	background: #1c202b;
}
:deep(.arco-menu-dark .arco-menu-item.arco-menu-selected){
	background: rgba(#7599f5, .1);
}
:deep(.arco-menu-indent){
	width: 35px;
}
:deep(.arco-layout-sider-children){
	height: calc(100% - 48px);
	&::-webkit-scrollbar{
		display: none;
	}
}
:deep(.arco-menu-inner){
	&::-webkit-scrollbar{
		display: none;
	}
}
:deep(.arco-layout-sider-has-trigger){
	padding-bottom: 0;
}
</style>