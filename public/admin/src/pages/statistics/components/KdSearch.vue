<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/components/KdSearch.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-04 14:11:55  -->
<template>
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
	<a-select placeholder="请选择变量" class="w340" multiple :max-tag-count="3" allow-clear
		v-model="state.search.data_id"
	>
		<a-option v-for="(item,index) in state.dataList" :key="index" :value="item.id">
			{{ item.name }}
		</a-option>
	</a-select>
	<a-range-picker
		class="w340"
		show-time
		format="YYYY-MM-DD HH:mm"
		shortcuts-position="left"
		:shortcuts="state.rangeShortcuts"
		v-model="state.search.time"
	/>
	<!-- 时间范围默认 [两小时前 ,当前时间]-->
	<a-button type="primary" @click="searchData">查询</a-button>
	<a-button @click="resetData">重置</a-button>
</a-space>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import dayjs from 'dayjs'
import { getDeviceList,getDeviceDetail,getDeviceVariableList } from '@/api/kdDevice'
import { Message } from '@arco-design/web-vue';
import { useRoute } from 'vue-router';
const emits = defineEmits(['search','changeDevice','changeData'])
const options = useRoute().query
const state = reactive({
	info:{				//设备数据
		page:1,
		limit:30,
		list:[],
	},
	slaveList:[],		//从机数据
	dataList:[],		//变量数据
	
	search:{
		name:"",		//设备名称搜索
		device_id:null,	//设备id
		slave_id:null,	//从机id
		data_id:[],		//变量id
		time:[],		//时间区间
	},
	rangeShortcuts:[
		{label: '最近一日',value: () => [dayjs().subtract(1, 'day'),dayjs()]},
		{label: '最近三日',value: () => [dayjs().subtract(3, 'day'),dayjs()]},
		{label: '最近七日',value: () => [dayjs().subtract(7, 'day'),dayjs()]},
		{label: '最近一个月',value: () => [dayjs().subtract(1, 'month'),dayjs()]},
		{label: '最近三个月',value: () => [dayjs().subtract(3, 'month'),dayjs()]},
		{label: '最近一年',value: () => [dayjs().subtract(1, 'year'),dayjs()]},
	]
})
onMounted(async ()=>{
	//默认显示一小时
	state.search.time = [
		formatDate(dayjs().subtract(1, 'hour')),
		formatDate(dayjs())
	]
	if( options.device_id ) {
		state.search.device_id = parseInt(options.device_id)
	}
	if( options.slave_id ) {
		state.search.slave_id = parseInt(options.slave_id)
		let res = await getDeviceVariableList({subordinate_id:options.slave_id})
		state.dataList = res.data?.list || []
	}
	if( options.id ) {
		state.search.data_id = [parseInt(options.id)]
		searchData()
	}
	
	getDevice(1,state.info.limit)
})

function formatDate(date){
	return dayjs(date).format("YYYY-MM-DD HH:mm")
}

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
			
			// 首次获取数据，存在带参数的情况下
			if( page === 1 && options.device_id ){
				let index = state.info.list.findIndex(item=>item.id === parseInt(options.device_id))
				if( index !==-1 ){
					getDeviceDetail({id:options.device_id}).then(res=>{
						state.info.list.push(res.data)
						state.slaveList = res.data?.subordinate || []
						emits("changeDevice",res.data)
					})
				}
			}
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
	state.search.data_id = []
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
	state.search.data_id = []
	state.dataList = []
	let { slave_id } = state.search
	if( !slave_id ) return false;
	getDeviceVariableList({subordinate_id:slave_id}).then(res=>{
		state.dataList = res.data.list || []
	})
}

//查询
function searchData(){
	let { data_id } = state.search
	if( !data_id.length ) {
		Message.warning('请选择变量')
		return;
	}
	let data = []
	for (var i = 0; i < state.dataList.length; i++) {
		if( data_id.includes(state.dataList[i].id )){
			data.push(state.dataList[i])
		}
	}
	emits('changeData',data)
	emits('search',JSON.parse(JSON.stringify(state.search)))
}
//重置搜索
function resetData(){
	state.search = {
		name:"",		//设备名称搜索
		device_id:null,	//设备id
		slave_id:null,	//从机id
		data_id:[],		//变量id
		time:[			//时间区间
			dayjs().subtract(1, 'hour'),
			dayjs()
		],		
	}
}

</script>

<style>
</style>