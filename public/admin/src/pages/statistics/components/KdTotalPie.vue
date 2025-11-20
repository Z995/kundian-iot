<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/components/KdTotalPie.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 14:43:08  -->
<template>
<div class="total-pie" :id="props.canvasId">
	
</div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import * as echarts from "echarts";
import { getTotalByDeviceStatus,getTotalByDeviceLabel,getTotalByGatewayOnlineStatus } from '@/api/kdStatistics'
const props = defineProps({
	canvasId:{
		type:String,
		default:"dataPie"
	},
	
	//统计类型 deviceStatus:设备状态   deviceLabel:设备标签   gatewayStatus:网关状态
	type:{
		type:String,
		default:'deviceStatus',
	}
})
let chartObj = null

onMounted(()=>{
	if( props.type ==='deviceStatus'){
		getTotalByDeviceStatus().then(res=>{
			let data = [
				{name:'在线',value:res.data?.online || 0},
				{name:'变量报警',value:res.data?.warning || 0},
				{name:'离线',value:res.data?.not_online || 0},
			]
			drawData(data)
		})
	}
	if( props.type ==='deviceLabel'){
		getTotalByDeviceLabel().then(res=>{
			let list = res.data || []
			let data = []
			list.forEach(item=>{
				data.push({
					name:item.name,
					value:item.count
				})
			})
			data.sort((a,b)=>b.value - a.value )
			//只保留最多数据的前七位
			let data1 = data.slice(0,7)
			drawData(data1)
		})
	}
	if( props.type ==='gatewayStatus'){
		getTotalByGatewayOnlineStatus().then(res=>{
			let data = [
				{name:'在线',value:res.data?.online || 0},
				{name:'报警',value:res.data?.warning || 0},
				{name:'离线',value:res.data?.not_online || 0},
			]
			drawData(data)
		})
	}
})

/**
 * @param {Object} dataList [{name:'在线',value:10}]
 */
function drawData(dataList){
	var chartDom = document.getElementById(props.canvasId);
	chartObj = echarts.init(chartDom);
	let option = {
		color:["#3c78ff","#ff9659","#c0cff3","#00CC66","#00CCFF","#6666FF","#CC33CC","#FF66CC","#009966"],
		tooltip: {
			trigger: 'item'
		},
		legend: {
			bottom:"0",
			left: 'center',
		},
		series: [
			{
				type: 'pie',
				radius: ['30%', '50%'],
				center: ['40%', '50%'],
				label: {
					show: true, // 是否显示
					formatter: function (params) {
						return `${params.name}(${params.value})`;
					},
		  
				},
				labelLine: {
					show: true, // 是否显示视觉引导线
				},
				itemStyle: {
					borderWidth: 5, // 描边线宽
					borderColor: '#fff' // 图形的描边颜色
				},
				data: dataList
			}
		]
	}
	option && chartObj.setOption(option);
}
</script>

<style lang="scss" scoped>
.total-pie{
	width: 100%;
	height: 240px;
}
</style>