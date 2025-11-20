<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/alarm/contacts.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 09:21:44  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="报警联系人" :bordered="false">
			<template #extra>
				<a-space>
					<a-button type="primary" @click="toAdd(null)">
						<i class="ri-add-line"></i>添加联系人
					</a-button>
					<a-button :disabled="!state.selectId.length">批量删除</a-button>
				</a-space>
			</template>
			
			<!-- 搜索参数 -->
			<a-space>
				<a-input placeholder="联系人姓名，手机号或邮箱" class="w300"></a-input>
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
					{title:'联系人姓名',dataIndex:'name'},
					{title:'手机',dataIndex:'phone'},
					{title:'邮箱',dataIndex:'email'},
					{title:'企业微信',dataIndex:'qi_wechat'},
					{title:'备注',dataIndex:'remark'},
					{title:'更新时间',dataIndex:'update_time'},
					{title:'操作',slotName:'action',width:130,fixed:'right'},
				]"
				:scroll="{x:1200}"
			>
				<template #action="{record}">
					<a-button type="text" size="mini" class="ml-10" @click="toAdd(record)">编辑</a-button>
					<a-popconfirm content="确认删除联系人吗?">
						<a-button type="text" size="mini">删除</a-button>
					</a-popconfirm>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
	<kd-contact-add ref="addRef" @success="getList"></kd-contact-add>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import KdContactAdd from './components/KdContactAdd.vue';
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
		{id:1,name:'张三',phone:'13452212345',email:'zhangsan@qq.com',qi_wechat:'-',remark:'测试',update_time:'2025-04-17 11:34:31'},
		{id:2,name:'李四',phone:'13452212345',email:'zhangsan@qq.com',qi_wechat:'-',remark:'测试',update_time:'2025-04-17 11:34:31'},
		{id:3,name:'王五',phone:'13452212345',email:'zhangsan@qq.com',qi_wechat:'-',remark:'测试',update_time:'2025-04-17 11:34:31'},
		{id:4,name:'赵六',phone:'13452212345',email:'zhangsan@qq.com',qi_wechat:'-',remark:'测试',update_time:'2025-04-17 11:34:31'},
		{id:5,name:'张三1',phone:'13452212345',email:'zhangsan@qq.com',qi_wechat:'-',remark:'测试',update_time:'2025-04-17 11:34:31'},
	]
}

//添加，编辑
function toAdd(data){
	addRef.value.show(data)
}
//重置搜索
function resetData(){
	state.search = {}
	getList(1,state.info.limit)
}
</script>

<style lang="scss" scoped>
</style>