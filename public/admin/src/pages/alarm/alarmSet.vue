<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/alarm/alarmSet.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 09:24:53  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="报警配置" :bordered="false">
			<template #extra>
				<a-space>
					<a-button type="primary" @click="toAdd(null)">
						<i class="ri-add-line"></i>添加配置
					</a-button>
					<a-button :disabled="!state.selectId.length">批量删除</a-button>
				</a-space>
			</template>
			
			<!-- 搜索参数 -->
			<a-space>
				<a-input placeholder="请输入报警配置名称" class="w300"></a-input>
				<a-button type="primary" :loading="state.loading" @click="getList(1,state.info.limit)">查询</a-button>
				<a-button @click="resetData">重置</a-button>
			</a-space>
			
			<a-table class="kd-small-table mt20" :pagination="false" row-key="id"
				:data="state.info.list"
				:loading="state.loading"
				:bordered="false"
				:row-selection="{
					type: 'checkbox',
					showCheckedAll: true,
					width:50
				}"
				v-model:selectedKeys="state.selectId"
				:columns="[
					{title:'ID',dataIndex:'id',width:80},
					{title:'报警配置名称',dataIndex:'name'},
					{title:'推送类型',dataIndex:'type'},
					{title:'推送主体',dataIndex:'main'},
					{title:'推送方式',dataIndex:'method'},
					{title:'推送机制',dataIndex:'jizhi'},
					{title:'状态',slotName:'status'},
					{title:'更新时间',dataIndex:'update_time'},
					{title:'操作',slotName:'action',width:130,fixed:'right'},
				]"
				:scroll="{x:1200}"
			>
				<template #status="{record}">
					<a-switch v-model="record.status" :checked-value="1" :unchecked-value="0"></a-switch>
				</template>
				<template #action="{record}">
					<a-button type="text" size="mini" class="ml-10" @click="toAdd(record)">编辑</a-button>
					<a-popconfirm content="确认删除报警配置吗?">
						<a-button type="text" size="mini">删除</a-button>
					</a-popconfirm>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
	<kd-alarm-set-add ref="addRef"></kd-alarm-set-add>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import KdAlarmSetAdd from './components/KdAlarmSetAdd.vue';
const addRef = ref()
const state = reactive({
	search:{},
	loading:false,
	info:{
		list:[],
		page:1,
		limit:10,
		count:0,
	},
	selectId:[],
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
	
	// 模拟数据
	state.info.list =[
		{id:1,name:'farmtest报警配置',type:'独立触发器',main:'地下20cm水分低位预警',method:'短信、邮件',jizhi:'仅一次推送',status:1,update_time:'2025-04-17 11:34:31'},
		{id:2,name:'farmtest报警配置',type:'独立触发器',main:'-',method:'短信、邮件',jizhi:'仅一次推送',status:0,update_time:'2025-04-17 11:34:31'},
		{id:3,name:'farmtest报警配置',type:'独立触发器',main:'地下20cm水分低位预警',method:'短信、邮件',jizhi:'报警沉默时间10分钟',status:1,update_time:'2025-04-17 11:34:31'},
		{id:4,name:'farmtest报警配置',type:'独立触发器',main:'地下20cm水分低位预警',method:'短信、邮件',jizhi:'仅一次推送',status:0,update_time:'2025-04-17 11:34:31'},
	]
}

//新增，编辑
function toAdd(data){
	addRef.value.show(data)
}
</script>

<style lang="scss" scoped>
</style>