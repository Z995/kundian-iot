<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/components/CqkdGatewayConfig.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-17 14:43:15  -->
<template>
<div v-if="state.show">
	<a-modal title="网关配置" v-model:visible="state.show" width="600px" :on-before-ok="saveData">
		<a-form :model="state.form" :label-col-props="{flex:'100px'}" :wrapper-col-props="{flex:1}" v-if="state.show">
			<a-form-item label="注册包" >
				<a-input placeholder="请输入注册包" v-model="state.form.code"></a-input>
			</a-form-item>
			<a-form-item label="数据字段">
				<div style="width: 100%;gap:20px;" class="flex">
					<a-input placeholder="redis key" v-model="state.form.val"></a-input>
					<a-radio-group type="button" v-model="state.form.vtype">
						<a-radio :value="0">ASCII</a-radio>
						<a-radio :value="1">HEX</a-radio>
					</a-radio-group>
				</div>
			</a-form-item>
			<a-form-item label="登录类型">
				<a-radio-group v-model="state.form.login">
					<a-radio :value="0">单点登录</a-radio>
					<a-radio :value="1">多点登录</a-radio>
				</a-radio-group>
			</a-form-item>
			<a-form-item label="设备数据日志">
				<a-radio-group v-model="state.form.log">
					<a-radio :value="1">记录</a-radio>
					<a-radio :value="0">不记录</a-radio>
				</a-radio-group>
			</a-form-item>
			<a-form-item label="数据过滤">
				<a-radio-group v-model="state.form.filter.is">
					<a-radio :value="1">启用</a-radio>
					<a-radio :value="0">不启用</a-radio>
				</a-radio-group>
			</a-form-item>
			<a-form-item label="过滤方式" v-if="state.form.filter.is ===1">
				<a-checkbox-group v-model="state.form.filter.type">
				    <a-checkbox :value="1">字节长度</a-checkbox>
				    <a-checkbox :value="2">前N位字符</a-checkbox>
				    <a-checkbox :value="3">忽略心跳包</a-checkbox>
				</a-checkbox-group>
			</a-form-item>
			<a-form-item label="字节长度" v-if="state.form.filter.type.includes(1)">
				<a-input-group style="width: 100%;">
				    <a-select placeholder="字节长度" allow-clear class="w160" v-model="state.form.filter.lengType">
				    	<a-option value="＞"> ＞ </a-option>
				    	<a-option value="＜"> ＜ </a-option>
				    	<a-option value="＝"> ＝ </a-option>
				    	<a-option value="≥"> ≥ </a-option>
				    	<a-option value="≤"> ≤ </a-option>
				    </a-select>
				    <a-input style="flex: 1;"  placeholder="请输入" v-model="state.form.filter.length"/>
				</a-input-group>
			</a-form-item>
			<a-form-item label="前N位字符" v-if="state.form.filter.type.includes(2)">
				<div class="flex-c">
					前<a-input placeholder="请输入" class="w160 ml10 mr10" v-model="state.form.filter.before"></a-input>
					<div class="w100 ml10">位为</div>
					<a-input placeholder="多个字段逗号分隔" v-model="state.form.filter.beforeVal"></a-input>
				</div>
			</a-form-item>
			<a-form-item label="心跳包" v-if="state.form.filter.type.includes(3)">
				<a-input placeholder="请输入" v-model="state.form.filter.heartVal"></a-input>
			</a-form-item>
			
			<a-form-item label="数据转发">
				<a-textarea v-model="state.form.forward" placeholder="转发设备的注册包，多个包用英文逗号分隔" style="height: 60px;"></a-textarea>
			</a-form-item>
			<a-form-item label="Http-Client">
				<a-textarea v-model="state.form.http" placeholder="转发URL地址，多个地址用英文逗号分隔" style="height: 60px;"></a-textarea>
			</a-form-item>
			<a-form-item label="自定义回复包">
				<div style="width: 100%;">
					<a-textarea  v-model="state.form.recode" placeholder="请输入自定义回复包" style="height: 60px;width: 100%;"></a-textarea>
					<a-radio-group type="button"  v-model="state.form.rtype">
						<a-radio :value="0">ASCII</a-radio>
						<a-radio :value="1">HEX</a-radio>
					</a-radio-group>
				</div>
			</a-form-item>
		</a-form>
	</a-modal>
</div>
</template>

<script setup>
import { reactive } from 'vue';
import { saveGatewayData } from '@/api/kdGateway'
import { Message } from '@arco-design/web-vue';
const emits = defineEmits(['success'])
const state = reactive({
	show:false,
	form:{
		code:"",			//注册包
		val:"",				//数据字段
		vtype:0,			//字段类型0：ASCII 1：HEX
		login:0,			//登录类型 0:单点登录,1:多点登录
		log:0,				//设备数据日志：0：不启用 1：启用
		forward:"",			//数据转发
		http:"",			//Http-Client
		recode:"",			//自定义回复包
		rtype:0,			//字段类型0：ASCII 1：HEX
		filter:{
			is:0,			//0：不启用 1启用
			type:[],		//过滤方式
			lengType:null,
			length:"",
			before:"",
			beforeVal:"",
			heartVal:""
		},
	}
})

function show(data){
	state.show = true
	state.form = {...data }
	if( !data.filter ){
		state.form.filter = {
			is:0,			//0：不启用 1启用
			type:[],		//过滤方式
			lengType:null,
			length:"",
			before:"",
			beforeVal:"",
			heartVal:""
		}
	}
}
//保存数据
async function saveData(){
	let form = JSON.parse(JSON.stringify(state.form))
	let res = await saveGatewayData(form)
	if( res.code === 200 ){
		Message.success("保存成功")
		state.show = false
		emits("success")
		return true;
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