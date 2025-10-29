<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/components/detail/CqkdDeviceLive.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-08-22 15:17:58  -->
<template>
<div class="device-live">
	<kd-empty :height="400" title="暂无绑定的监控" v-if="!props.list.length"></kd-empty>
	<div class="live-box flex">
		<div class="live-item-box"  v-for="(item,index) in props.list" :key="index">
			<div class="live-item">
				<!-- 普通监控 -->
				<template v-if="item.related_type ==='local'">
					<!-- 无播放源 -->
					<div class="offline-status" v-if="!item.flv && !item.hls">暂无播放源</div>
					<div class="playing-show" v-else-if="state.player?.[item.id]?.play">
						<!-- flv直播流 -->
						<cqkd-play-jessibuca v-if="item.flv" :id="'localLiveFlvBox_'+item.id" :src="item.flv"></cqkd-play-jessibuca>
						<cqkd-play-m3u8 :autoplay="true" v-else :id="'localLiveM3u8Box_'+item.id" :src="item.hls"></cqkd-play-m3u8>
					</div>
					<!-- 可播放 -->
					<div class="play-icon" v-else @click="playLive(item)">
						<i class="ri-play-fill"></i>
					</div>
				</template>
				
				<!-- 坤典云监控 -->
				<template v-if="item.related_type ==='kun_dian'">
					<!-- 离线状态 -->
					<div class="offline-status" v-if="item.state!=='online'">离线</div>
					<!-- 播放加载中 -->
					<div class="play-ing" v-else-if="state.player?.[item.id]?.play && state.player?.[item.id]?.loading">
						<img class="loading-icon" src="/static/img/wxapp/player_loading.png" alt="">
						<div class="loading-txt">监控加载中...</div>
					</div>
					<div class="playing-show" v-else-if="!state.player?.[item.id]?.loading && state.player?.[item.id]?.src">
						<!-- flv直播流 -->
						<cqkd-play-jessibuca :id="'localLiveFlvBox_'+item.id" :src="state.player?.[item.id]?.src"></cqkd-play-jessibuca>
					</div>
					<div class="offline-status" v-else-if="!state.player?.[item.id]?.loading && state.player?.[item.id]?.error">
						{{ state.player?.[item.id]?.error }}
					</div>
					<!-- 可播放 -->
					<div class="play-icon" v-else @click="playLive(item)">
						<i class="ri-play-fill"></i>
					</div>
				</template>
			</div>
			<div class="name">
				<span v-if="item.related_type ==='local'">{{item.name}}</span>
				<span v-if="item.related_type ==='kun_dian'">{{item.desc || item.name}}</span>
			</div>
		</div>
	</div>
</div>
</template>

<script setup>
import { reactive } from 'vue';
import CqkdPlayM3u8 from '@/components/live/CqkdPlayM3u8.vue';
import CqkdPlayJessibuca from '@/components/live/CqkdPlayJessibuca.vue';
import { getLiveDetail ,getLiveChanngeList } from '@/api/kdLive'

const props = defineProps({
	list:{
		type:Array,
		default:[]
	}
})
const state = reactive({
	player:{},		//播放数据
})

//监控播放
async function playLive(data){
	if( !state.player[data.id] ) {
		if( data.related_type ==='local' ){
			state.player[data.id] = {
				play:true,
			}
		}
		if( data.related_type ==='kun_dian' ){
			state.player[data.id] = {
				play:true,
				loading:true,	//加载监控详情数据	
				live:null,		//监控数据
				error:'',		//错误数据
				src:"",			//播放地址
			}
			let res = await getLiveDetail({id:data.id})
			if( res.code === 200 ){
				state.player[data.id].live = res.data
				//平台设备，获取第一个通道数据
				if( res.data?.details?.device.type ===2 ){
					let channel_res = await getLiveChanngeList({id:data.id})
					if( channel_res.code === 200 && channel_res.data?.items.length ){
						state.player[data.id].src = channel_res.data?.items[0].flv
					}
				}
				if( !state.player[data.id].src ) {
					state.player[data.id].src = res.data?.details?.device.flv
				}
				state.player[data.id].loading = false
			}else{
				state.player[data.id].error = res.msg
				state.player[data.id].loading = false
			}
		}
	}
}



</script>

<style lang="scss" scoped>
.device-live{
	width: 100%;
	
	.live-box{
		width: 100%;
		flex-wrap: wrap;
		gap:4px;
		.live-item-box{
			width: 394px;
			height: 250px;
			margin-bottom: 10px;
		}
		.live-item{
			width: 394px;
			height: 220px;
			background: #000;
			cursor: pointer;
			position: relative;
			.offline-status{
				width: 100%;
				height: 100%;
				text-align: center;
				line-height: 225px;
				font-size: 16px;
				letter-spacing: 4px;
				color: red;
			}
			.play-icon{
				width: 100%;
				height: 100%;
				text-align: center;
				line-height: 225px;
				color: #fff;
				i{
					font-size: 70px;
				}
			}
			.play-ing{
				width: 100%;
				height: 100%;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				.loading-icon{
					width: 40px;
					height: 40px;
					animation: spin 2s ease-in-out infinite;
				}
				.loading-txt{
					color: #999;
					margin-top: 10px;
					font-size: 13px;
					letter-spacing: 2px;
				}
			}
			.playing-show{
				width: 100%;
				height: 100%;
			}
		}
		.name{
			width: 100%;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
			height: 30px;
			background: #f7f7f7;
			padding: 0 10px;
			line-height: 30px;
			color: #555;
		}
	}
}
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>