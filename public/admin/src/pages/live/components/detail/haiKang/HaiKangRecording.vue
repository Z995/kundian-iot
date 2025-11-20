<!-- 坤典物联-萤石云本地录像 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/haiKang/HaiKangRecording.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 15:23:34  -->
<template>
<div class="hk-recording">
	<a-space>
		<a-range-picker
		    showTime
			class="w360"
			shortcuts-position="left"
			format="YYYY-MM-DD HH:mm"
			v-model="state.search.time"
			:allow-clear="false"
			:shortcuts="state.rangeShortcuts"
		/>
		<a-button type="primary">查询</a-button>
		<a-button>重置</a-button>
	</a-space>
	<a-table class="mt15" :pagination="false" :bordered="false" :data="state.info.list" 
		:columns="[
			{title:'设备通道号',dataIndex:'channelNo',width:200},
			{title:'录像时间',slotName:'time'},
			{title:'回放源',slotName:'recType'},
			{title:'文件类型',dataIndex:'localType'},
			{title:'操作',slotName:'action'},
		]"
	>
		<template #recType="{record}">
			<span v-if="record.recType ===0">-</span>
			<span v-if="record.recType ===1">云存储</span>
			<span v-if="record.recType ===2">本地录像</span>
		</template>
		<template #time="{record}">
			<span>{{record.startTime}}~{{record.endTime}}</span>
		</template>
		<template #action="{record}">
			<a-button type="text" size="mini" class="ml-10">回放</a-button>
		</template>
	</a-table>
	<kd-pager :page-data="state.info" :show-size="false"></kd-pager>
</div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import dayjs from 'dayjs'
const state = reactive({
	search:{
		time:[],
	},
	rangeShortcuts:[
		{label: '最近七日',value: () => [dayjs().subtract(7, 'day'),dayjs()]},
		{label: '最近一个月',value: () => [dayjs().subtract(1, 'month'),dayjs()]},
		{label: '最近三个月',value: () => [dayjs().subtract(3, 'month'),dayjs()]},
		{label: '最近一年',value: () => [dayjs().subtract(1, 'year'),dayjs()]},
	],
	info:{
		page:1,
		count:0,
		list:[],
		limit:20,
	}
})

onMounted(()=>{
	//根据屏幕决定查询数量
	state.info.limit = Math.floor((window.innerWidth-250)/230) *3
	//默认显示一小时
	state.search.time = [
		dayjs().subtract(1, 'day').format("YYYY-MM-DD HH:mm"),
		dayjs().format("YYYY-MM-DD HH:mm")
	]
	
	state.info.list = [
		{
			recType: 1,
			channelNo: 1,
			startTime: '2025-01-01 12:00',
			endTime: '2025-01-01 12:30',
			localType: "ALLEVENT",
			id: 20432171600,
			fileId: "20432171600",
			fileSize: 4011828,
			createTime: "2016-08-22 13:59:13",
		},
		{
			recType: 2,
			channelNo: 1,
			startTime: '2025-01-01 12:00',
			endTime: '2025-01-01 12:30',
			localType: "ALLEVENT",
			id: 20432171600,
			fileId: "20432171600",
			fileSize: 4011828,
			createTime: "2016-08-22 13:59:13",
		}
	]
})

</script>

<style>
</style>