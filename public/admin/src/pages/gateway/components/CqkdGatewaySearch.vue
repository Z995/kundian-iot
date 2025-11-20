<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/components/CqkdGatewaySearch.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-29 09:53:15  -->
<template>
<a-form :model="state.form" layout="inline">
	<a-form-item label="网关状态">
		<a-select placeholder="全部" class="w140" allow-clear v-model="state.form.gateway_status">
			<a-option :value="1">在线</a-option>
			<a-option :value="0">离线</a-option>
			<a-option :value="2">网关报警</a-option>
			<a-option :value="-1">等待初始上线</a-option>
		</a-select>
	</a-form-item>
	<a-form-item label="网关型号">
		<a-select placeholder="全部" class="w140" allow-clear v-model="state.form.marque_id">
			<a-option v-for="(item,index) in state.mouldList" :key="index" :value="item.id">
				{{ item.name }}
			</a-option>
		</a-select>
	</a-form-item>
	<a-form-item label="网关名称">
		<a-input placeholder="请输入SN或网关名称" v-model="state.form.name" class="w200" allow-clear></a-input>
		<a-button type="primary" class="ml10" @click="searchData">查询</a-button>
		<a-button class="ml10" @click="resetData">重置</a-button>
	</a-form-item>
</a-form>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { getGatewayMouldList } from '@/api/kdGateway'
const emits = defineEmits(['search'])
const state = reactive({
	form:{
		gateway_status:null,
		marque_id:null,
		name:"",
	},
	mouldList:[],
})
onMounted(()=>{
	getGatewayMouldList({page:1,limit:100}).then(res=>{
		state.mouldList = res.data?.list || []
	})
})

function searchData(){
	emits('search',JSON.parse(JSON.stringify(state.form)))
}

//重置搜索
function resetData(){
	state.form = {
		gateway_status:null,
		marque_id:null,
		name:"",
	}
	searchData()
}

</script>

<style>
</style>