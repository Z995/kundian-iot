<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/gatewayAlarmRecord.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 17:52:16  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="网关报警记录" :bordered="false">
			<template #extra>
				<a-space>
					<a-button :disabled="!state.selectId.length" @click="showDeal">批量处理</a-button>
					<a-button :disabled="!state.selectId.length">批量删除</a-button>
				</a-space>
			</template>
			<!-- 搜索参数 -->
			<kd-gateway-alarm-search @search="getSearchData"></kd-gateway-alarm-search>
			<a-table class="kd-small-table mt20" :pagination="false" row-key="id" :bordered="false" :data="state.info.list"
				:columns="[
					{title:'网关名称',dataIndex:'name'},
					{title:'报警内容',slotName:'alarm_con',width:160},
					{title:'报警时间',dataIndex:'time',width:160},
					{title:'报警状态',slotName:'status'},
					{title:'处理状态',slotName:'deal'},
					{title:'操作',slotName:'action',fixed:'right' ,width:130,fixed:'right'},
				]"
				:loading="state.loading"
				:row-selection="{
					type: 'checkbox',
					showCheckedAll: true,
					width:50
				}"
				v-model:selectedKeys="state.selectId"
				:scroll="{x:1200}"
			>
				<template #alarm_con="{record}">
					<a-tooltip :content="record.alarm_con">
						<div class="trigger-con">{{record.alarm_con}}</div>
					</a-tooltip>
				</template>
				<template #status="{record}">
					<span class="normal" v-if="record.status===1">正常</span>
					<span class="warning" v-else>报警</span>
				</template>
				<template #deal="{record}">
					<span v-if="record.deal===1">未处理</span>
					<span v-else>已处理</span>
				</template>
				<template #action="{record}">
					<a-button type="text" class="ml-10" size="mini">删除</a-button>
					<a-popconfirm content="确认处理该报警记录吗?">
						<a-button type="text" size="mini" v-if="record.deal===1">处理</a-button>
					</a-popconfirm>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import KdGatewayAlarmSearch from "./components/KdGatewayAlarmSearch.vue"
const dealRef = ref()
const state = reactive({
	loading:false,
	search:{},
	info:{
		page:1,
		limit:10,
		count:0,
		list:[],
	},
	selectId:[],
})
onMounted(()=>{
	getList(1,10)
})

function getSearchData(data){
	state.search = data
}
function getList(page,limit){
	//  模拟数据
	state.info.list = [
		{id:1,name:'坤典科技测试1号',alarm_con:'网关无线信号强度已高于 弱',time:'2025-06-01 14:24:45',status:1,deal:0},
		{id:2,name:'坤典科技测试1号',alarm_con:'网关无线信号强度已高于 弱',time:'2025-06-01 14:24:45',status:0,deal:1},
		{id:3,name:'坤典科技测试1号',alarm_con:'网关无线信号强度已高于 弱',time:'2025-06-01 14:24:45',status:0,deal:1},
		{id:4,name:'坤典科技测试1号',alarm_con:'网关无线信号强度已高于 弱',time:'2025-06-01 14:24:45',status:1,deal:0},
	]
}

//处理报警
function showDeal(data){
	dealRef.value.show(data)
}
</script>

<style lang="scss" scoped>
.trigger-con{
	width: 150px;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.normal{
	color: #00CC66;
}
.warning{
	color: orangered;
}
</style>