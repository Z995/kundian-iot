<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/list/CqkdHaiKang.vue  -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-15 16:11:28  -->
<template>
<div>
	<a-form :model="state.form" ref="formRef" :label-col-props="{flex:'150px'}" :wrapper-col-props="{flex:1}">
		<a-form-item label="设备名称" field="name" :rules="[{required:true,message:'展示名称必填'}]">
			<a-input v-model="state.form.name" class="w600" placeholder="请输入设备展示名称"></a-input>
		</a-form-item>
		<a-form-item label="设备序列号" field="deviceSerial" :rules="[{required:true,message:'设备序列号必填'}]">
			<div class="w600">
				<a-input v-model="state.form.deviceSerial" placeholder="请输入设备序列号"></a-input>
				<div class="tips mt10">存在英文字母的设备序列号，字母需为大写</div>
			</div>
		</a-form-item>
		<a-form-item label="设备验证码" field="validateCode" :rules="[{required:true,message:'设备验证码必填'}]">
			<div class="w600">
				<a-input v-model="state.form.validateCode" placeholder="请输入设备验证码"></a-input>
				<div class="tips mt10">设备机身上的六位大写字母</div>
			</div>
		</a-form-item>
		<a-form-item label="监控位置">
			<div class="w600" v-if="!state.loading">
				<kd-select-map-location :address="state.form.address" :lngLat="state.location" @success="getMapData"></kd-select-map-location>
			</div>
		</a-form-item>
		<a-form-item>
			<a-button type="primary" class="w120">保存</a-button>
		</a-form-item>
	</a-form>
</div>
</template>

<script setup>
import KdSelectMapLocation from '@/components/KdSelectMapLocation/index.vue'
import { reactive } from 'vue';
const state = reactive({
	loading:false,
	form:{
		name:"",				//设备名称
		deviceSerial:"",		//设备序列号
		validateCode:"",		//设备验证码
		address:"",				//设备位置
	}
})
</script>

<style>
</style>