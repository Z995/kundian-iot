<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/haiKang/HaiKangChannel.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 10:46:32  -->
<template>
<div class="hk-channel">
	<a-table class="kd-small-table" :pagination="false" :bordered="false" :data="state.list"
		:columns="[
			{title:'备序列号',dataIndex:'deviceSerial'},
			{title:'通道名',slotName:'channelName'},
			{title:'通道号',dataIndex:'channelNo'},
			{title:'在线状态',slotName:'status'},
			{title:'视频质量',slotName:'videoLevel'},
		]"
		:loading="state.loading"
	>
		<template #channelName="{record}">
			<span>{{record.channelName}}</span>
			<span class="kd-link ml10" @click="state.form.show = true">
				<i class="ri-edit-line"></i>
			</span>
		</template>
		<template #status="{record}">
			<a-tag v-if="record.status===1" color="#00CC66" size="mini">在线</a-tag>
			<a-tag v-else color="#999" size="mini">离线</a-tag>
		</template>
		<template #videoLevel="{record}">
			<span v-if="record.videoLevel===0">流畅</span>
			<span v-if="record.videoLevel===1">均衡</span>
			<span v-if="record.videoLevel===2">高清</span>
			<span v-if="record.videoLevel===3">超清</span>
		</template>
	</a-table>
	
	<div v-if="state.form.show">
		<a-modal title="修改通道名称" v-model:visible="state.form.show" width="400px" :on-before-ok="saveData">
			<a-input v-model="state.form.name" placeholder="请输入通道名称"></a-input>
		</a-modal>
	</div>
</div>
</template>

<script setup>
import { reactive } from 'vue';

const state = reactive({
	loading:false,
	list:[
		{
			deviceSerial: "427734222",
			ipcSerial: "427734222",
			channelNo: 1,
			deviceName: "My(427734222)427734222",
			channelName: "My(427734222)427734222",
			status: 1,
			isShared: "0",
			isEncrypt: 0,
			videoLevel: 2,
		},
		{
			deviceSerial: "427734222",
			ipcSerial: "427734222",
			channelNo: 2,
			deviceName: "My(427734222)427734222",
			channelName: "My(427734222)427734222",
			status: 1,
			isShared: "0",
			isEncrypt: 0,
			videoLevel: 2,
		}
	],
	form:{
		show:false,
		name:'',
	}
})
</script>

<style lang="scss" scoped>
</style>