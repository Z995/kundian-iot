<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/farming/farmingType.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-10 10:34:23  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card :bordered="false">
		    <template #title>
		        <div class="flex-c">
		            <span>农事类型</span>
		        </div>
		    </template>
			<template #extra>
				<a-button type="primary" @click="showEdit(null)"><i class="ri-add-line"></i>添加类型</a-button>
			</template>
		</a-card>
		
		<a-table :pagination="false" row-key="id" :bordered="false" :data="state.info.list" :columns="[
			{title:'ID',dataIndex:'id',width:120},
			{title:'类型名称',dataIndex:'name'},
			{title:'状态',slotName:'status'},
			{title:'排序',dataIndex:'rank'},
			{title:'创建时间',dataIndex:'create_time'},
			{title:'操作',slotName:'action',width:180},
		]" :loading="state.loading">
			<template #status="{record}">
				<a-tag color="#00CC66" v-if="record.status==1">启用</a-tag>
				<a-tag color="gray" v-else>禁用</a-tag>
			</template>
			<template #action="{record}">
				<a-button type="text" size="mini" class="ml-10">编辑</a-button>
				<a-popconfirm content="确认删除农事类型吗？" @ok="delData(record.id)">
					<a-button type="text" size="mini">删除</a-button>
				</a-popconfirm>
			</template>
		</a-table>
		<kd-pager :pageData="state.info" :event="getList"></kd-pager>
	</div>
	<cqkd-add-farming-type ref="addRef" @success="getList"></cqkd-add-farming-type>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import CqkdAddFarmingType from './components/CqkdAddFarmingType.vue'
const addRef = ref()
const state = reactive({
	loading:false,
	info:{
		page:1,
		limit:10,
		count:0,
		list:[],
	}
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
	state.info.list = [
		{id:1,name:'除草',status:1,create_time:'2025/10/10 12:00',rank:10},
		{id:2,name:'施肥',status:0,create_time:'2025/10/10 12:00',rank:10},
		{id:3,name:'浇水',status:1,create_time:'2025/10/10 12:00',rank:10},
	]
}
//新增编辑
function showEdit(data){
	addRef.value.show(data)
}

//删除数据
function delData(id){
	
}

</script>

<style>
</style>