<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/list/CqkdGeneralLive.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-15 16:03:18  -->
<template>
<div>
	<a-spin :loading="state.saving" tip="数据保存中..." style="width: 100%;">
		<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'150px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="监控名称" field="name" :rules="[{required:true,message:'监控名称必填'}]">
				<a-input v-model="state.form.name" placeholder="请输入监控名称" class="w600"></a-input>
			</a-form-item>
			<a-form-item label="m3u8地址" >
				<div>
					<a-input v-model="state.form.hls" placeholder="请输入m3u8直播流地址" class="w600"></a-input>
					<div class="tips mt10">m3u8播放源编码支持H.264格式</div>
				</div>
			</a-form-item>
			<a-form-item label="flv地址" >
				<div>
					<a-input v-model="state.form.flv" placeholder="请输入flv直播流地址" class="w600"></a-input>
					<div class="tips">m3u8播放源编码支持H.264，H.265格式</div>
				</div>
			</a-form-item>
			<a-form-item label="监控位置">
				<div class="w600" v-if="!state.loading">
					<kd-select-map-location :lngLat="state.location" @success="getMapData"></kd-select-map-location>
				</div>
			</a-form-item>
			<a-form-item label="监控备注">
				<a-textarea v-model="state.form.desc" placeholder="请输入监控备注" class="w600" style="height: 120px;"></a-textarea>
			</a-form-item>
			<a-form-item>
				<a-button type="primary" class="w120" @click="saveData" :loading="state.saving">保存</a-button>
			</a-form-item>
		</a-form>
	</a-spin>
</div>
</template>

<script setup>
import KdSelectMapLocation from '@/components/KdSelectMapLocation/index.vue'
import { saveLiveData,getLiveDetail } from '@/api/kdLive'
import { reactive, ref } from 'vue';
import { Message } from '@arco-design/web-vue';
import router from '../../../../router';
import { onMounted } from 'vue';
const props = defineProps({
	id:{
		type:[Number,String],
		default:0,
	}
})
const formRef = ref()
const state = reactive({
	show:false,
	loading:false,
	saving:false,
	form:{
		type:'local',		//普通直播流
		name:'',			//监控名称
		desc:'',			//监控详情
		state:'',			//
		rtmp:'',			
		hls:'',
		flv:'',
		webrtc:'',
		longitude:'',
		latitude:'',
	}
})

onMounted(async ()=>{
	if( props.id ){
		state.loading = true
		let res = await getLiveDetail({id:props.id})
		state.loading = false
		if( res.data ){
			state.form = {
				id:res.data.id,
				type:'local',		
				name:res.data.name,			
				desc:res.data.desc,			
				state:res.data.state,			
				rtmp:res.data.rtmp,			
				hls:res.data.hls,
				flv:res.data.flv,
				webrtc:res.data.webrtc,
				longitude:res.data.longitude,
				latitude:res.data.latitude,
			}
			if( res.data.longitude && res.data.latitude ){
				state.location = [res.data.longitude,res.data.latitude]
			}
		}
	}
})

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

function getMapData(data){
	state.form.longitude = data.location[0]
	state.form.latitude = data.location[1]
}
</script>

<style>
</style>