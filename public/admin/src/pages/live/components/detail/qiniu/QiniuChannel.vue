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
	<a-table class="kd-small-table" :bordered="false" :data="monitorState.state.channelList"
		:columns="[
			{title:'通道国标ID',dataIndex:'gbId'},
			{title:'通道名称',dataIndex:'name'},
			{title:'通道状态',slotName:'state'},
			{title:'厂家',dataIndex:'vendor'},
			{title:'最近同步时间',slotName:'lastSyncAt'},
			{title:'操作',slotName:'action'},
		]"
	>
		<template #state="{record}">
			<a-tag v-if="record.state ==='online'" color="#00CC66" size="mini">在线</a-tag>
			<a-tag v-if="record.state ==='offline'" color="gray" size="mini">离线</a-tag>
			<a-tag v-if="record.state ===''" color="gray" size="mini">未知</a-tag>
			<a-tag v-if="record.state ==='notReg'" color="#FF9933" size="mini">未注册</a-tag>
			<a-tag v-if="record.state ==='locked'" color="#FF3333" size="mini">锁定</a-tag>
		</template>
		<template #lastSyncAt="{record}">
			<span>{{ formatTime(record.lastSyncAt * 1000)}}</span>
		</template>
		<template #action="{record}">
			<a-button type="text" size="mini" class="ml-10" @click="previewChannel(record.gbId)">预览</a-button>
		</template>
	</a-table>
	<div class="qn-total flex-c mt15">
		<span>通道总数：<span class="count">{{ monitorState.state.channelTotal.all }}</span></span>
		<span class="online">● 在线：<span class="count">{{ monitorState.state.channelTotal.online }}</span></span>
		<span class="offline">● 离线：<span class="count">{{ monitorState.state.channelTotal.offline }}</span></span>
	</div>
</div>
</template>

<script setup>
import { getLiveChanngeList } from '@/api/kdLive'
import { formatTime } from '@/util/util'
import { useMonitorStore } from '@/store/monitor'
const monitorState = useMonitorStore()

const emits = defineEmits(['preview'])

function previewChannel(data){
	emits("preview",data)
}

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