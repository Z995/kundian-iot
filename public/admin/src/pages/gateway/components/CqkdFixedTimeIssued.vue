<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/components/CqkdFixedTimeIssued.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-15 09:57:41  -->
<template>
<div v-if="state.show">
	<a-modal title="定时下发" v-model:visible="state.show" width="700px" :on-before-ok="saveData">
		<a-form :model="state.form" ref="formRef" :rules="state.rules" :label-col-props="{flex:'100px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="状态">
				<a-radio-group v-model="state.form.status">
					<a-radio :value="1">已启用</a-radio>
					<a-radio :value="0">未启用</a-radio>
				</a-radio-group>
			</a-form-item>
			<a-form-item label="执行频率">
				<a-radio-group v-model="state.form.expire">
					<div class="time-box flex">
						<div class="time-item"><a-radio :value="1">1秒1次</a-radio></div>
						<div class="time-item"><a-radio :value="2">0.5秒1次</a-radio></div>
						<div class="time-item"><a-radio :value="3">60秒1次</a-radio></div>
						<div class="time-item"><a-radio :value="4">30秒1次</a-radio></div>
						<div class="time-item"><a-radio :value="5">3秒1次</a-radio></div>
						<div class="time-item"><a-radio :value="6">10秒1次</a-radio></div>
						<div class="time-item"><a-radio :value="7">10分钟1次</a-radio></div>
						<div class="time-item"><a-radio :value="8">60分钟1次</a-radio></div>
					</div>
				</a-radio-group>
			</a-form-item>
			<a-form-item label="自定义内容" field="val">
				<div style="width: 100%;">
					<a-textarea placeholder="自定义内容" v-model="state.form.val" style="height: 120px;;"></a-textarea>
					<a-radio-group type="button" v-model="state.form.val_type">
						<a-radio :value="1">ASCII</a-radio>
						<a-radio :value="2">HEX</a-radio>
					</a-radio-group>
				</div>
			</a-form-item>
		</a-form>
	</a-modal>
</div>
</template>
<script setup>
import { reactive, ref } from 'vue';
import { saveGatewayData } from '@/api/kdGateway'
import { Message } from '@arco-design/web-vue';
const formRef = ref()
const emits = defineEmits(['success'])
const state = reactive({
	show:false,
	info:null,
	form:{
		status:1,
		expire:1,
		val:'',
		val_type:1,
	},
	rules:{
		val:[
			{
				validator:(value,cb)=>{
					if( state.form.status ===1 && !value){
						cb("内容必填")
					}else{
						cb()
					}
				}
			},
		]
	}
})
function show(data){
	state.show = true
	state.info = data
	if( !data.crontab ){
		state.form = {
			status:1,
			expire:1,
			val:'',
			val_type:1,
		}
	}else{
		state.form = {
			status:data.crontab.status,
			expire:data.crontab.expire,
			val:data.crontab.val,
			val_type:data.crontab.val_type,
		}
	}
}

async function saveData(){
	let valid = await formRef.value.validate()
	if( valid) return false;
	let form = JSON.parse(JSON.stringify(state.form))
	// 接口请求操作
	let res = await saveGatewayData({...state.info,crontab:form})
	if( res.code === 200 ){
		Message.success("操作成功")
		emits('success')
		return true
	}
	Message.error(res.msg)
	return false
}

defineExpose({
	show
})
</script>
<style lang="scss" scoped>
.time-box{
	width: 100%;
	flex-wrap: wrap;
	.time-item{
		width: 130px;
	}
}
</style>