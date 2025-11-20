<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/components/history/KdHistoryDataLine.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 16:27:37  -->
<template>
<div class="history-data-box">
	<div class="history-data-box" id="historyLine" v-if="state.list.length"></div>
	<kd-empty v-if="!state.list.length" :height="390"></kd-empty>
</div>

</template>

<script setup>
import { nextTick, onMounted, reactive } from 'vue';
import * as echarts from "echarts";
import dayjs from 'dayjs'
let chartObj = null
const state = reactive({
	list:[],
})
function drawData(list){
	state.list = list
	nextTick(()=>{
		var chartDom = document.getElementById("historyLine");
		if( !list.length ) return false;
		if( chartObj ){
			chartObj.dispose()
			chartObj = null
		}
		chartObj = echarts.init(chartDom);
		let categroy = [] , seriesData = []
		for (let i = 0; i < list.length; i++) {
			for (let j = 0; j < list[i].log.length; j++) {
				categroy.push(list[i].log[j].create_time)
			}
		}
		
		//去重
		const uniqueArr = Array.from(new Set(categroy));
		//排序
		uniqueArr.sort((a,b)=>new Date(a).getTime() - new Date(b).getTime() )
		for (let i = 0; i < list.length; i++) {
			let value = []
			for (let j = 0; j < uniqueArr.length; j++) {
				let temp = list[i].log.find( item => uniqueArr[j] === item.create_time )
				if( temp ){
					value.push( parseFloat(temp.val ) )
				}else{
					value.push('')
				}
			}	
			seriesData.push({
				name: list[i].variable?.name,
				data:value,
				connectNulls:true,	//连接断裂两端点
				type: 'line',
				smooth: true,
				symbolSize: 8,
				symbol: 'circle',
				lineStyle: {
					width: 3,
					// color: '#4080FF'
				},
				itemStyle: {
					// 设置数据点（圆点）的颜色
					// color: '#4080FF', // 可以是具体的颜色值，如 'blue'、'#123456' 等
					borderColor: '#fff', // 数据点边框颜色
					borderWidth: 2 // 数据点边框宽度
				}
			})
		}	
		
		let option= {
			color:["#3c78ff","#00CC66","#c0cff3","#00CCFF","#6666FF","#CC33CC","#FF66CC","#009966","#ff9659"],
			grid: {
				left: '2%',
				right: '4%',
				bottom: '13%',
				top: '15%',
				containLabel: true
			},
		   legend: {
				show: true,
				itemWidth: 12,
				itemHeight: 12,
				top: '1%',
			},
			dataZoom: [
				{
					show: true,
					type: 'slider',
					bottom: 0,
					y: '90%',
					start: uniqueArr.length < 100 ? 0 :60,
					height: 30, //组件高度
					end: 100
				},
			],
			tooltip: {
			  trigger: 'axis'
			},
			xAxis: {
				type: 'category',
				data: uniqueArr,
				axisTick: {
					show: false
				},
				axisLabel: {
					textStyle: {
						color: '#4E5969'
					},
					showMaxLabel: true, //强制显示最后一个数据的刻度
					formatter: function (params) {
						let date = new Date(params);
						let hour = date.getHours();
						let miu = date.getMinutes();
						let sec = date.getSeconds();
						return (
							(hour < 10 ? '0' + hour : hour) +
							':' +
							(miu < 10 ? '0' + miu : miu)
						);
					},
				},
				axisLine: {
					lineStyle: {
						color: '#EAEBF0'
					}
				}
			},
			yAxis: {
				type: 'value',
				axisLabel: {
					textStyle: {
						color: '#4E5969'
					},
				},
				splitLine:{
					lineStyle:{
						type:'dashed',
					}
				},
			},
			series:seriesData
		}
		option && chartObj.setOption(option);
	})
}

defineExpose({
	drawData
})
</script>

<style lang="scss" scoped>
.history-data-box{
	width: 100%;
	height: 390px;
}
</style>