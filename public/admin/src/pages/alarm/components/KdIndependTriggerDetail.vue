<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/alarm/components/KdIndependTriggerDetail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 10:29:28  -->
<template>
<div v-if="state.show">
	<a-modal title="独立触发器信息" v-model:visible="state.show" width="600px" :footer="null">
		<a-spin :loading="state.loading" tip="数据加载中..." style="width: 100%;">
			<div class="info-box">
				<div v-if="state.info" class="info-box">
					<div class="info-item flex">
						<div class="ii-title">触发器名称：</div>
						<div class="ii-value">{{state.info.name}}</div>
					</div>
					<div class="info-item flex">
						<div class="ii-title">模板和变量：</div>
						<div class="ii-value">
							<span>{{ state.info.device?.name || '-'}}-</span>
							<span>{{ state.info.subordinate?.subordinate?.name || '-'}}-</span>
							<span>{{ state.info.subordinateVariable?.name || '-'}}-</span>
						</div>
					</div>
					<div class="info-item flex">
						<div class="ii-title">触发条件：</div>
						<div class="ii-value">
							<span v-if="state.info.condition===0">开关OFF</span>
							<span v-if="state.info.condition===1">开关ON</span>
							<span v-if="state.info.condition===2">数值小于{{state.info?.condition_parameter.A}}</span>
							<span v-if="state.info.condition===3">数值大于{{state.info?.condition_parameter.B}}</span>
							<span v-if="state.info.condition===4">数值大于{{state.info?.condition_parameter.A}}且小于{{state.info?.condition_parameter.B}}</span>
							<span v-if="state.info.condition===5">数值小于{{state.info?.condition_parameter.A}}或大于{{state.info?.condition_parameter.B}}</span>
							<span v-if="state.info.condition===6">数值等于{{state.info?.condition_parameter.A}}</span>
						</div>
					</div>
					<div class="info-item flex">
						<div class="ii-title">报警：</div>
						<div class="ii-value">
							<a-switch disabled v-model="state.info.is_alarm" :checked-value="1" :unchecked-value="0"></a-switch>
						</div>
					</div>
					<template v-if="state.info.is_alarm ===1">
						<div class="info-item flex">
							<div class="ii-title">报警推送内容：</div>
							<div class="ii-value">{{ state.info.alarm_push}}</div>
						</div>
						<div class="info-item flex">
							<div class="ii-title">恢复正常推送内容：</div>
							<div class="ii-value">{{ state.info.resume_push}}</div>
						</div>
					</template>
					<div class="info-item flex">
						<div class="ii-title">联动：</div>
						<div class="ii-value"><a-switch disabled v-model="state.info.is_linkage" :checked-value="1" :unchecked-value="0"></a-switch></div>
					</div>
					<template v-if="state.info.is_linkage===1">
						<div class="info-item flex">
							<div class="ii-title">联动变量：</div>
							<div class="ii-value">
								{{state.info?.linkageDevice?.name}} -
								{{state.info?.linkageSubordinate?.subordinate?.name}} -
								{{state.info?.linkageSubordinateVariableId.name}} 
								<span v-if="state.info.linkage_type ===2">（{{state.info?.control_type ===2 ?'手动下发':'取报警值'}}）</span>
							</div>
						</div>
						<div class="info-item flex" v-if="state.info?.control_type ===2 && state.info.linkage_type ===2">
							<div class="ii-title">下发数据：</div>
							<div class="ii-value">{{state.info?.number}}</div>
						</div>
					</template>
				</div>
			</div>
		</a-spin>
	</a-modal>
</div>
</template>

<script setup>
import { reactive } from 'vue';
import { getIndependentTriggerDetail } from "@/api/kdTrigger"
const state = reactive({
	show:false,
	loading:false,
	info:null,
})

function show(id){
	state.show = true
	state.loading = true
	getIndependentTriggerDetail({id}).then(res=>{
		state.loading = false
		state.info = res.data
	}).catch(()=>{
		state.loading = false
	})
}
defineExpose({
	show
})
</script>

<style lang="scss" scoped>
.info-box{
	width: 100%;
	height: 400px;
	font-size: 12px;
	font-weight: 300;
	.info-item{
		width: 100%;
		padding: 10px 20px;
		.ii-title{
			width: 130px;
		}
		.ii-value{
			color: #333;
		}
	}
}
</style>