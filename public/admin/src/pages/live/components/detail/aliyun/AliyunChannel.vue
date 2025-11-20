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
<div class="al-channel">
	<a-table class="kd-small-table" :pagination="false" :bordered="false" :data="state.list"
		:columns="[
			{title:'国标 ID',dataIndex:'GbId',width:230},
			{title:'通道名称',slotName:'Name'},
			{title:'通道 ID',dataIndex:'ChannelId'},
			{title:'流 ID',dataIndex:'StreamId'},
			{title:'流状态',slotName:'StreamStatus'},
			{title:'操作',slotName:'action'},
		]"
		:loading="state.loading"
	>
		<template #Name="{record}">
			<span>{{record.Name}}</span>
			<span class="kd-link ml10" @click="state.form.show = true">
				<i class="ri-edit-line"></i>
			</span>
		</template>
		<template #StreamStatus="{record}">
			<a-tag v-if="record.StreamStatus==='on'" color="#00CC66" size="mini">在线</a-tag>
			<a-tag v-else color="#999" size="mini">离线</a-tag>
		</template>
		<template #action="{record}">
			<a-popconfirm content="确认删除通道吗？">
				<a-button type="text" size="mini" class="ml-10">删除</a-button>
			</a-popconfirm>
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
			StreamStatus: "off",
		    GbId: "3100000****000000002",
		    Params: {},
		    DeviceId: "348*****380-cn-qingdao",
		    ChannelId: 0,
		    DeviceStatus: "on",
		    Name: "通道1",
		    StreamId: "323*****997-cn-qingdao"
		},
		{
			StreamStatus: "off",
		    GbId: "3100000****000000002",
		    Params: {},
		    DeviceId: "348*****380-cn-qingdao",
		    ChannelId: 0,
		    DeviceStatus: "on",
		    Name: "通道1",
		    StreamId: "323*****997-cn-qingdao"
		},
	],
	form:{
		show:false,
		name:''
	}
})
</script>

<style lang="scss" scoped>

</style>