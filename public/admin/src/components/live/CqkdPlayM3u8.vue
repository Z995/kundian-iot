<!-- 坤典物联 - m3u8监控播放 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   CqkdPlayM3u8 -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-17 11:37:21  -->
<template>
<div class="play-m3u8-box">
	<div class="play-container" :id="props.id"></div>
</div>
</template>

<script setup>
import { onMounted, reactive ,onUnmounted,watch} from 'vue';
import Player from 'xgplayer'
import HlsPlugin from 'xgplayer-hls'
import 'xgplayer/dist/index.min.css';
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
	autoplay:{
		type:Boolean,
		default:false,
	}
})
const state = reactive({
	player:null,
	errorCode:{
		
	}
})
watch(()=>props.src,val=>{
	if( !val ) return;
	if( state.player ){
		state.player.destroy() //销毁播放器
		state.player = null
	}
	setTimeout(()=>{
		initPlay()
	},1000)
},{deep:true})
onMounted(()=>{
	initPlay()
})
function initPlay(){
	let src = props.src
	//开发环境下端口必须为1240才能正常播放
	if( import.meta.env.MODE === 'development' ){
		src = src.replace('447','1240')
		src = src.replace('https','http')
	}
	state.player = new Player({
	    el:  document.querySelector('.play-container'),
	    isLive: false,
	    url: src, // hls 流地址
	    plugins: [HlsPlugin] ,// 第二步
		height: '100%',
		width: '100%',
		autoplay:props.autoplay,
		autoplayMuted:true,
	})
}

onUnmounted(() => {
	if( state.player ){
		state.player.destroy() //销毁播放器
		state.player = null
	}
})
</script>

<style lang="scss" scoped>
.play-m3u8-box{
	width: 100%;
	height: 100%;
	color: red;
	.play-container{
		width: 100%;
		height: 100%;
	}
}
</style>