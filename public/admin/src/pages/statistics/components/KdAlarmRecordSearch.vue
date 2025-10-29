<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/components/KdAlarmRecordSearch.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 17:08:38  -->
<template>
<div class="search-box">
	<a-space>
		<a-select class="w160" placeholder="请选择设备" v-model="state.search.device_id"
				:allow-search="true"
				allow-clear
				@search="searchDevice" :filter-option="false"
				@clear="clearDevice"
				@dropdown-reach-bottom="getDevice(state.info.page+1,state.info.limit)"
				@change="getSubirdinate"
		>
		  <a-option v-for="item of state.info.list" :value="item.id">{{item.name}}</a-option>
		</a-select>
		<a-select placeholder="请选择从机" class="w160" allow-clear
			v-model="state.search.slave_id"
			@change="getValue"
			@clear="getValue"
		>
			<a-option v-for="(item,index) in state.slaveList" :key="index" :value="item.id">
				{{ item.subordinate.name }}
			</a-option>
		</a-select>
		<a-select placeholder="请选择变量" class="w160" allow-clear
			v-model="state.search.variable_id"
		>
			<a-option v-for="(item,index) in state.dataList" :key="index" :value="item.id">
				{{ item.name }}
			</a-option>
		</a-select>
		<!-- 变量报警记录独有搜索参数 -->
		<div v-if="props.type ==='dataAlarmRerocrd'">
			<a-select placeholder="报警状态" class="w120" allow-clear v-model="state.search.is_warning">
				<a-option :value="0">正常</a-option>
				<a-option :value="1">报警</a-option>
			</a-select>
			<a-select placeholder="处理状态" class="w120" allow-clear v-model="state.search.status">
				<a-option :value="0">未处理</a-option>
				<a-option :value="1">已处理</a-option>
			</a-select>
		</div>
		<!-- 时间范围默认 [两小时前 ,当前时间]-->
		<a-range-picker
			class="w340"
			show-time
			format="YYYY-MM-DD HH:mm"
			shortcuts-position="left"
			:shortcuts="state.rangeShortcuts"
			v-model="state.search.time"
		/>
		<a-button type="primary" @click="searchData">查询</a-button>
		<a-button @click="resetData">重置</a-button>
	</a-space>
</div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import dayjs from 'dayjs'
import { getDeviceList,getDeviceDetail,getDeviceVariableList } from '@/api/kdDevice'
const emits = defineEmits(['search'])
const props = defineProps({
	//页面 dataAlarmRerocrd:变量报警记录 linkRecord:联动记录
	type:{
		type:String,
		default:"dataAlarmRerocrd"
	}
})
const state = reactive({
	search:{
		name:"",			//设备名称搜索
		device_id:null,		//设备id
		slave_id:null,		//从机id
		variable_id:null,	//变量id
		time:[],			//时间区间
		is_warning:null,	//预警状态 0正常 1预警
		status:null,		//处理状态 0未处理 1已处理
	},
	info:{					//设备数据
		page:1,
		limit:30,
		list:[],
	},
	slaveList:[],		//从机数据
	dataList:[],		//变量数据
})

onMounted(async()=>{
	getDevice(1,state.info.limit)
})

//获取设备列表数据
function getDevice(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	let param = {
		page:state.info.page,
		limit:state.info.limit,
	}
	let search = state.search
	if( search ){
		if( search.name ) param.name = search.name
	}
	getDeviceList(param).then(res=>{
		if( res.code === 200 ){
			state.info.list = page === 1 ? (res.data?.list || []) : state.info.list.concat(res.data?.list || [])
		}
	})
}
//设备搜索
function searchDevice(e){
	state.search.key = e
	getDevice(1,30)
}
//设备清空
function clearDevice(){
	state.search.name = ''
	getDevice(1,state.info.limit)
}
//获取从机数据
function getSubirdinate(){
	let { device_id } = state.search
	state.search.slave_id = null
	state.search.data_id = null
	state.slaveList = []
	state.dataList = []
	if( !device_id ) return false;
	getDeviceDetail({id:device_id}).then(res=>{
		state.slaveList = res.data?.subordinate || []
		emits("changeDevice",res.data)
	})
}

//获取从机变量数据
function getValue(){
	state.search.data_id = null
	state.dataList = []
	let { slave_id } = state.search
	if( !slave_id ) return false;
	getDeviceVariableList({subordinate_id:slave_id}).then(res=>{
		state.dataList = res.data.list || []
	})
}

function searchData(){
	emits('search',JSON.parse(JSON.stringify(state.search)))
}

function resetData(){
	state.search = {
		name:"",			//设备名称搜索
		device_id:null,		//设备id
		slave_id:null,		//从机id
		variable_id:null,	//变量id
		time:[],			//时间区间
		is_warning:null,	//预警状态 0正常 1预警
		status:null,		//处理状态 0未处理 1已处理
	}
	searchData()
}
</script>

<style>
</style>