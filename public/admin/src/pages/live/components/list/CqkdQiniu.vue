<!-- 坤典物联-七牛云设备添加 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/list/CqkdQiniu.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 16:08:36  -->
<template>
<div>
	<a-form :model="state.form" ref="formRef" :label-col-props="{flex:'150px'}" :wrapper-col-props="{flex:1}">
		<a-form-item label="设备类型">
			<a-radio-group v-model="state.form.device_type">
			    <a-radio :value="1">摄像头</a-radio>
			    <a-radio :value="2">平台（监控录像机/NVR）</a-radio>
			</a-radio-group>
		</a-form-item>
		<a-form-item label="设备名称" field="desc" :rules="[{required:true,message:'设备名称必填'}]">
			<a-input v-model="state.form.desc" class="w600" placeholder="请输入设备名称"></a-input>
		</a-form-item>
		<a-form-item label="设备位置">
			<div class="w600" v-if="!state.loading">
				<kd-select-map-location :lngLat="state.location" @success="getMapData"></kd-select-map-location>
			</div>
		</a-form-item>
		<a-form-item>
			<a-button type="primary" class="w120" @click="saveData">保存</a-button>
		</a-form-item>
	</a-form>
</div>
</template>

<script setup>
import KdSelectMapLocation from '@/components/KdSelectMapLocation/index.vue'
import { saveLiveData,getLiveDetail } from '@/api/kdLive'
import { reactive, ref } from 'vue';
import { Message } from '@arco-design/web-vue';
import router from '../../../../router';
import { onMounted } from 'vue';
const formRef = ref()
const state = reactive({
	loading:false,
	form:{
		type:"kun_dian",	//所属平台
		device_type:1, 		//1：摄像头，2：平台
		desc:"",			//设备名称
		longitude:'',
		latitude:'',
	},
	location:[],
})

function getMapData(data){
	state.form.longitude = data.location[0]
	state.form.latitude = data.location[1]
}

async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) return false;
	let form = JSON.parse(JSON.stringify(state.form))
	let res = await saveLiveData(form)
	if( res.code === 200 ){
		Message.success("保存成功")
		setTimeout(function(){
			router.go(-1)
		},1000)
		return
	}
	Message.error(res.msg)
}
</script>

<style>
</style>