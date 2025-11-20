<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/alarm/components/KdContactAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 09:40:58  -->
<template>
<a-modal title="新增联系人" v-model:visible="state.show" width="600px" :on-before-ok="saveData">
	<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'130px'}" :wrapper-col-props="{flex:1}">
		<a-form-item label="联系人姓名" field="name" :rules="[{required:true,message:'姓名必填'}]">
			<a-input v-model="state.form.name" placeholder="请输入联系人姓名"></a-input>
		</a-form-item>
		<a-form-item label="手机号">
			<a-input v-model="state.form.phone" placeholder="请输入手机号"></a-input>
		</a-form-item>
		<a-form-item label="邮箱">
			<a-input v-model="state.form.email" placeholder="请输入邮箱"></a-input>
		</a-form-item>
		<a-form-item label="企微群机器人地址">
			<a-input v-model="state.form.qi_wechat" placeholder="请输入企微群机器人地址"></a-input>
		</a-form-item>
		<a-form-item label="备注">
			<a-input v-model="state.form.remark" placeholder="请输入备注"></a-input>
		</a-form-item>
	</a-form>
</a-modal>
</template>

<script setup>
import { reactive, ref } from 'vue';
const emits = defineEmits(['success'])
const formRef = ref()
const state = reactive({
	show:false,
	form:{
		name:'',
		phone:'',
		remark:'',
		qi_wechat:'',
		email:'',
	}
})

function show(data){
	state.show = true
}
async function saveData(){
	
}
defineExpose({
	show
})
</script>

<style lang="scss" scoped>
</style>