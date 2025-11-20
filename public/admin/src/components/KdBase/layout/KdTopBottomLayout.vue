<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/components/KdBase/layout/KdTopBottomLayout.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-07 10:31:48  -->
<template>
<a-layout class="top-bottom-layout">
	<a-layout-header class="top-bottom-header">
		<div class="header-box flex flex-cb">
			<div class="logo-box">
				<a-image width="120" src="/static/img/logo.png" class="kd-logo" fit="fill"></a-image>
			</div>
			<div class="menu-list flex">
				<template v-for="(item,index) in menuStore.state.menu" :key="index">
					<div class="menu-item" 
						:class="{active:menuStore.state.activeMenu== item.id}"
						@click="menuStore.jumpOnePath(item)"
						v-if="item.show"
					>
						{{ item.name}}
					</div>
				</template>
			</div>
			<view class="header-right">
				<kd-header-user></kd-header-user>
			</view>
		</div>
	</a-layout-header>
	<a-layout class="top-bottom-content flex">
		<a-layout-sider :width="160" class="slide-menu" v-if="showSecondMenu">
			<template v-for="(item,index) in menuStore.state.menu" :key="index">
				<template v-if="menuStore.state.activeMenu== item.id">
					<div class="menu-item" v-for="(val,ind) in item.children" :key="ind"
						:class="{active:menuStore.state.selectedKeys.includes(val.id)}"
						@click="menuStore.jumpTwoPath(val)"
					>
						{{ val.name }}
					</div>
				</template>
			</template>
		</a-layout-sider>
		<a-layout-content class="top-bottom-main-content">
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
import { useConfigStore } from '@/store/config'
import { computed, onMounted, reactive } from 'vue';
import { useMenuStore } from '@/store/menu';
import { useRoute } from 'vue-router';
import menuData from '@/router/menu';

import kdHeaderUser from '@/components/KdBase/kdHeaderUser.vue'

const configStore = useConfigStore()
const menuStore = useMenuStore()
const routerData = useRoute()

//是否显示二级菜单
const showSecondMenu = computed(()=>{
	return !['total'].includes(menuStore.state.activeMenu)
})

</script>

<style lang="scss" scoped>
.top-bottom-layout{
	width:100%;
	height: 100%;
	position: fixed;
	left: 0;
	top: 0;
	background: #f8f8f8;
	.top-bottom-header{
		width: 100%;
		height: 50px;
		background: var(--slide-bg);
		.header-box{
			width: 100%;
			height: 100%;
			padding: 0 20px 0 0;
			gap:30px;
			.logo-box{
				width: 130px;
				height: 100%;
				padding: 6px 10px;
			}
			
			.menu-list{
				flex: 1;
				color: var(--ment-text-color);
				height: 100%;
				.menu-item{
					text-align: center;
					width:80px;
					height: 100%;
					line-height: 50px;
					cursor: pointer;
				}
				.active{
					background: var(--primary-color);
					color: #fff;
				}
			}
			.header-right{
				color: #fff;
			}
		}
	}
	.top-bottom-content{
		width: 100%;
		height: calc(100% - 50px);
		.slide-menu{
			width: 100%;
			.menu-item{
				width: 100%;
				height: 50px;
				padding-left: 30px;
				line-height: 50px;
				cursor: pointer;
			}
			.active{
				background: var(--primary-color);
				color: #fff;
			}
		}
		.top-bottom-main-content{
			height: 100%;
			overflow: hidden;
			overflow-y: auto;
			padding: 10px;
		}
	}
}
</style>