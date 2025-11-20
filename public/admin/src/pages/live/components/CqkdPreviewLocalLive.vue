<!-- 坤典物联 - 普通监控预览 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/live/components/CqkdPreviewLocalLive.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-31 09:44:52  -->
<template>
<div v-if="state.show">
	<a-modal title="监控预览" v-model:visible="state.show" width="700px" :footer="null">
		<a-radio-group type="button" size="large" v-model="state.play_type" v-if="state.data.hls && state.data.flv">
			<a-radio value="flv">flv直播流</a-radio>
			<a-radio value="m3u8">m3u8直播流</a-radio>
		</a-radio-group>
		<div class="preview-box mt10" v-if="state.data && state.null_src ">
			<!-- m3u8播放 -->
			<cqkd-play-m3u8 v-if="state.play_type ==='m3u8'" id="localLiveM3u8Box" :src="state.data.hls"></cqkd-play-m3u8>
			
			<!-- flv直播流 -->
			<cqkd-play-jessibuca v-if="state.play_type ==='flv'" id="localLiveFlvBox" :src="state.data.flv"></cqkd-play-jessibuca>
		
			<!-- mp4播放 -->
			<div class="mp4-box" v-if="state.play_type ==='mp4'">
				<video style="width: 100%;height: 100%;" :src="state.data.mp4"></video>
			</div>
		</div>
		<div class="mt10 play-src" v-if="state.null_src">
			播放地址：
			<span v-if="state.play_type ==='m3u8'">{{state.data.hls}}</span>
			<span v-if="state.play_type ==='flv'">{{state.data.flv}}</span>
			<span v-if="state.play_type ==='mp4'">{{state.data.mp4}}</span>
		</div>
		<div class="none-data" v-if="!state.null_src">
			<kd-empty></kd-empty>
		</div>
	</a-modal>
</div>
</template>

<script setup>
import { reactive } from 'vue';
import CqkdPlayM3u8 from '@/components/live/CqkdPlayM3u8.vue';
import CqkdPlayJessibuca from '@/components/live/CqkdPlayJessibuca.vue';
const state = reactive({
	show:false,
	play_type:'m3u8',
	data:null,
	null_src:true,		//是否有播放地址
})

function show(data){
	state.show = true
	state.data = data
	if( data.flv ){
		state.play_type = 'flv'
		return;
	}
	if( data.hls ){
		state.play_type ='m3u8'
		return;
	}
	if( data.mp4 ){
		state.play_type = 'mp4'
		return;
	}
	state.null_src = false
}

defineExpose({
	show
})
</script>

<style lang="scss" scoped>
.preview-box{
	width: 100%;
	height: 370px;
}
.play-src{
	color: #999;
	font-weight: 300;
	word-break: break-all;
}
</style>