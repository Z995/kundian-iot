<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 14:23:19  -->
<template>
<kd-page-box>
	<div class="total-box flex">
		<div class="total-item">
			<div class="ti-title f12 fb">设备状态统计</div>
			<kd-total-pie type="deviceStatus" canvas-id="statusPine"></kd-total-pie>
		</div>
		<div class="total-item">
			<div class="ti-title f12 fb">设备标签统计</div>
			<kd-total-pie type="deviceLabel" canvas-id="labelPine"></kd-total-pie>
		</div>
		<div class="total-item">
			<div class="ti-title f12 fb">网关状态统计</div>
			<kd-total-pie type="gatewayStatus" canvas-id="gatewayPine"></kd-total-pie>
		</div>
	</div>
	<!-- 报警统计 -->
	<div class="kd-content mt20">
		<div class="ti-title f12 fb mb10">报警统计</div>
		<kd-total-alarm></kd-total-alarm>
	</div>
	
	<!-- 新增设备/网关统计  -->
	<div class="add-box flex mt20">
		<div class="add-item">
			<div class="ti-title f12 fb mb10">新增设备</div>
			<kd-line-add type="deviceNew" ref="deviceLineRef" canvas-id="deviceLine"></kd-line-add>
		</div>
		<div class="add-item">
			<div class="ti-title f12 fb mb10">新增网关</div>
			<kd-line-add type="gatewayNew" ref="gatewayLineRef" canvas-id="gatewayLine"></kd-line-add>
		</div>
	</div>
</kd-page-box>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import KdTotalPie from './components/KdTotalPie.vue';
import KdTotalAlarm from './components/KdTotalAlarm.vue';
import KdLineAdd from './components/KdLineAdd.vue';
const deviceLineRef = ref()
const gatewayLineRef = ref()

onMounted(()=>{
	
})
</script>

<style lang="scss" scoped>
.total-box{
	width: 100%;
	gap:20px;
	.total-item{
		flex: 1;
		height: 100%;
		background-color: #fff;
		border-radius: 10px;
		padding: 20px;
	}
}
.add-box{
	width: 100%;
	gap:20px;
	.add-item{
		flex:1;
		background-color: #fff;
		border-radius: 10px;
		padding: 20px;
	}
}
</style>