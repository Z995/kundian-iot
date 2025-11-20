<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/staff/list.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-19 17:42:13  -->
<template>
<page-box>
	<div class="kd-content">
		<a-card :bordered="false">
		    <template #title>
		        <div class="flex-c">员工列表</div>
		    </template>
		    <template #extra>
		        <a-button type="primary" @click="showEdit(null)">
		            <i class="ri-add-line"></i>添加员工
		        </a-button>
		    </template>
		</a-card>
		<a-table :pagination="false" row-key="id" :bordered="false" :data="state.info.list" :columns="[
		    {title:'ID',dataIndex:'id',width:80},
		    {title:'头像',dataIndex:'name'},
		    {title:'员工姓名',dataIndex:'name'},
		    {title:'联系电话',dataIndex:'phone'},
		    {title:'职务',dataIndex:'appointment',width:140},
		    {title:'年龄',dataIndex:'age'},
		    {title:'性别',dataIndex:'sex'},
		    {title:'备注',dataIndex:'remark'},
		    {title:'入职时间',dataIndex:'entry_time'},
		    {title:'状态',slotName:'status'},
		    {title:'操作',slotName:'action',width:180},
		]" :loading="state.loading">
		    <template #status="{record}">
		        <a-tag color="#00CC66" v-if="record.status==1">在职</a-tag>
		        <a-tag color="#ccc" v-else>离职</a-tag>
		    </template>
		    <template #action="{record}">
		        <a-button type="text" size="mini" class="ml-10" @click="showEdit(record)">编辑</a-button>
		        <a-popconfirm content="确认删除该员工吗？" @ok="delData(record.id)">
		            <a-button type="text" size="mini">删除</a-button>
		        </a-popconfirm>
		    </template>
		</a-table>
		<kd-pager :pageData="state.info" :event="getList"></kd-pager>
	</div>
	<cqkd-add-staff ref="staffAddRef"></cqkd-add-staff>
</page-box>
</template>

<script setup>
import {onMounted, reactive, ref} from "vue"
import CqkdAddStaff from "./components/CqkdAddStaff.vue"
const staffAddRef = ref()
const state = reactive({
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
	state.info.list = [
		{id:1,name:'张三',phone:"13452212345",appointment:'操作工人',age:'32',sex:'男',remar:'-',entry_time:'2025-10-10',status:1},
		{id:2,name:'李四',phone:"13452212345",appointment:'操作工人',age:'32',sex:'男',remar:'-',entry_time:'2025-10-10',status:1},
		{id:3,name:'王五',phone:"13452212345",appointment:'操作工人',age:'32',sex:'男',remar:'-',entry_time:'2025-10-10',status:2},
	]
}

function showEdit(data){
	staffAddRef.value.show(data)
}


</script>

<style lang="scss" scoped>

</style>