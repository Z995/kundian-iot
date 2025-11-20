<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/CqkdHaiKangDetail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-15 17:00:18  -->
<template>
<div>
	<a-tabs default-active-key="1">
	    <a-tab-pane key="1" title="基本信息">
			<hai-kang-info :info="state.info" :statusList="state.deviceStatus"></hai-kang-info>
	    </a-tab-pane>
	    <a-tab-pane key="2" title="通道列表">
			<hai-kang-channel></hai-kang-channel>
	    </a-tab-pane>
	    <a-tab-pane key="3" title="云台控制">
			<hai-kang-ptz></hai-kang-ptz>
	    </a-tab-pane>
	    <a-tab-pane key="4" title="截图管理">
			<hai-kang-screenshot></hai-kang-screenshot>
	    </a-tab-pane>
	    <a-tab-pane key="5" title="本地录像">
			<hai-kang-recording></hai-kang-recording>
	    </a-tab-pane>
	</a-tabs>
</div>
</template>

<script setup>
import HaiKangInfo from './haiKang/HaiKangInfo.vue';
import HaiKangChannel from './haiKang/HaiKangChannel.vue';
import HaiKangPtz from './haiKang/HaiKangPtz.vue';
import HaiKangScreenshot from './haiKang/HaiKangScreenshot.vue';
import HaiKangRecording from './haiKang/HaiKangRecording.vue';
import { reactive } from 'vue';

const state = reactive({
	info:{
		displayName: "DS-3E1518P-E-230W(K96719611)",
		fullSerial: "K96719611",
		model: "DS-3E1500",
		category: "UNKNOWN",
		status: 1,
		supportWifi: 0,
		releaseVersion: "1.7.0",
		version: "V1.0.0 build 221213",
		availableChannelCount: 1,
		relatedDeviceCount: 0,
		supportCloud: "0",
		supportExt: "{\"support_device_light\":\"1\"}",
		parentCategory: "COMMON",
		netAddress: "47.98.160.100",
		signal: "100%",
		netType: "wire",
	},
	deviceStatus:[
		{name:'隐私状态',value:0,value_txt:'打开'},
		{name:'红外状态',value:0,value_txt:'启用'},
		{name:'告警声音模式',value:0,value_txt:'短叫'},
		{name:'电池电量',value:0,value_txt:'100%'},
		{name:'云存储状态',value:0,value_txt:'激活'},
		{name:'信号强度',value:0,value_txt:'100%'},
		{name:'休眠状态',value:0,value_txt:'休眠'},
		{name:'sd硬盘状态',value:0,value_txt:'正常'},
	]
})
</script>

<style lang="scss" scoped>

</style>