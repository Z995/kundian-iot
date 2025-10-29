<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/components/KdSelectMapLocation/KdMarkerAddress.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-04 14:58:21  -->
<template>
	<div class="marker-map" :id="props.id"></div>
</template>

<script setup>
import { onMounted, reactive, watch } from 'vue';
import { useTianDiMap } from '@/util/common/TianDiMap'
const { tianState,initMap ,setMapCenter,addMarker,removeMarker,getCurrentLocation } = useTianDiMap()
const props = defineProps({
	id:{
		type:String,
		default:"markerMapBox"
	},
	latitude:{
		type:[String,Number],
		default:""
	},
	longitude:{
		type:[String,Number],
		default:""
	}
})
const state = reactive({
	
})

watch(()=>props.longitude,()=>{
	if( parseFloat(props.latitude) && parseFloat(props.longitude )){
		addMarker(props.longitude,props.latitude,{draggable:false})
	}else{
		removeMarker()
	}
})

onMounted(()=>{
	initMap({
		el:props.id,
	},()=>{
		//初始化完成，设置中心点标记
		if( !props.longitude || !props.latitude ){
			getCurrentLocation()
		}
		let lng = props.longitude || 116.411794
		let lat = props.latitude || 39.9068
		addMarker(lng, lat,{draggable:false})
	})
})
</script>

<style lang="scss" scoped>
.marker-map{
	width: 100%;
	height: 100%;
}
</style>