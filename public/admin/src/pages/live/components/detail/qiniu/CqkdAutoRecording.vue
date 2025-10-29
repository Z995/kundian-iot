<!-- 坤典物联-自动录制配置 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/qiniu/CqkdAutoRecording.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 17:23:36  -->
<template>
<div v-if="state.show">
	<a-modal title="自动录制配置" v-model:visible="state.show" width="500px" :on-before-ok="saveData">
		<a-form :model="state.form" ref="formRef" :label-col-props="{flex:'100px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="录制周期">
				<a-radio-group v-model="state.form.op_type">
					<a-radio :value="1">每天</a-radio>
					<a-radio :value="2">每周</a-radio>
					<a-radio :value="3">固定时间点</a-radio>
				</a-radio-group>
			</a-form-item>
			<a-form-item label="每天录制" v-if="state.form.op_type===1">
				<div>
					<a-space>
						<a-input v-model="state.form.day" placeholder="请输入">
							<template #prepend>每</template>
							<template #append>天</template>
						</a-input>
						<a-time-picker v-model="state.form.time" class="w160" format="HH:mm"/>
					</a-space>
					<div class="tips mt5">每{{state.form.day}}天 {{state.form.time}}录制一次</div>
				</div>
			</a-form-item>
			<a-form-item label="每周录制" v-if="state.form.op_type===2">
				<div>
					<a-space>
						<a-select placeholder="请选择" v-model="state.form.week">
							<a-option :value="1">周一</a-option>
							<a-option :value="2">周二</a-option>
							<a-option :value="3">周三</a-option>
							<a-option :value="4">周四</a-option>
							<a-option :value="5">周五</a-option>
							<a-option :value="6">周六</a-option>
							<a-option :value="7">周日</a-option>
						</a-select>
						<a-time-picker v-model="state.form.time" class="w160" format="HH:mm"/>
					</a-space>
					<div class="tips mt5">每周{{state.form.week}}{{state.form.time}}录制一次</div>
				</div>
			</a-form-item>
			<a-form-item label="固定时间" v-if="state.form.op_type===3">
				<div>
					<a-date-picker
						v-model="state.form.date"
					    show-time
					    format="YYYY-MM-DD HH:mm:ss"
					  />
					<div class="tips mt5">{{state.form.date}}录制一次</div>
				</div>
			</a-form-item>
			<a-form-item label="录制时长">
				<a-input-number v-model="state.form.recording_duration" class="w200" :min="5" placeholder="请输入录制时长">
					<template #append>秒</template>
				</a-input-number>
			</a-form-item>
			<a-form-item label="是否启用">
				<a-switch v-model="state.form.status"
					:checked-value="1" :unchecked-value="0"
					checked-text="启用" unchecked-text="禁用"
				></a-switch>
			</a-form-item>
		</a-form>
	</a-modal>
</div>
</template>

<script setup>
import { Message } from '@arco-design/web-vue';
import { reactive } from 'vue';
import {saveLiveAutoShotAndRecordSet,getLiveAutoShotAndRecordData} from '@/api/kdLive'
const state = reactive({
	show:false,
	form:{
		type:2,		//1截图配置 2录制配置
		monitor_id:0,
		channels:'',
		op_type:1,	//1每天 2每周 3时间点
		day:null,		
		week:'',
		date:'',
		time:"",
		recording_duration:null,
		status:0,
	}
})

async function show(live_id,channels){
	state.show = true
	let param = {
		monitor_id:live_id,
		channels:channels || '',
		type:2
	}
	let data = null
	let res = await getLiveAutoShotAndRecordData(param)
	if( res.code ===200 && res.data ){
		data = res.data
	}
	let day = null,week = '',date = '',time = ''
	if( data ){
		if( data.op_type ===1 ){
			let temp = data.op_value.split(',')
			day = parseInt(temp[0])
			time = temp[1]
		}
		if( data.op_type ===2 ){
			let temp = data.op_value.split(',')
			week = parseInt(temp[0])
			time = temp[1]
		}
		if( data.op_type ===3 ){
			date = data.op_value
		}
	}
	state.form = {
		type:2,
		monitor_id:live_id,
		channels:channels || '',
		op_type:data?data.op_type :1,
		day:day,
		week:week,
		date:date,
		time:time,
		status:data?data.status : 0,
		recording_duration:data?data.recording_duration : null,
	}
}
//保存配置
async function saveData(){
	let { op_type,day,week,date,time } = state.form
	let param = {
		type:state.form.type,		
		monitor_id:state.form.monitor_id,
		channels:state.form.channels,
		op_type:op_type,
		status:state.form.status,
		recording_duration:state.form.recording_duration,
		op_value:""
	}
	if( op_type ===1){
		if( !day || !time ){
			Message.warning("请输入截图时间")
			return false;
		}
		param.op_value = `${day},${time}`
	}
	if( op_type ===2 ){
		if( !week || !time ){
			Message.warning("请选择截图时间")
			return false;
		}
		param.op_value = `${week},${time}`
	}
	if( op_type ===3 ){
		if( !date ){
			Message.warning("请选择截图时间")
			return false;
		}
		param.op_value = date
	}
	let res = await saveLiveAutoShotAndRecordSet(param)
	if( res.code === 200 ){
		Message.success("配置保存成功")
		return true
	}
	Message.error(res.msg)
	return false;
}
defineExpose({
	show
})
</script>

<style>
</style>