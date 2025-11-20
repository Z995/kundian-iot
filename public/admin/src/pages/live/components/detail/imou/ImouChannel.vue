<!-- 坤典物联-七牛云通道 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/qiniu/QiniuChannel.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 16:45:34  -->
<template>
<div class="qn-channel">
	<a-table class="kd-small-table" :pagination="false" :bordered="false" :data="state.list"
		:columns="[
			{title:'通道号',dataIndex:'channelId'},
			{title:'通道名称',slotName:'name'},
			{title:'通道状态',slotName:'state'},
			{title:'最后离线时间',dataIndex:'lastSyncAt'},
		]"
		:loading="state.loading"
	>
		<template #name="{record}">
			<span>{{record.name}}</span>
			<span class="kd-link ml10" @click="state.form.show = true">
				<i class="ri-edit-line"></i>
			</span>
		</template>
		<template #state="{record}">
			<a-tag v-if="record.state==='online'" color="#00CC66" size="mini">在线</a-tag>
			<a-tag v-else color="#999" size="mini">离线</a-tag>
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
	list:[
		{
		  channelId: "34020000001310000001",
		  name: "1F亮秀路车位-左",
		  state: "online",
		  vendor: "Dahua",
		  lastSyncAt: '2025-01-01 12:00:11'
		},
		{
		  channelId: "34020000001310000017",
		  name: "IPC",
		  state: "online",
		  vendor: "Dahua",
		  lastSyncAt: '2025-01-01 12:00:11'
		},
		{
		  channelId: "34020000001310000003",
		  name: "6F西北角",
		  state: "online",
		  vendor: "Dahua",
		  lastSyncAt: '2025-01-01 12:00:11'
		},
		{
		  channelId: "34020000001310000004",
		  name: "2F东南阳台",
		  state: "online",
		  vendor: "Dahua",
		  lastSyncAt: '2025-01-01 12:00:11'
		},
		{
		  channelId: "34020000001310000005",
		  name: "3F通道东北半球",
		  state: "online",
		  vendor: "Dahua",
		  lastSyncAt: '2025-01-01 12:00:11'
		},
	],
	form:{
		show:false,
		name:''
	}
})
</script>

<style lang="scss" scoped>
.qn-total{
	width: 100%;
	gap:30px;
	padding-left: 16px;
	letter-spacing: 2px;
	font-size: 13px;
	.online{
		color: #00CC66;
	}
	.offline{
		color: #999;
	}
	.count{
		font-weight: bold;
	}
}
</style>