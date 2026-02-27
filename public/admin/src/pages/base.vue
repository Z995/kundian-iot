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
<div>
	<!-- 左右布局样式 -->
	<template v-if="menuStore.state.layoutStyle =='left-right'">
		<kd-left-right-layout></kd-left-right-layout>
	</template>
	<!-- 上下布局样式 -->
	<template v-if="menuStore.state.layoutStyle =='top-bottom'">
		<kd-top-bottom-layout></kd-top-bottom-layout>
	</template>
</div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import menuData from '../router/menu';
import { useDeviceStore } from '@/store/device'
import { useMenuStore } from '@/store/menu';
import { useRoute } from 'vue-router';
import KdLeftRightLayout from '@/components/KdBase/layout/KdLeftRightLayout.vue'
import KdTopBottomLayout from '@/components/KdBase/layout/KdTopBottomLayout.vue'
import { useUsrCloudState } from '@/store/usrColud'

const routerData = useRoute()
const state = reactive({
})
const deviceStore = useDeviceStore()
const menuStore = useMenuStore()
const usrCloudStore = useUsrCloudState()

onMounted(()=>{
	menuStore.getRefreshPage()
	menuStore.state.menu = Object.assign(menuData)
	menuStore.getOpenMenu(routerData.fullPath)
	//链接socket
	deviceStore.connect()

	//初始化有人云sdk
	//usrCloudStore.initSdk()
})

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
	// height: calc(100% - 48px);
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