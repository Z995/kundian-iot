<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/components/KdLineAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 15:22:25  -->
<template>
<div class="line-box">
	<div class="date flex mb10 fb">
		<div class="data-item" :class="{active:state.type ===1 }" @click="changeDay(1)">近七日</div>
		<div class="data-item" :class="{active:state.type ===2 }" @click="changeDay(2)">最近一个月</div>
	</div>
	<a-spin :loading="state.loading" style="width: 100%;height: 300px;" tip="数据加载中...">
		<div class="line-add" v-if="state.list.length" :id="props.canvasId"></div>
	</a-spin>
	<!-- 无数据时 -->
	<kd-empty v-if="!state.list.length" :height="300"></kd-empty>
</div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import * as echarts from "echarts";
import { getLineByDeviceNew,getLineByGatewayNew } from '@/api/kdStatistics'
import { dayjs } from '@arco-design/web-vue/es/_utils/date';
let chartObj = null
const props = defineProps({
	canvasId:{
		type:String,
		default:"dataLine"
	},
	type:{
		type:String,
		default:'deviceNew'	,	//deviceNew:设备新增数据   gatewayNew:网关新增数据
	}
})

const state = reactive({
	loading:false,
	type:1,		//1七天数据 2 三十天数据
	list:[],
})

onMounted(()=>{
	if( props.type ==='deviceNew') {
		getDevice()
	}
	if( props.type ==='gatewayNew') {
		getGateway()
	}
})

//获取设备数据
function getDevice(){
	let param = {type:state.type }
	state.loading = true
	getLineByDeviceNew(param).then(res=>{
		state.loading = false
		state.list = res.data
		setTimeout(function(){
			drawData(res.data)
		},500)
	}).catch(()=>{
		state.loading = false
	})
}

//获取网关
function getGateway(){
	let param = {type:state.type }
	state.loading = true
	getLineByGatewayNew(param).then(res=>{
		state.loading = false
		state.list = res.data
		setTimeout(function(){
			drawData(res.data)
		},500)
	}).catch(()=>{
		state.loading = false
	})
}

//切换时间
function changeDay(day){
	state.type = day
	if( props.type ==='deviceNew') {
		getDevice()
	}
	if( props.type ==='gatewayNew') {
		getGateway()
	}
}

function drawData(dataList){
	if( !dataList.length ) return false;
	if( !chartObj ) {
		var chartDom = document.getElementById(props.canvasId);
		chartObj = echarts.init(chartDom);
	}
	let time = [] , value = []
	dataList.forEach(item=>{
		time.push(dayjs(item.date).format("MM/DD"))
		value.push(parseInt(item.count))
	})
	
	let option = {
		color:['#3c78ff'],
		grid: {
		    left: "5%",
		    right: "5%",
		    bottom: "5%",
			top:"5%",
		    containLabel: true,
		},
		tooltip: {
		  trigger: 'axis'
		},
		xAxis: {
		    type: 'category',
		    data:time,
		},
		yAxis: {
		    type: 'value',
			splitLine:{
				lineStyle:{
					type:'dashed',
				}
			},
		},
		series: [
		    {
				name:'新增设备',
				data: value,
				type: 'bar',
				barWidth:20,
				itemStyle: {
					borderRadius: [4, 4, 0, 0],
				},
		    }
		]
	}
	option && chartObj.setOption(option);
}

defineExpose({
	drawData
})
</script>

<style lang="scss" scoped>
.line-box{
	width: 100%;
	.line-add{
		width: 100%;
		height: 300px;
	}
}
.date{
	width: 100%;
	.data-item{
		width: 100px;
		height: 36px;
		border: 1px solid #efefef;
		text-align: center;
		line-height: 36px;
		cursor: pointer;
		&:first-child{
			border-right: none;
		}
	}
	.active{
		background: #0066FF;
		color: #fff;
		border: 1px solid #0066FF;
	}
}
</style>