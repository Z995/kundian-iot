<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/components/mould/KdAddMouldData.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 09:43:42  -->
<template>
<div class="add-mould-box">
	<!-- <a-space class="mb20">
		<a-button type="primary" class="w120" @click="showAdd(null)">添加变量</a-button>
		<a-button type="primary" class="w120">导入变量</a-button>
		<a-button type="primary" class="w120" :disabled="!state.selectId.length">导出变量</a-button>
		<a-popconfirm content="确认删除选择的变量数据吗?">
			<a-button type="primary" class="w120" :disabled="!state.selectId.length">批量删除</a-button>
		</a-popconfirm>
	</a-space> -->
	<!-- <a-button type="primary" class="w120" :disabled="!state.selectId.length" @click="delData">批量删除</a-button> -->
	<a-table class="kd-small-table" :pagination="false" row-key="unique_key"
		:bordered="false" :data="props.list" :columns="[
		{title:'ID',dataIndex:'id'},
		{title:'变量名称',dataIndex:'name'},
		{title:'变量类型',slotName:'type'},
		{title:'数值类型',slotName:'data_format'},
		{title:'寄存器',slotName:'register'},
		{title:'读写',slotName:'read_write_mode'},
		{title:'存储方式',slotName:'storage_mode'},
		{title:'排序',dataIndex:'sort'},
		{title:'操作',slotName:'action'},
	]" 
		:loading="state.loading"
		:row-selection="{
			type: 'checkbox',
			showCheckedAll: true,
			width:50
		}"
		v-model:selectedKeys="state.selectId"
		@select="deleteBatch"
		@select-all="deleteBatch"
	>
		<template #type="{record}">
			<span>{{constantData.dataType[record.type]}}</span>
		</template>
		<template #data_format="{record}">
			<span>{{constantData.dataFormat[record.data_format]}}</span>
		</template>
		<template #register="{record}">
			<span>{{record.register_mark}}{{record.register_address}}</span>
		</template>
		<template #read_write_mode="{record}">
			<span>{{record.read_write_mode === 1?'只读':'读写'}}</span>
		</template>
		<template #storage_mode="{record}">
			<span>{{record.storage_mode === 1?'变化存储':'全部存储'}}</span>
		</template>
		<template #action="{record,rowIndex}">
			<a-button type="text" size="mini" class="ml-12" @click="showAdd(rowIndex)">编辑</a-button>
			<a-popconfirm content="确认删除变量数据吗?" @ok="delData(rowIndex)">
				<a-button type="text" size="mini">删除</a-button>
			</a-popconfirm>
		</template>
	</a-table>
</div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import constantData from '../../../../util/common/constantData';
const emits = defineEmits(['edit','delete','deleteBatch'])
const props = defineProps({
	index:{
		type:Number,
		default:0,
	},
	list:{
		type:Array,
		default:[],
	}
})

const state = reactive({
	loading:false,

	selectId:[],
})

const showAdd = (data)=>emits("edit",data)
const delData = (data)=>emits("delete",data)
const deleteBatch = (data)=>emits("deleteBatch",data)
// function delData(){
// 	console.log(state.selectId);
// }

</script>

<style>
</style>