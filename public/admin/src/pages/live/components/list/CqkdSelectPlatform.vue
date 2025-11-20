<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/CqkdSelectPlatform.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-15 15:17:27  -->
<template>
<div v-if="state.show">
	<a-modal title="选择监控协议" v-model:visible="state.show" width="600px" :footer="null">
		<div class="platform flex">
			<router-link :to="{path:'/live/addLive',query:{type:'local'}}">
				<div class="platform-item" @click="state.show = false">
					<img class="pi-icon" src="/static/img/live/live-common.png" alt="">
					<div class="pi-name">普通直播流地址</div>
				</div>
			</router-link>
			<router-link :to="{path:'/live/addLive',query:{type:'kun_dian'}}">
				<div class="platform-item" @click="state.show = false">
					<img class="pi-icon" src="/static/img/live/live-gb.png" alt="">
					<div class="pi-name">国标监控</div>
				</div>
			</router-link>
		</div>
	</a-modal>
</div>
</template>

<script setup>
import { reactive } from 'vue';
const state = reactive({
	show:false,
	selectType:'',
	list:[
		{type:'kun_dian',icon:'live-gb.png'},
		{type:3,icon:'haikang.png'},
		{type:6,icon:'lecheng.png'},
	]
})

function showModal(){
	state.show = true
}
defineExpose({
	showModal
})
</script>

<style lang="scss" scoped>
.platform{
	width: 100%;
	gap:30px;
	flex-wrap: wrap;
	justify-content: center;
	height: 200px;
	align-items: center;
	.platform-item{
		width: 200px;
		height: 130px;
		background: #f7f7f7;
		cursor: pointer;
		border-radius: 6px;
		position: relative;
		border: 2px solid #f7f7f7;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		transition:all linear .2s;
		.pi-icon{
			width: 40px;
			height: 40px;
			margin-bottom: 15px;
		}
		.pi-name{
			width: 100%;
			text-align: center;
			font-size: 16px;
			font-weight: 300;
			letter-spacing: 2px;
			color: #1786ff;
		}
		&:hover{
			border: 2px solid #0066FF;
			background:rgba(#0066FF, .1);
			.pi-name{
				font-weight: bold;
			}
			transform: scale(1.1);
		}
	}
}
</style>