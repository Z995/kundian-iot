<!-- 坤典物联-七牛云云平台 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/qiniu/QiniuPtz.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 11:03:46  -->
<template>
<div class="qn-ptz">
	<div class="qn-header flex">
		<a-select placeholder="选择通道" class="w240">
			<a-option>My(427734222)427734222</a-option>
			<a-option>My(427734222)427734221</a-option>
		</a-select>
		<a-space>
			<a-button type="primary">截取当前画面</a-button>
			<a-button type="primary">开始录制</a-button>
			<a-radio-group v-model="state.play_type" type="button">
				<a-radio :value="1">webrtc</a-radio>
				<a-radio :value="2">flv直播流</a-radio>
			</a-radio-group>
		</a-space>
	</div>
	<div class="qn-ptz-box flex mt15">
		<div class="play-box">
			<cqkd-preview-live></cqkd-preview-live>
		</div>
		<div class="control-board flex">
			<div class="cb-item" v-for="(item,index) in state.controlBtn" :key="index">
				<i class="ri-icon" v-if="item.icon" :class="item.icon"></i>
				<span v-if="item.text">{{ item.text}}</span>
			</div>
		</div>
	</div>
	<!-- 预置位 -->
	<div class="preset-box mt15">
		<div class="f14 fb flex-c mb10">
			<span class="mr10">预置位</span>
			<a-button size="mini" @click="showAddPreset"><i class="ri-add-line"></i>新增预置位</a-button>
		</div>
		<a-table class="kd-small-table" :pagination="false" :bordered="false" :data="state.presetData"
			:columns="[
				{title:'ID',dataIndex:'id',width:100},
				{title:'预置位名称',dataIndex:'name'},
				{title:'操作',slotName:'action'},
			]"
		>
			<template #action="{record}">
				<a-button type="text" size="mini" class="ml-10">调用</a-button>
				<a-popconfirm content="确认删除预置位吗？">
					<a-button type="text" size="mini">删除</a-button>
				</a-popconfirm>
			</template>
		</a-table>
	</div>
	
	<!-- 添加预置位 -->
	<cqkd-add-preset ref="presetRef"></cqkd-add-preset>
</div>
</template>

<script setup>
import CqkdAddPreset from '../CqkdAddPreset.vue';
import CqkdPreviewLive from '@/components/live/CqkdPreviewLive.vue';
import { reactive, ref } from 'vue';
const presetRef = ref()
const state = reactive({
	play_type:1,
	controlBtn:[
		{icon:'ri-arrow-left-up-fill',at:4},
		{icon:'ri-arrow-up-fill',at:0},
		{icon:'ri-arrow-right-up-fill',at:6},
		{icon:'ri-arrow-left-fill',at:2},
		{icon:'',at:-1},
		{icon:'ri-arrow-right-fill',at:3},
		{icon:'ri-arrow-left-down-fill',at:5},
		{icon:'ri-arrow-down-fill',at:1},
		{icon:'ri-arrow-right-down-fill',at:7},
		{icon:'ri-subtract-line',at:9},
		{icon:'',at:-1,text:'变倍'},
		{icon:'ri-add-line',at:8},
		{icon:'ri-subtract-line',at:11},
		{icon:'',at:-1,text:'变焦'},
		{icon:'ri-add-line',at:10},
	],

	presetData:[
		{id:1,name:'前方',channelNo:1},
		{id:2,name:'前方2',channelNo:1},
	],
})

//添加预置位
const showAddPreset = ()=>presetRef.value.show()
</script>

<style lang="scss" scoped>
.qn-ptz{
	width: 100%;
	.qn-header{
		width: 60%;
		justify-content: space-between;
	}
	.qn-ptz-box{
		width: 100%;
	}
	.play-box{
		width: 60%;
		aspect-ratio: 16 / 9;
		background-color: #000;
		position: relative;
	}
	.control-board{
		width: 19vw;
		margin-left: 30px;
		flex-wrap: wrap;
		gap:1vw;
		align-content: flex-start;
		.cb-item{
			width: 5vw;
			height: 5vw;
			border: 2px solid #ebebeb;
			text-align: center;
			line-height: 5vw;
			color: #636363;
			cursor: pointer;
			&:nth-child(5){
				border: none;
			}
			&:nth-child(11){
				border: none;
			}
			&:nth-child(14){
				border: none;
			}
			.ri-icon{
				font-size: 32px;
			}
		}
	}
	
	.preset-box{
		width: 60%;
		
	}
}
</style>