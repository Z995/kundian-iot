<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/detail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-29 11:28:30  -->
<template>
<kd-page-box :loading="state.loading">
	<div class="kd-content">
		<a-tabs v-model:active-key="state.active" >
		    <a-tab-pane key="detail" title="网关详情">
				<div v-if="state.info">
					<!-- 网关基本信息 -->
					<cqkd-gateway-detail :info="state.info" @refresh="getInfo"></cqkd-gateway-detail>
					<!-- 网关接入信息 -->
					<cqkd-gateway-access-info :info="state.info"></cqkd-gateway-access-info>
				</div>
			</a-tab-pane>
		    <a-tab-pane key="net" title="网络调试">
				<div v-if="state.info">
					<cqkd-network-debug :deviceCode="state.info.code"></cqkd-network-debug>
				</div>
			</a-tab-pane>
		</a-tabs>
	</div>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import CqkdGatewayDetail from './components/detail/CqkdGatewayDetail.vue';
import CqkdGatewayAccessInfo from './components/detail/CqkdGatewayAccessInfo.vue';
import CqkdNetworkDebug from './components/detail/CqkdNetworkDebug.vue';
import { getGatewayDetailData } from '@/api/kdGateway'
import { useRoute } from 'vue-router';
const option = useRoute().query
const state = reactive({
	loading:false,
	active:"detail",
	info:null,
})

onMounted(()=>{
	getInfo()
})

//获取网关详情
function getInfo(){
	state.loading = true
	getGatewayDetailData({id:option.id}).then(res=>{
		state.loading = false
		state.info = res.data
	}).catch(()=>{
		state.loading = false
	})
}

</script>

<style lang="scss" scoped>
</style>