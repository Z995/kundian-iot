<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/account/components/KdAccountAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 15:13:16  -->
<template>
<div v-if="state.show">
	<a-drawer title="新增用户" v-model:visible="state.show" :on-before-ok="saveData" width="800px">
		<a-form :model="state.form" ref="formRef" :label-col-props="{flex:'120px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="用户名" field="name" :rules="[{required:true,message:'用户名必填'}]">
				<a-input v-model="state.form.name" placeholder="请输入用户名" class="w500"></a-input>
			</a-form-item>
			<a-form-item label="登录账号" field="phone" :rules="[{required:true,message:'登录账号必填'}]">
				<a-input v-model="state.form.phone" placeholder="请输入登录账号/手机号" class="w500"></a-input>
			</a-form-item>
			<a-form-item v-if="!state.form.id" label="登陆密码" field="password" :rules="[{required:true,message:'登陆密码必填'}]">
				<a-input v-model="state.form.password" placeholder="请输入登陆密码" class="w500"></a-input>
			</a-form-item>
			<!-- <a-form-item label="用户备注">
				<a-textarea placeholder="请输入备注" class="w500" style="height: 70px;"></a-textarea>
			</a-form-item> -->
			<a-form-item label="状态">
				<a-switch v-model="state.form.status" :checked-value="1" :unchecked-value="0" checked-text="正常" unchecked-text="禁用"></a-switch>
			</a-form-item>
		</a-form>
	</a-drawer>
</div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { saveAccountData } from '@/api/kdAccount'
import { Message } from '@arco-design/web-vue';
const emits = defineEmits(['success'])
const formRef = ref()
const state = reactive({
	show:false,
	form:{
		id:0,
		name:'',
		phone:'',
		password:'',
		status:1,
	}
})

function show(data){
	state.show = true
	state.form = {
		id:data?data.id : 0,
		name:data?data.name : '',
		phone:data?data.phone : '',
		password:'',
		status:data?parseInt(data.status) : 1,
	}
}

async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) return false;
	
	let form = JSON.parse(JSON.stringify(state.form))
	if( !form.id ) delete form.id
	
	let res = await saveAccountData(form)
	if( res.code ===200 ){
		Message.success("保存成功")
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