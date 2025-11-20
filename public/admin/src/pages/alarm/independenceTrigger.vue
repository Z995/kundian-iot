<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/src/pages/alarm/independenceTrigger.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 09:26:11  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="模板触发器" :bordered="false">
			<template #extra>
				<a-space>
					<a-button type="primary" @click="toAdd(null)">
						<i class="ri-add-line"></i>添加触发器
					</a-button>
					<!-- <a-button :disabled="!state.selectId.length">批量删除</a-button> -->
				</a-space>
			</template>
			<!-- 搜索参数 -->
			<a-space>
				<a-input v-model="state.search.name" placeholder="请输入触发器名称" class="w200"></a-input>
				<a-input v-model="state.search.device_name" placeholder="请输入设备名称" class="w200"></a-input>
				<a-button type="primary" :loading="state.loading" @click="getList(1,state.info.limit)">查询</a-button>
				<a-button @click="resetData">重置</a-button>
			</a-space>
			<a-table class="kd-small-table mt20" :pagination="false" row-key="id"
				:data="state.info.list"
				:loading="state.loading"
				:bordered="false"
				:columns="[
					{title:'ID',dataIndex:'id',width:80},
					{title:'触发器名称',dataIndex:'name'},
					{title:'触发器类型',slotName:'trigger_type'},
					{title:'触发主体',slotName:'device'},
					{title:'报警变量',slotName:'alarm_data'},
					{title:'触发条件',slotName:'condition'},
					{title:'联动',slotName:'link'},
					{title:'状态',slotName:'status'},
					{title:'更新时间',dataIndex:'update_time'},
					{title:'操作',slotName:'action',width:180,fixed:'right'},
				]"
				:scroll="{x:1200}"
			>
				<template #trigger_type="{record}">
					<span v-if="record.trigger_type ===1">设备</span>
				</template>
				<template #device="{record}">
					<span>
						{{ record.device?.name}}-
						{{ record.subordinate?.subordinate?.name}}
					</span>
				</template>
				<template #alarm_data="{record}">
					<span>{{ record.subordinateVariable?.name}}</span>
				</template>
				<template #condition="{record}">
					<span v-if="record.condition===0">开关OFF</span>
					<span v-if="record.condition===1">开关ON</span>
					<span v-if="record.condition===2">数值小于{{record?.condition_parameter.A}}</span>
					<span v-if="record.condition===3">数值大于{{record?.condition_parameter.B}}</span>
					<span v-if="record.condition===4">数值大于{{record?.condition_parameter.A}}且小于{{record?.condition_parameter.B}}</span>
					<span v-if="record.condition===5">数值小于{{record?.condition_parameter.A}}或大于{{record?.condition_parameter.B}}</span>
					<span v-if="record.condition===6">数值等于{{record?.condition_parameter.A}}</span>
				</template>
				<template #link="{record}">
					<div v-if="record.is_linkage ===1">
						<span>{{record.linkage_type ===1 ?'采集：':'控制：'}}</span>
						<span>{{record.linkageSubordinate?.subordinate?.name}}-</span>
						<span>{{record.linkageSubordinateVariableId?.name}}</span>
					</div>
					<div v-else>-</div>
				</template>
				<template #status="{record}">
					<a-switch v-model="record.status" :checked-value="1" :unchecked-value="0"
					></a-switch>
				</template>
				<template #action="{record}">
					<a-button type="text" size="mini" class="ml-10" @click="toDetail(record.id)">查看</a-button>
					<a-button type="text" size="mini" class="ml-10" @click="toAdd(record.id)">编辑</a-button>
					<a-popconfirm content="确认触发器吗?" @ok="delData(record.id)">
						<a-button type="text" size="mini">删除</a-button>
					</a-popconfirm>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
	<kd-independ-trigger-add ref="addRef" @success="getList"></kd-independ-trigger-add>
	<kd-independ-trigger-detail ref="detailRef"></kd-independ-trigger-detail>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import KdIndependTriggerAdd from './components/KdIndependTriggerAdd.vue';
import KdIndependTriggerDetail from './components/KdIndependTriggerDetail.vue';
import { getIndependentTriggerList,deleteIndependentTriggerData,updateIndependentTriggerStatus } from "@/api/kdTrigger"
import { Message } from '@arco-design/web-vue';
const addRef = ref()
const detailRef = ref()
const state = reactive({
	search:{
		name:"",
		device_name:""
	},
	loading:false,
	info:{
		list:[],
		page:1,
		limit:10,
		count:0,
	},
})
onMounted(()=>{
	getList(1,10)
})

function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	let param = { 
		page:state.info.page,
		limit:state.info.limit,
	}
	let { name,device_name } = state.search
	if( name ) param.name = name
	if( device_name ) param.device_name = device_name
	state.loading = true
	getIndependentTriggerList(param).then(res=>{
		state.loading = false
		state.info.list = res.data.list
		state.info.count = res.data?.count || 0
	}).catch(()=>{
		state.loading = false
	})
}
//新增，编辑
function toAdd(data){
	addRef.value.show(data)
}
//查看详情
function toDetail(data){
	detailRef.value.show(data)
}

//删除独立触发器数据
function delData(id){
	deleteIndependentTriggerData({id}).then(res=>{
		if( res.code === 200 ){
			Message.success('删除成功')
			getList()
			return;
		}
		Message.error(res.msg)
	})
}

//修改触发器状态
// function changeStatus(data){
// 	let param = {
// 		id:data.id,
// 		value:data.status ===1 ?0 :1,
		
// 	}
// 	updateIndependentTriggerStatus(param).then(res=>{
// 		if( res.code === 200 ){
// 			Message.success('操作成功')
// 			getList()
// 			return;
// 		}
// 		Message.error(res.msg)
// 	})
// }
//重置搜索
function resetData(){
	state.search = {
		name:"",
		device_name:"",
	}
	getList(1,state.info.limit)
}
</script>

<style lang="scss" scoped>
</style>