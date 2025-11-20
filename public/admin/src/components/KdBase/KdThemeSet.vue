<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/components/KdBase/KdThemeSet.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-07 11:34:06  -->
<template>
<div v-if="state.show">
	<a-drawer :width="340" v-model:visible="state.show" title="主题编辑" :footer="null">
		<a-divider orientation="center">布局切换</a-divider>
		<div class="layout-box flex flex-cb">
			<div class="layout-item flex" :class="{active:state.form.layout =='left-right'}" @click="setLayout('left-right')">
				<div class="li-one"></div>
				<div class="li-two"></div>
				<div class="li-main flex-1">
					<div class="li-top"></div>
					<div class="li-content"></div>
				</div>
			</div>
			<div class="layout-item" :class="{active:state.form.layout =='top-bottom'}" @click="setLayout('top-bottom')">
				<div class="li-two-top"></div>
				<div class="li-two-main flex">
					<div class="li-two"></div>
					<div class="li-content"></div>
				</div>
			</div>
		</div>
		<a-divider orientation="center">主题设置</a-divider>
		<div class="theme-color flex">
			<div class="theme-item" v-for="(item,index) in themeConfig.themeList" :key="index"
				:class="{active:index == state.form.theme }"
				@click="setTheme(index)"
			>
				<div class="color-item flex-c">
					<div class="color-icon" :style="{background:item.color['--slide-bg']}"></div>
					<div class="color-icon" :style="{background:item.color['--primary-color']}"></div>
				</div>
				<div class="name">{{ item.name}}</div>
			</div>
		</div>
	</a-drawer>
</div>
</template>

<script setup>
import { reactive } from 'vue';
import themeConfig from '@/util/common/kdTheme';
import { useMenuStore } from '@/store/menu';
const menuStore = useMenuStore()
const state = reactive({
	show:false,
	form:{
		theme:'blue-black',
		layout:'left-right'
	}
})
function show(){
	state.show = true
	let themeStr = localStorage.getItem("__KD_IOT_THEMECONFIG__")
	if( themeStr ){
		let data = JSON.parse(themeStr)
		state.form.theme = data.theme || 'blue-black'
		state.form.layout = data.layout || 'left-right'
	}
	
}

//设置布局
function setLayout(value){
	state.form.layout = value
	localStorage.setItem("__KD_IOT_THEMECONFIG__",JSON.stringify(state.form))
	menuStore.state.layoutStyle = value
}
//设置主题

function setTheme(value){
	state.form.theme = value
	localStorage.setItem("__KD_IOT_THEMECONFIG__",JSON.stringify(state.form))
	menuStore.setPageTheme()
}
defineExpose({
	show
})
</script>

<style lang="scss" scoped>
.layout-box{
	width: 100%;
	padding: 0 10px;
	height: 110px;
	align-items: flex-start;
	.layout-item{
		width: 130px;
		height:80px;
		gap:5px;
		cursor: pointer;
		padding: 8px;
		box-shadow: 1px 1px 10px #eeeded;
		border-radius: 4px;
		div{border-radius: 4px;}
		.li-one{
			width: 15px;
			height: 100%;
			background: var(--primary-color);
		}
		.li-two{
			width: 15px;
			height: 100%;
			background: var(--primary-color-active);
		}
		.li-main{
			height: 100%;
			.li-top{
				width: 100%;
				height: 15px;
				background: var(--primary-color-active);
			}
			.li-content{
				width: 100%;
				height: calc(100% - 20px);
				margin-top: 5px;
				border: 1px dashed var(--primary-color);
				background: #f8f8f8;
			}
		}
		.li-two-top{
			width: 100%;
			height: 15px;
			background: var(--primary-color);
		}
		.li-two-main{
			width: 100%;
			height: calc(100% - 20px);
			margin-top: 5px;
			.li-content{
				flex: 1;
				height: 100%;
				border: 1px dashed var(--primary-color);
				background: #f8f8f8;
				margin-left: 5px;
			}
		}
	}
	.active{
		border: 2px solid var(--primary-color);
	}
}
.theme-color{
	width: 100%;
	flex-wrap: wrap;
	gap:20px;
	justify-content: center;
	align-items: flex-start;
	.theme-item{
		width: 100px;
		height: 100px;
		border-radius: 10px;
		padding: 10px;
		background: #f4f4f4;
		cursor: pointer;
		border: 2px solid #f4f4f4;
		.color-item{
			justify-content: center;
			width: 100%;
			padding-top: 5px;
		}
		.color-icon{
			width: 40px;
			height: 40px;
			border-radius: 50%;
			&:first-child{
				z-index: 9;
			}
			&:last-child{
				margin-left: -15px;
			}
		}
		.name{
			width: 100%;
			text-align: center;
			margin-top: 8px;
		}
	}
	.active{
		border: 2px solid var(--primary-color);
		color: var(--primary-color);
		background: var(--primary-color-active);
	}
}
</style>