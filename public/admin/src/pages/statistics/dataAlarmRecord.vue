<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/dataAlarmRecord.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 14:26:27  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="变量报警记录" :bordered="false">
			<!-- 搜索参数 -->
			<kd-alarm-record-search type="dataAlarmRerocrd" @search="getSearchData"></kd-alarm-record-search>
			<a-table class="kd-small-table mt20" :pagination="false" row-key="id" :bordered="false" :data="state.info.list"
				:columns="[
					{title:'ID',dataIndex:'id',width:80},
					{title:'设备名称',dataIndex:'name'},
					{title:'触发器名称',dataIndex:'trigger_name'},
					{title:'触发器类型',slotName:'trigger_type'},
					{title:'从机名称',dataIndex:'subordinate_name'},
					{title:'变量',dataIndex:'variable_name'},
					{title:'当前值',dataIndex:'value'},
					{title:'触发条件',slotName:'trigger_con',width:160},
					{title:'报警时间',dataIndex:'create_time',width:160},
					{title:'报警状态',slotName:'is_warning'},
					{title:'处理状态',slotName:'status'},
					{title:'操作',slotName:'action',fixed:'right' ,width:130},
				]"
				:loading="state.loading"
				:scroll="{x:1400}"
			>
				<template #trigger_type="{record}">
					<span v-if="record.trigger_type ===1">设备</span>
				</template>
				<template #trigger_con="{record}">
					<div v-if="record.trigger_condition">
						<a-tooltip :content="record.trigger_condition">
							<div class="trigger-con">{{record.trigger_condition}}</div>
						</a-tooltip>
					</div>
					<div v-else> - </div>
				</template>
				<template #is_warning="{record}">
					<span class="normal" v-if="record.is_warning===0">正常</span>
					<span class="warning" v-else>报警</span>
				</template>
				<template #status="{record}">
					<span v-if="record.status===0">未处理</span>
					<span v-else>已处理</span>
				</template>
				<template #action="{record}">
					<a-popconfirm content="确认删除报警记录吗？" @ok="delData(record.id)">
						<a-button type="text" class="ml-10" size="mini">删除</a-button>
					</a-popconfirm>
					<a-popconfirm content="确认已处理了吗？" @ok="dealData(record.id)">
						<a-button type="text" size="mini" v-if="record.status!==1">处理</a-button>
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
import KdAlarmRecordSearch from "./components/KdAlarmRecordSearch.vue"
import { getDataAlarmRecord,dealDataAlarmStatus ,deleteDataAlarm } from '@/api/kdStatistics'
import { Message } from "@arco-design/web-vue";
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
	getList(1,state.info.limit)
}
function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit 
	let param = {
		page:state.info.page,
		limit:state.info.limit,
	}
	let search = state.search
	if( search.device_id ) param.device_id = search.device_id
	if( search.variable_id ) param.variable_id = search.variable_id
	if( [0,1].includes(search.is_warning)) param.is_warning = search.is_warning
	if( [0,1].includes(search.status)) param.status = search.status
	if( search.time && search.time.length){
		param.start_time = search.time[0]
		param.end_time = search.time[1]
	}
	
	state.loading = true
	getDataAlarmRecord(param).then(res=>{
		state.loading = false
		state.info.list = res.data.list || []
		state.info.count = res.data.count || 0
	}).catch(()=>{
		state.loading = false
	})
}

//处理报警
function dealData(id){
	dealDataAlarmStatus({id}).then(res=>{
		if( res.code ===200 ){
			Message.success('处理成功')
			getList()
			return
		}
		Message.error(res.msg)
	})
}

//删除记录
function delData(id){
	deleteDataAlarm({id}).then(res=>{
		if( res.code ===200 ){
			Message.success('删除成功')
			getList()
			return
		}
		Message.error(res.msg)
	})
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