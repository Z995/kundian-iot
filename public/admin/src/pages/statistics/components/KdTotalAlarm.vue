<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/components/KdTotalAlarm.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 15:10:34  -->
<template>
<div class="alarm-box">
	<div class="alarm-item flex">
		<div class="ai-title fb">设备报警</div>
		<div class="ai-today" v-for="(item,index) in state.list" :key="index">
			<div class="f12 ait-desc">{{ item.day }}报警（已处理/未处理）</div>
			<div class="mt10 fb">
				<span class="total">{{item.count}}</span>
				<span class="f12">({{item.disposed_count}}/{{ item.undisposed_count }})</span>
			</div>
		</div>
	</div>
	<!-- <div class="alarm-item flex">
		<div class="ai-title fb">网关报警</div>
		<div class="ai-today">
			<div class="f12 ait-desc">今日报警（已处理/未处理）</div>
			<div class="mt10 fb">
				<span class="total">4</span>
				<span class="f12">(0/4)</span>
			</div>
		</div>
		<div class="ai-today">
			<div class="f12 ait-desc">近7天报警（已处理/未处理）</div>
			<div class="mt10 fb">
				<span class="total">14</span>
				<span class="f12">(0/14)</span>
			</div>
		</div>
		<div class="ai-today">
			<div class="f12 ait-desc">近30天报警（已处理/未处理）</div>
			<div class="mt10 fb">
				<span class="total">34</span>
				<span class="f12">(0/34)</span>
			</div>
		</div>
	</div> -->
</div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { getTotalByDeviceAlarm } from '@/api/kdStatistics'
const state = reactive({
	list:[],
})
onMounted(()=>{
	getTotalByDeviceAlarm().then(res=>{
		state.list = res.data
	})
})
</script>

<style lang="scss" scoped>
.alarm-box{
	width: 100%;
	.alarm-item{
		width: 100%;
		border-bottom: 1px solid #f4f4f4;
		padding: 20px;
		&:last-child{
			border-bottom: none;
		}
	}
	.ai-title{
		width: 150px;
	}
	.ai-today{
		flex: 1;
		.total{
			font-size: 18px;
			margin-right: 10px;
		}
	}
	.ait-desc{
		color: #777;
	}
}
</style>