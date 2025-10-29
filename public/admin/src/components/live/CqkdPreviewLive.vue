<!-- 坤典物联 - 监控预览 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/components/live/CqkdPreviewLive.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-17 11:32:30  -->
<template>
<div class="preview-live-box" v-if="state.playType">
	<!-- m3u8播放 -->
	<cqkd-play-m3u8 v-if="state.playType ==='m3u8'" :autoplay="props.autoplay" :id="props.id" :src="props.src"></cqkd-play-m3u8>
	
	<!-- flv直播流 -->
	<cqkd-play-jessibuca v-if="state.playType ==='flv'" :id="props.id" :src="props.src"></cqkd-play-jessibuca>
</div>
</template>

<script setup>
import { onMounted, reactive, watch } from 'vue';
import CqkdPlayM3u8 from './CqkdPlayM3u8.vue';
import CqkdPlayJessibuca from './CqkdPlayJessibuca.vue';
const props = defineProps({
	//播放容器id
	id:{
		type:String,
		default:"livePlayBox"
	},
	//播放地址
	src:{
		type:String,
		default:"",
	},
	//自动播放，针对m3u8
	autoplay:{
		type:Boolean,
		default:false,
	}
})
const state = reactive({
	playType:"",
})

watch(()=>props.src,val=>{
	state.playType =''
	if( !val ){
		return ''
	}
	if( val.includes('.m3u8')){
		state.playType = 'm3u8'
	}
	if( val.includes('.flv')){
		state.playType = 'flv'
	}
},{deep:true,immediate:true})

</script>

<style lang="scss" scoped>
.preview-live-box{
	width: 100%;
	height: 100%;
}
</style>