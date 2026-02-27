<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/set.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-27 14:27:02  -->
<template>
	<page-box>
		<div class="kd-content">
			<div class="iot-token">
				<a-button type="primary" @click="getGainKey">获取密钥</a-button>
				<a-input class="w500 ml15" placeholder="密钥Token" disabled v-model="state.token">
					<template #append>
						<span class="kd-link" v-clipboard:copy="state.token" v-clipboard:success="copySuccess" @click="showModal">复制</span>
					</template>
				</a-input>
			</div>
			<div class="tips iot-tips">
				提示：密钥获取成功后，复制填写到智慧农场>设置>基本设置>第三方接口>物联网第三方请求
			</div>
		</div>
	</page-box>
</template>

<script setup>
	import { reactive,onMounted } from 'vue';
	import { Message } from '@arco-design/web-vue';
	import {getSecretKey} from '@/api/kdAccount'
	const state = reactive({
		token:''
	})
	
	onMounted(() =>{
		let key = localStorage.getItem("_THREE_TOKEM")
		if(key) state.token = key
	})
	
	function getGainKey(){
		getSecretKey().then(res =>{
			Message.success("物联网密钥获取成功")
			state.token = res.data.secret_key || ''
			localStorage.setItem("_THREE_TOKEM",res.data.secret_key)
		})
	}
	
	function copySuccess(){
		Message.success("复制成功")
	}
</script>

<style lang="scss" scoped>
	.iot-token{
		display: flex;
	}
	.iot-tips{
		margin-top: 15px;
		margin-left: 105px;
	}
</style>