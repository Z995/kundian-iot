<!-- 坤典物联-->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/alarm/components/KdAlarmSetAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 11:09:27  -->
<template>
<a-drawer title="添加报警配置" v-model:visible="state.show" :on-before-ok="saveData" width="800px">
	<a-form :model="state.form" ref="formRef" :label-col-props="{flex:'130px'}" 
		:wrapper-col-props="{flex:1}" v-if="state.show"
	>
		<a-form-item label="报警配置名称" field="name" :rules="[{required:true,message:'报警配置名称必填'}]">
			<a-input v-model="state.form.name" placeholder="请输入报警配置名称" class="w500"></a-input>
		</a-form-item>
		<a-form-item label="推送类型" field="type" :rules="[{required:true,message:'推送类型必选'}]">
			<a-radio-group v-model="state.form.type">
				<a-radio :value="1">设备</a-radio>
				<a-radio :value="2">模板触发器</a-radio>
				<a-radio :value="3">独立触发器</a-radio>
			</a-radio-group>
		</a-form-item>
		<a-form-item label="选择设备" field="did" :rules="[{required:true,message:'设备必选'}]" v-if="state.form.type===1">
			<a-select placeholder="请选择设备" v-model="state.form.did">
				<a-option>设备1</a-option>
				<a-option>设备2</a-option>
				<a-option>设备3</a-option>
			</a-select>
		</a-form-item>
		<a-form-item label="选择触发器" field="did" :rules="[{required:true,message:'触发器必选'}]" v-else>
			<a-select placeholder="请选择触发器" v-model="state.form.did">
				<a-option>触发器1</a-option>
				<a-option>触发器2</a-option>
				<a-option>触发器3</a-option>
			</a-select>
		</a-form-item>
		<a-form-item label="推送机制" field="jizhi" :rules="[{required:true,message:'推送机制必填'}]">
			<div>
				<div>变量值达到触发条件时</div>
				<a-radio-group direction="vertical" v-model="state.form.jizhi">
					<a-radio :value="1">仅第一次推送</a-radio>
					<a-radio :value="2">报警沉默时间 <a-input class="w120"></a-input> 分钟</a-radio>
				</a-radio-group>
			</div>
		</a-form-item>
		<a-form-item label="推送方式" field="method" :rules="[{required:true,message:'推送方式必填'}]">
			<a-checkbox-group v-model="state.form.method">
			    <a-checkbox value="1">短信</a-checkbox>
			    <a-checkbox value="2">邮件</a-checkbox>
			    <a-checkbox value="3">企业微信</a-checkbox>
			</a-checkbox-group>
		</a-form-item>
		<a-form-item label="推送人" field="user" :rules="[{required:true,message:'推送人必选'}]">
			<a-select placeholder="请选择推送人" v-model="state.form.user" multiple>
				<a-option>用户1</a-option>
				<a-option>用户2</a-option>
				<a-option>用户3</a-option>
			</a-select>
		</a-form-item>
		<a-form-item label="推送规则描述">
			<a-input placeholder="请输入推送规则描述" class="w500"></a-input>
		</a-form-item>
	</a-form>
</a-drawer>
</template>

<script setup>
import { reactive, ref } from 'vue';
const formRef = ref()
const state = reactive({
	show:false,
	form:{
		name:"",
		type:1,
		did:'',
		jizhi:1,
		method:[],
		user:[],
	}
})

function show(){
	state.show = true
}
async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) return false
}

defineExpose({
	show
})
</script>

<style>
</style>