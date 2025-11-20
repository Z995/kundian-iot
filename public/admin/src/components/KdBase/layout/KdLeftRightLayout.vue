<!-- 坤典物联-菜单导航左右结构布局 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/src/components/KdBase/KdLeftRightLayout -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-06 16:35:56  -->
<template>
 <a-layout class="left-right-layout">
	<a-layout-sider :width=" showSecondMenu ? state.slideWidth :100">
		<div class="flex left-right-layout-menu">
			<!-- 一级分类菜单 -->
			<div class="main-menu">
				<div class="logo-box">
					<a-image width="90" src="/static/img/logo.png" class="kd-logo" fit="fill"></a-image>
				</div>
				<template v-for="(item,index) in menuStore.state.menu" :key="index">
					<div class="menu-item flex-c" 
						:class="{active:item.id == menuStore.state.activeMenu}"
						@click="menuStore.jumpOnePath(item)"
						v-if="item.show"
					>
						<i class="iconfont f16" :class="item.icon"></i>
						<span class="ml5">{{ item.name }}</span>
					</div>
				</template>
			</div>
			<!-- 二级分类菜单 -->
			<div class="second-menu" v-if="showSecondMenu">
				<template v-for="(item,index) in menuStore.state.menu" :key="index">
					<div class="main-name f17" v-if="item.id == menuStore.state.activeMenu">{{ item.name}}</div>
					<template v-if="item.children?.length && item.id == menuStore.state.activeMenu">
						<div class="sm-item" v-for="(val,ind) in item.children" :key="ind"
							:class="{active:menuStore.state.selectedKeys.includes(val.id)}"
							@click="menuStore.jumpTwoPath(val)"
						>
							{{ val.name }}
						</div>
					</template>
				</template>
			</div>
		</div>
	</a-layout-sider>
	<a-layout-content>
		<a-layout-header class="left-right-header">
			<div class="header-box flex flex-cb">
				<div>
					<kd-menu-breadcrumb></kd-menu-breadcrumb>
				</div>
				<div>
					<kd-header-user></kd-header-user>
				</div>
			</div>
		</a-layout-header>
		<a-layout-content class="kd-left-right-main-content">
			<router-view v-slot="{ Component }">
				<transition name="fade" mode="out-in">
					<keep-alive :include="configStore.state.keepAlive">
						<component :is="Component" />
					</keep-alive>
				</transition>
			</router-view>
		</a-layout-content>
	</a-layout-content>
 </a-layout>
</template>

<script setup>
import { useConfigStore } from '@/store/config'
import { computed, onMounted, reactive } from 'vue';
import { useMenuStore } from '@/store/menu';
import { useRoute } from 'vue-router';
import menuData from '@/router/menu';

import KdMenuBreadcrumb from '@/components/KdBase/KdMenuBreadcrumb.vue'
import kdHeaderUser from '@/components/KdBase/kdHeaderUser.vue'

const configStore = useConfigStore()
const menuStore = useMenuStore()
const routerData = useRoute()

const state = reactive({
	slideWidth:240,		//侧边栏宽度
})

//是否显示二级菜单
const showSecondMenu = computed(()=>{
	return !['total'].includes(menuStore.state.activeMenu)
})

onMounted(()=>{
	
})

</script>

<style lang="scss" scoped>
.left-right-layout{
	width:100%;
	height: 100%;
	position: fixed;
	left: 0;
	top: 0;
	background: #f8f8f8;
	.left-right-layout-menu{
		width: 100%;
		height: 100%;
		display: flex;
	}
	.main-menu{
		width: 100px;
		height: 100%;
		background: var(--slide-bg);
		overflow: hidden;
		overflow-y: auto;
		border-right: 1px solid #f8f8f8;
		&::-webkit-scrollbar{
			display: none;
		}
		
		.logo-box{
			width: 100%;
			height: 50px;
			padding-top: 10px;
			text-align: center;
			.kd-logo{
				width: 100%;
			}
		}
		.menu-item{
			width: 100%;
			padding: 0 10px;
			height: 50px;
			color: var(--ment-text-color);
			cursor: pointer;
			letter-spacing: 2px;
		}
		.active{
			background: var(--primary-color);
			color: #fff;
		}
	}
	.second-menu{
		width: 140px;
		height: 100%;
		overflow: hidden;
		overflow-y: auto;
		&::-webkit-scrollbar{
			display: none;
		}
		.main-name{
			width: 100%;
			text-align: center;
			height: 50px;
			line-height: 50px;
			box-shadow: 1px 1px 1px #f7f7f7;
		}
		.sm-item{
			width: 100%;
			padding-left: 20px;
			height: 50px;
			cursor: pointer;
			line-height: 50px;
			color: #555;
			&:hover{
				background: var(--primary-color-active);
				color: var(--primary-color);
			}
		}
		.active{
			background: var(--primary-color-active);
			color: var(--primary-color);
		}
	}
}

.left-right-header{
	width: 100%;
	height: 50px;
	background: #fff;
	.header-box{
		width: 100%;
		height: 100%;
		padding: 0 20px;
	}
}
.kd-left-right-main-content{
	width: calc(100% - 20px);
	height: calc(100% - 90px);
	padding: 10px;
	overflow: hidden;
	overflow-y: auto;
}
</style>