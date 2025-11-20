<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/material/components/CqkdWarehouseMaterialList.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-19 11:04:14  -->
<template>
<div>
	<a-modal title="仓库物料查看" v-model:visible="state.show" width="1000px" :footer="null">
	    <a-form :model="state.search" layout="inline">
	    	<a-form-item label="物料类别">
	    		<a-select v-model="state.search.type" placeholder="请选择" allow-clear class="w120">
	    		    <a-option value="0">肥料</a-option>
	    		    <a-option value="1">农药</a-option>
	    		    <a-option value="2">种子</a-option>
	    		    <a-option value="3">兽药</a-option>
	    		    <a-option value="4">饲料</a-option>
	    		    <a-option value="5">其他</a-option>
	    		</a-select>
	    	</a-form-item>
	    	<a-form-item label="所在仓库">
	    		<a-select v-model="state.search.house" placeholder="请选择" allow-clear class="w200">
	    		    <a-option value="0">仓库A</a-option>
	    		    <a-option value="1">仓库B</a-option>
	    		</a-select>
	    	</a-form-item>
	    	<a-form-item label="物料名称">
	    		<a-input v-model="state.search.name" placeholder="物料名称" class="w200"></a-input>
	    		<a-button type="primary" class="ml10">搜索</a-button>
	    		<a-button class="ml10">重置</a-button>
	    	</a-form-item>
	    </a-form>
		<a-table class="kd-small-table" :pagination="false" row-key="id" :bordered="false" :data="state.info.list" :columns="[
		    {title:'ID',dataIndex:'id',width:100},
		    {title:'图片',slotName:'cover',width:100},
		    {title:'名称',dataIndex:'name'},
		    {title:'类别',dataIndex:'type'},
		    {title:'规格',slotName:'attr'},
		    {title:'创建时间',dataIndex:'create_time'},
		]" :loading="state.loading">
		    <template #cover="{record}">
		        <a-image class="ml-10" :rec="record.img" width="36"></a-image>
		    </template>
		    <template #data="{record}">
		        <div>{{ record.name }}</div>
		    </template>
		    <template #attr="{record}">
		        <div>{{ record.attr || '-' }}</div>
		    </template>
		</a-table>
	    <kd-pager :pageData="state.info" :event="getList"></kd-pager>
	</a-modal>
</div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';

const state = reactive({
	show:false,
	loading:false,
	info:{
		page:1,
		limit:10,
		count:0,
		list:[]
	},
	search:{
		type:null,
		house:null,
		name:'',
	}
})
onMounted(()=>{
})

function show(){
	state.show = true
	getList(1,10)
}

function getList(page,limit){
	state.info.list = [
		{id:1,name:'张三',cropName:'苹果',pickingCount:'100',expirationDate:'12',create_time:'2025/10/10'},
		{id:2,name:'张三',cropName:'苹果',pickingCount:'100',expirationDate:'34',create_time:'2025/10/10'},
		{id:3,name:'张三',cropName:'苹果',pickingCount:'100',expirationDate:'25',create_time:'2025/10/10'},
	]
}
defineExpose({
	show
})
</script>

<style>
</style>