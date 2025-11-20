<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/components/CqkdGatewayLinkDevice.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-29 10:10:28  -->
<template>
<div v-if="state.show">
	<a-modal title="关联设备数" v-model:visible="state.show" width="700px" :footer="null">
		<div v-if="state.show">
			<a-table row-key="id" :data="state.list" class="kd-small-table"
				:bordered="false"
				:columns="[
					{title:'ID',dataIndex:'id'},
					{title:'设备名称',dataIndex:'name'},
					{title:'设备Code',dataIndex:'code'},
					{title:'设备状态',slotName:'status'},
					{title:'创建时间',dataIndex:'create_time'},
				]"
			>
				<template #status="{record}">
					<div class="device-status offline" v-if="record.status===0"><span class="dot"></span>离线</div>
					<div class="device-status online" v-else-if="record.status ===1"><span class="dot"></span>在线</div>
					<div class="device-status alarm" v-else-if="record.is_warning==1"><span class="dot"></span>报警</div>
				</template>
			</a-table>
		</div>
	</a-modal>
</div>
</template>

<script setup>
import { reactive } from 'vue';
const state = reactive({
	show:false,
	list:[],
})
function show(data){
	state.show = true
	state.list = data
}

defineExpose({
	show
})
</script>

<style lang="scss" scoped>
.device-status{
	font-size: 12px;
	.dot{
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: var(--color);
		display: inline-block;
		margin-right: 5px;
	}
}
.online{
	color: #3bce95;
	--color:#3bce95;
}
.offline{
	color: #999;
	--color:#999;
}
.alarm{
	color: red;
	--color:red;
}
.noset{
	color: orange;
	--color:orange;
}
</style>