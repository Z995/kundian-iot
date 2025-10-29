<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/CqkdUpdateDeviceName.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 14:15:52  -->
<template>
<div v-if="state.show">
	<a-modal title="修改设备名称" v-model:visible="state.show" width="400px" :on-before-ok="saveData">
		<a-input v-model="state.desc" placeholder="请输入设备名称"></a-input>
	</a-modal>
</div>
</template>

<script setup>
import { Message } from '@arco-design/web-vue';
import { reactive } from 'vue';
import { saveUpdateLiveData ,updateLiveData } from '@/api/kdLive'
const emits = defineEmits(['success'])
const state = reactive({
	show:false,
	desc:"",
	id:0
})

function show(data){
	state.show = true
	state.desc = data.desc || ''
	state.id = data.id || ''
}

async function saveData(){
	if( !state.desc ){
		Message.warning("请输入设备名称")
		return false;
	}
	//保存操作
	let param = {
		id:state.id,
		desc:state.desc,
		name:state.desc
	}
	let res = await saveUpdateLiveData(param)
	if( res.code === 200 ){
		Message.success('修改成功')
		emits('success')
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