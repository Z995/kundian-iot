<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/staff/components/list.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-20 10:30:03  -->
<template>
<div v-if="state.show">
	<a-modal title="添加/编辑员工管理" v-model:visible="state.show" width="600px" :on-before-ok="saveDate">
	    <a-spin :loading="state.loading">
	        <a-form ref="formRef" :model="state.form" :label-col-props="{flex:'110px'}" :wrapper-col-props="{flex:'400px'}">
	            <a-form-item label="员工姓名" field="name" :rules="[{required:true,message:'姓名必填'}]">
	                <a-input v-model="state.form.name" placeholder="请输员工姓名"></a-input>
	            </a-form-item>
	            <a-form-item label="性别">
	                <a-radio-group v-model="state.form.sex">
	                    <a-radio :value="1">男</a-radio>
	                    <a-radio :value="2">女</a-radio>
	                </a-radio-group>
	            </a-form-item>
	            <a-form-item label="职务">
	                <a-input v-model="state.form.appointment" placeholder="请输入职务"></a-input>
	            </a-form-item>
	            <a-form-item label="电话">
	                <a-input v-model="state.form.phone" placeholder="请输入联系方式"></a-input>
	            </a-form-item>
	            <a-form-item label="出生年月">
	                <a-date-picker v-model="state.form.age" style="width: 200px;" />
	            </a-form-item>
	            <a-form-item label="备注">
	                <a-input v-model="state.form.remarks" placeholder="请输入备注"></a-input>
	            </a-form-item>
	            <a-form-item label="头像">
	                <a-upload
	                    list-type="picture-card"
	                    action="/"
	                    :default-file-list="state.fileList"
	                    image-preview
	                />
	            </a-form-item>
	        </a-form>
	    </a-spin>
	</a-modal>
</div>
</template>

<script setup>
import { reactive } from 'vue';
const state = reactive({
	show:false,
	form:{
		name:'',
		phone:"",
		appointment:'',
		age:'',
		sex:1,
		remar:'',
		entry_time:'',
		status:1
	}
})

function show(data){
	state.show = true
}

defineExpose({
	show
})

</script>

<style>
</style>