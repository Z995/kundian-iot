<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/live/detail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-15 16:56:17  -->
<template>
<kd-page-box :loading="monitorState.state.loading">
	<div class="kd-content">
		<kd-back title="监控设备详情"></kd-back>
		<!-- 萤石云监控 -->
		<cqkd-hai-kang-detail v-if="option.type==='3'"></cqkd-hai-kang-detail>
		<!-- 七牛云 & 坤典云-->
		<cqkd-qiniu-detail v-if="option.type==='kun_dian'"></cqkd-qiniu-detail>
		<!-- 阿里云 -->
		<cqkd-aliyun-detail v-if="option.type ==='5'"></cqkd-aliyun-detail>
		<!-- 乐橙 & 大华 -->
		<cqkd-imou-detail v-if="option.type ==='6'"></cqkd-imou-detail>
	</div>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { useMonitorStore } from '@/store/monitor'
import { lngLatToAddress } from '@/util/util'
import { getNamespaceInfo } from "@/api/kdLive"

//组件
import CqkdHaiKangDetail from './components/detail/CqkdHaiKangDetail.vue';
import CqkdQiniuDetail from './components/detail/CqkdQiniuDetail.vue';
import CqkdAliyunDetail from './components/detail/CqkdAliyunDetail.vue';
import CqkdImouDetail from './components/detail/CqkdImouDetail.vue';
const monitorState = useMonitorStore()
const option = useRoute().query
const state = reactive({

})

onMounted(async()=>{
	if( option.type ==='kun_dian'){
		state.loading = true
		await monitorState.getInfo(option.id)
		if( monitorState.state.info?.longitude && monitorState.state.info?.latitude){
			let { longitude,latitude } = monitorState.state.info
			if( parseFloat(longitude) && parseFloat(latitude )){
				//根据经纬度解析详细地址
				lngLatToAddress(longitude,latitude).then(res=>{
					monitorState.state.info.address_format = res.data?.result?.formatted_address || ''
				})
			}
		}
		if( monitorState.state.detail?.type ===2 ){
			await monitorState.getChannel()
		}
		monitorState.getPlayUrl()
		
		//获取网关信息
		getNamespaceInfo().then(res=>{
			monitorState.state.gateway = res.data?.[0] || null
		})
	}
})

</script>

<style lang="scss" scoped>
</style>