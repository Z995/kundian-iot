<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/live/components/config/C'qCqkdTencent.vue -->
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
	<a-modal title="腾讯云配置" v-model:visible="state.show" width="600px" :on-before-ok="saveData">
		<a-form :model="state.form" :label-col-props="{flex:'100px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="SecretId">
				<a-input v-model="state.form.tencent_SecretId" placeholder="请输入腾讯云SecretId"></a-input>
			</a-form-item>
			<a-form-item label="SecretKey">
				<a-input v-model="state.form.tencent_SecretKey" placeholder="请输入腾讯云SecretKey "></a-input>
			</a-form-item>
		</a-form>
	</a-modal>
</div>
</template>

<script setup>
import { reactive, ref } from 'vue';
const state = reactive({
	show:false,
	form:{
		tencent_SecretId:"",
		tencent_SecretKey:"",
	}
})

function show(){
	state.show = true
}

async function saveData(){
	
}
defineExpose({
	show
})
</script>

<style>
</style>