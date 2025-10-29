<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/alarm/mouldTrigger.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 09:27:35  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="模板触发器" :bordered="false">
			<template #extra>
				<a-space>
					<a-button type="primary" @click="toAdd(null)">
						<i class="ri-add-line"></i>添加触发器
					</a-button>
				</a-space>
			</template>
			<!-- 搜索参数 -->
			<a-space>
				<a-input v-model="state.search.name" placeholder="触发器名称" class="w200"></a-input>
				<a-input v-model="state.search.template_name" placeholder="模板名称" class="w200"></a-input>
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
					{title:'模板名称',slotName:'mould_name'},
					{title:'更新时间',dataIndex:'update_time'},
					{title:'操作',slotName:'action',width:180,fixed:'right'},
				]"
				:scroll="{x:1200}"
			>
				<template #mould_name="{record}">
					<span>{{ record.template?.name || '-'}}</span>
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
	<kd-mould-trigger-add ref="addRef" @success="getList"></kd-mould-trigger-add>
	<kd-mould-trigger-detail ref="detailRef"></kd-mould-trigger-detail>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import KdMouldTriggerAdd from './components/KdMouldTriggerAdd.vue';
import KdMouldTriggerDetail from './components/KdMouldTriggerDetail.vue';
import { getMouldTriggerList,deleteMouldTriggerData } from "@/api/kdTrigger"
import { Message } from '@arco-design/web-vue';
const addRef = ref()
const detailRef = ref()
const state = reactive({
	search:{
		name:"",
		template_name:"",
	},
	loading:false,
	info:{
		list:[],
		page:1,
		limit:10,
		count:0,
	}
})
onMounted(()=>{
	getList(1,10)
})

function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	let { name,template_name } = state.search
	let param = { 
		page:state.info.page,
		limit:state.info.limit,
	}
	if( name ) param.name = name
	if( template_name ) param.template_name = template_name
	state.loading = true
	getMouldTriggerList(param).then(res=>{
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
//删除模板触发器数据
function delData(id){
	deleteMouldTriggerData({id}).then(res=>{
		if( res.code === 200 ){
			Message.success('删除成功')
			getList()
			return;
		}
		Message.error(res.msg)
	})
}
//重置搜索
function resetData(){
	state.search = {
		name:"",
		template_name:"",
	}
	getList(1,state.info.limit)
}
</script>

<style lang="scss" scoped>
</style>