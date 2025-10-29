<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/live/components/config/CqkdKundian.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-15 11:39:28  -->
<template>
<div v-if="state.show">
	<a-modal title="授权坤典云配置" v-model:visible="state.show" width="500px" :on-before-ok="saveData">
		<div class="auth-box">
			<div class="ab-tab flex">
				<div class="tab-item" :class="{active:state.type===1}" @click="state.type =1">账号密码授权</div>
				<div class="tab-item" :class="{active:state.type===2}" @click="state.type =2">验证码授权</div>
			</div>
		</div>
		<a-form :model="state.form" :label-col-props="{flex:'100px'}" :wrapper-col-props="{flex:1}">
			<div v-if="state.type===1">
				<a-form-item label="登录账号">
					<a-input v-model="state.form.mobile" placeholder="请输入坤典云账号"></a-input>
				</a-form-item>
				<a-form-item label="登录密码">
					<a-input v-model="state.form.password" type="password" placeholder="请输入坤典云密码"></a-input>
				</a-form-item>
			</div>
			<div v-if="state.type===2">
				<a-form-item label="登录账号">
					<a-input v-model="state.form.mobile" placeholder="请输入坤典云账号"></a-input>
					<a-button type="primary" class="ml20" :disabled="state.timer" @click="showSlideVerify">
						{{ !state.timer ? '发送验证码' : state.count + 's后重新发送'}}
					</a-button>
				</a-form-item>
				<a-form-item label="验证码">
					<a-input v-model="state.form.code" placeholder="请输入验证码"></a-input>
				</a-form-item>
			</div>
		</a-form>
	</a-modal>
	<cqkd-slide-verify ref="verifyRef" @success="sendCode"></cqkd-slide-verify>
	
</div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { getConfigData,authKdPlatform,sendLoginCode } from '@/api/kdConfig'
import { Message } from '@arco-design/web-vue';
import CqkdSlideVerify from '@/components/CqkdSlideVerify.vue';

const verifyRef = ref()
const state = reactive({
	show:false,
	type:1,		//1账号密码 2验证码
	form:{
		mobile:"",
		password:"",
		code:"",
	},
	timer:0,
	count:60,
})
function show(){
	state.show = true
	//获取授权信息
	getConfigData(['kun_dian_cloud_mobile','kun_dian_cloud_password']).then(res=>{
		if( res.data.kun_dian_cloud_mobile ){
			state.form.mobile = res.data.kun_dian_cloud_mobile
		}
		if( res.data.kun_dian_cloud_password ){
			state.form.password = res.data.kun_dian_cloud_password
		}
	})
}

//显示滑块验证
function showSlideVerify(){
	let {mobile } = state.form
	if( !mobile ) {
		Message.warning("请输入手机号")
		return;
	}
	if (!/^1[3456789]\d{9}$/.test(mobile)) {
		Message.warning("手机号格式不正确")
		return ;
	}
	verifyRef.value.show()
}

//发送验证码
function sendCode(verify){
	if( !verify ) return ;
	sendLoginCode({mobile:state.form.mobile}).then(res=>{
		if( res.code === 200 ){
			Message.success("短信已发送")
			state.timer = setInterval(()=>{
				/** 清除定时器 */
				if( state.count <=0 ){
					clearInterval(state.timer)
					state.timer = 0
					state.count = 0
				}else{
					state.count --
				}
			},1000)
		}else{
			Message.error(res.msg)
		}
	})
}

async function saveData(){
	let form = JSON.parse(JSON.stringify(state.form))
	let param = {
		mobile:form.mobile,
	}
	if( !form.mobile ){
		Message.warning("请输入手机号")
		return false;
	}
	//密码授权参数
	if( state.type ===1 ){
		if( !form.password ){
			Message.warning("请输入密码")
			return false;
		}
		param.password = form.password
	}
	//验证码授权参数
	if( state.type ===2 ){
		if( !form.code ){
			Message.warning("请输入验证码")
			return false;
		}
		param.code = form.code
	}
	
	let res = await authKdPlatform(param)
	if( res.code === 200 ){
		Message.success('授权成功')
		return true
	}
	Message.error(res.msg)
	return false;
	
}
defineExpose({
	show
})
</script>

<style lang="scss" scoped>
.auth-box{
	width: 100%;
	.ab-tab{
		width: 100%;
		border-bottom: 1px solid #f7f7f7;
		margin-bottom: 40px;
		.tab-item{
			width: 50%;
			height: 40px;
			text-align: center;
			letter-spacing: 2px;
			color: #999;
			font-size: 17px;
			position: relative;
			cursor: pointer;
		}
		.active{
			color: #0066FF;
			&::after{
				position: absolute;
				content: "";
				width: 70%;
				left: 15%;
				height: 4px;
				background: #0066FF;
				bottom: -2px;
				border-radius: 4px;
			}
		}
	}
}
</style>