<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/statistics/components/history/KdHistoryDataList.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 16:50:17  -->
<template>
<div class="history-list">
	<!-- <div class="data-type flex mb20">
		<div class="type-item" :class="{active:state.type ===1}" @click="changeType(1)">多变量模式</div>
		<div class="type-item" :class="{active:state.type ===2}" @click="changeType(2)">单变量模式</div>
	</div> -->
	<div class="data-type flex mt20 mb10">
		<div class="type-item" 
			v-for="(item,index) in state.dataList" :key="index"
			:class="{active:state.data_id === item.id }"
			@click="state.data_id = item.id"
		>
			{{ item.name }}
		</div>
	</div>
	<template v-for="(item,index) in state.logList " :key="index">
		<div v-if="item.variable.id === state.data_id">
			<a-table class="kd-small-table" :pagination="{pageSize:40}" 
				row-key="id" :bordered="false" :data="item.log"
				:columns="state.columns"
			></a-table>
		</div>
	</template>
</div>
</template>

<script setup>
import { reactive } from 'vue';

const state = reactive({
	loading:false,
	type:1,	//1多变量模式 2单变量模式
	info:{
		list:[],
		page:1,
		limit:10,
		count:0,
	},
	columns:[
		{title:'数值',dataIndex:'val'},
		{title:'更新时间',dataIndex:'create_time'},
	],
	dataList:[],	//变量列表
	logList:[],		//变量数据
	data_id:0,
})

function refreshData(list,logList){
	state.dataList = list
	state.logList = logList
	if( list.length ){
		state.data_id = list[0].id
	}
}
defineExpose({
	refreshData
})
</script>

<style lang="scss" scoped>
.history-list{
	width: 100%;
	margin-top: 30px;
	min-height: 300px;
	.data-type{
		width: 100%;
		height: 36px;
		border-bottom: 1px solid #f4f4f4;
		.type-item{
			padding: 0 20px;
			height: 100%;
			line-height: 36px;
			border:1px solid #f4f4f4;
			border-bottom: none;
			cursor: pointer;
		}
		.active{
			border: 1px solid #0066FF;
			background: #0066FF;
			color: #fff;
		}
	}
}
</style>