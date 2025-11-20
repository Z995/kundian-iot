<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/login/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-28 15:27:43  -->
<template>
<div class="kd-login">
	<div class="login-form">
		<div class="lf-title f24">欢迎登录</div>
		<div class="login-type flex-c">
			<div class="type-item" :class="{active:state.loginType ==='account'}" @click="state.loginType = 'account'">账号密码登录</div>
			<div class="type-item" :class="{active:state.loginType ==='code'}" @click="state.loginType = 'code'">短信登录</div>
		</div>
		<a-form :model="state.form" @submit="toLogin">
			<!-- 账号密码登录 -->
			<div v-if="state.loginType ==='account'">
				<div class="form-input">
					<i class="ri-user-3-line f22"></i>
					<a-input class="input-view" v-model="state.form.phone" placeholder="请输入登录账号"></a-input>
				</div>
				<div class="form-input mt20">
					<i class="ri-lock-2-line f22"></i>
					<a-input class="input-view" v-model="state.form.password" placeholder="请输入登录密码" type="password"></a-input>
				</div>
			</div>
			<!-- 验证码登录 -->
			<div v-if="state.loginType ==='code'">
				<div class="form-input">
					<i class="ri-user-3-line f22"></i>
					<a-input class="input-view" placeholder="请输入登录手机号"></a-input>
				</div>
				<div class="code-box flex-c mt20">
					<div class="form-input" style="flex: 1;">
						<i class="ri-lock-2-line f22"></i>
						<a-input class="input-view" placeholder="请输入验证码"></a-input>
					</div>
					<a-button class="code-btn">获取验证码</a-button>
				</div>
			</div>
			<a-button type="primary" class="login-btn"  html-type="submit" :loading="state.loading">立即登录</a-button>
			<div class="forget mt10">忘记密码?</div>
		</a-form>
	</div>
</div>
</template>
<script setup>
import { reactive } from 'vue';
import router from '../../router';
import { accountLogin } from "@/api/kdLogin"
import { Message } from '@arco-design/web-vue';

const state = reactive({
	loginType:'account',	
	loading:false,
	form:{
		phone:"",
		password:""
	}
})

async function toLogin(){
	let form = JSON.parse(JSON.stringify(state.form))
	if( !form.phone ) {
		Message.warning("请输入登录账号")
		return false
	}
	if( !form.password ){
		Message.warning("请输入登录密码")
		return false
	}
	state.loading = true
	try {
		let res = await accountLogin(form)
		state.loading = false
		//登录成功
		if( res.code ===200 && res.data?.token){
			Message.success("登录成功")
			localStorage.setItem("_IOT_TOKEN_",res.data.token)	//存储登录token
			localStorage.setItem("_IOT_USER_",form.phone)		//存储当前登录账号
			localStorage.removeItem("_IOT_MENU_KEY_")
			router.push("/index")
			return false
		}
		Message.error(res.msg)
	} catch (error) {
		state.loading = false
	}
}
</script>

<style lang="scss" scoped>
.kd-login{
	width: 100%;
	height: 100%;
	position: fixed;
	left: 0;
	top: 0;
	background: url("../../assets/kd/logo-bg.jpg") no-repeat;
	background-size: cover;
	
	.login-form{
		position: absolute;
		right: 200px;
		top: 50%;
		transform: translateY(-50%);
		width: 400px;
		height: 500px;
		
		background: rgba( 255, 255, 255, 0.8 );
		box-shadow: 0 8px 32px 0 rgba( #a3c0c7, 0.37 );
		backdrop-filter: blur( 0px );
		-webkit-backdrop-filter: blur( 0px );
		border-radius: 20px;
		border: 1px solid rgba( 255, 255, 255, 0.18 );
		padding: 40px;
		.lf-title{
			letter-spacing: 4px;
			font-weight: bold;
		}
	}
	.login-type{
		width: 100%;
		gap:36px;
		margin-top: 30px;
		margin-bottom: 30px;
		.type-item{
			color: #999;
			font-size: 15px;
			letter-spacing: 2px;
			height: 26px;
			cursor: pointer;
		}
		.active{
			font-weight: bold;
			color: #000;
			position: relative;
			
			&::after{
				content: '';
				position: absolute;
				width: 100%;
				height: 2px;
				background: #165DFF;
				left: 0;
				bottom: 0;
			}
		}
	}
	
	.form-input{
		width: 100%;
		height: 50px;
		display: flex;
		align-items: center;
		background: #f2f3f5;
		border-radius: 10px;
		position: relative;
		.input-view{
			height: 50px;
			border-radius: 10px;
			padding-left: 50px;
		}
		i{
			position: absolute;
			left: 20px;
			z-index: 999;
			color: #86909c;
		}
	}
	.code-box{
		width: 100%;
		gap:20px;
	}
	.code-btn{
		width: 120px;
		height: 50px;
		border-radius: 10px;
	}
	.login-btn{
		width:100%;
		margin-top: 40px;
		height: 46px;
		border-radius: 10px;
		font-size: 16px;
		letter-spacing: 4px;
	}
	.forget{
		color: #165DFF;
		text-align: right;
		cursor: pointer;
	}
}
</style>