<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/device/components/CqkdUpdateDataValue.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-17 10:58:04  -->
<template>
<div v-if="state.show">
	<a-modal v-model:visible="state.show" width="500px" title="修改变量值" :on-before-ok="saveData">
		<a-form ref="formRef" :model="state" :label-col-props="{flex:'100px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="变量值" :rules='[{required:true,message:"请输入变量值"}]'>
				<a-input v-model="state.value" placeholder="请输入变量值"></a-input>
			</a-form-item>
		</a-form>
	</a-modal>
</div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { updateDeviceDataValue } from '@/api/kdDevice'
import { Message} from '@arco-design/web-vue'
const formRef = ref()
const emits = defineEmits(['success'])
const state = reactive({
	show:false,
	value:"",
	variable_id:"",	//变量id
})

function show(data){
	state.show = true
	state.value = data.value
	state.variable_id = data.id
}
//保存
async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) return false
	let param = {
		variable_id:state.variable_id,
		value:state.value
	}
	let res = await updateDeviceDataValue(param)
	if( res.code === 200 ){
		Message.success('变量值修改成功')
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

<style>
</style>