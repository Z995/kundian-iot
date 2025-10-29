<template>
	<div v-if="gbControl.state.device">
		<div :style="{width:'100%',height:`${rem(240)}px`,position:'relative',background:'#000'}">
			<div :style="{
					width: '100%',
					height: `${rem(240)}px`,
					background: '#000',
					zIndex: '9',
				}" 
			v-if="gbControl.state.playType == 1">
				<cqkd-play-m3u8 id="localLiveM3u8Box" :src="gbControl.state.device.hls"></cqkd-play-m3u8>
			</div>
			<div :style="{
					width: '100%',
					height: `${rem(240)}px`,
					background: '#000',
					zIndex: '9',
				}"  v-if="gbControl.state.playType == 2">
				<cqkd-play-jessibuca id="localLiveFlvBox" :src="gbControl.state.device.flv"></cqkd-play-jessibuca>
			</div>
			<div :style="{
					width: '100%',
					height: `${rem(240)}px`,
					background: '#000',
					zIndex: '9',
				}"  v-if="gbControl.state.playType == 3">
				<div class="play-box">
					<a-spin :loading="state.loading" style="width: 100%;height: 100%;" tip="监控加载中...">
						<div class="play-box-div" id="playBoxdiv">
							<div class="pv-status" v-if="!state.loading && !state.flowStatus">离线</div>
							<div class="pv-status pv-error" :style="{fontSize:`${rem(16)}px`}" v-if="state.errorCode">
								{{ constantData.qiuNiuSdkError[state.errorCode]}}
							</div>
						</div>
					</a-spin>
				</div>
			</div>
		</div>
		<div class="device-option" :style="{height:`${rem(60)}px`}" v-if="gbControl.state.device.related_type!=='local'">
			<div class="option-item" :style="{height: `${rem(60)}px`,width: `${rem(100)}px`}" :class="{active:gbControl.state.active ==1}" @click="changeActive(1)">
				<div :style="{fontSize:`${rem(14)}px`}">云台控制</div>
			</div>
			<div class="option-item" :style="{height: `${rem(60)}px`,width: `${rem(100)}px`}" :class="{active:gbControl.state.active ==2}" @click="changeActive(2)" 
				v-if="gbControl.state.device.details.device?.type == 2"
			>
				<div :style="{fontSize:`${rem(14)}px`}">通道</div>
			</div>
		</div>
		<div v-if="gbControl.state.active ==1 && gbControl.state.playType !== null" class="flex-c" :style="{width: '94%',padding:`${rem(5)}px`,marginLeft: '3%',marginTop: `${rem(20)}px`,border: '1px solid #eee',}">
			<div class="mr20" 
				:style="{
					width: `${rem(110)}px`,
					height: `${rem(26)}px`,
					lineHeight: `${rem(26)}px`,
					textAlign: 'center',
					fontSize: `${rem(15)}px`,
					cursor: 'pointer',
					borderRadius:`${rem(10)}px`
				}"
			v-if="gbControl.state.device.hls" :class="gbControl.state.playType == 1?'active':''" @click="changePlayType(1)">M3U8</div>
			<div class="mr20"
				:style="{
					width: `${rem(110)}px`,
					height: `${rem(26)}px`,
					lineHeight: `${rem(26)}px`,
					textAlign: 'center',
					fontSize: `${rem(15)}px`,
					cursor: 'pointer',
					borderRadius:`${rem(10)}px`
				}"
			v-if="gbControl.state.device.flv" :class="gbControl.state.playType == 2?'active':''" @click="changePlayType(2)">FLV</div>
			<div class=""
				:style="{
					width: `${rem(110)}px`,
					height: `${rem(26)}px`,
					lineHeight: `${rem(26)}px`,
					textAlign: 'center',
					fontSize: `${rem(15)}px`,
					cursor: 'pointer',
					borderRadius:`${rem(10)}px`
				}"
			v-if="gbControl.state.device.webrtc" :class="gbControl.state.playType == 3?'active':''" @click="changePlayType(3)">WebRTC</div>
		</div>
		<template v-if="gbControl.state.device.related_type !== 'local'">
			<Kd-control v-if="gbControl.state.active ==1" @control="controlDevice"></Kd-control>
			<Kd-channel v-if="gbControl.state.active ==2" @change="startPullStearms()"></Kd-channel>
		</template>
	</div>
</template>
<script setup>
	import { onMounted,reactive } from 'vue';
	import { useRoute } from 'vue-router';
	import { getLiveDetail,startLivePullStearms,stopLivePullStearms,controlLiveDevice } from "@/api/kdLive"
	import { useGbControlState } from '@/store/gbControl'
	import CqkdPlayM3u8 from '@/components/live/CqkdPlayM3u8.vue';
	import CqkdPlayJessibuca from '@/components/live/CqkdPlayJessibuca.vue';
	import constantData from '@/util/common/constantData'
	import { Message } from '@arco-design/web-vue';
	import KdControl from './components/KdControl.vue';
	import { playGbLive } from '@/util/util'
	import KdChannel from './components/KdChannel.vue';
	import {rem} from '@/util/pxtovw'
	const option = useRoute().query
	const gbControl = useGbControlState()
	if(option && option.token) localStorage.setItem("_IOT_TOKEN_",option.token)
	const state = reactive({
		loading:false,
		flowStatus:false,	//是否在播放
		errorCode:null,		//播放错误代码
	})
	
	onMounted(() =>{
		gbControl.state.device_id = option.id
		getDeviceDetail()
	})

	async function getDeviceDetail() {
		let res = await getLiveDetail({id:option.id})
		gbControl.state.device = res.data
		if(res.data.related_type == 'local'){
			gbControl.state.user_device = res.data.hls
		}else{
			gbControl.state.user_device = res.data.details.device.webrtc
		}
		if( gbControl.state.device.hls ) gbControl.state.playType = 1
		if( gbControl.state.device.flv ) gbControl.state.playType = 2
		if( gbControl.state.device.webrtc || gbControl.state.details.device ) gbControl.state.playType = 3
		if(gbControl.state.playType == 3){
			startPullStearms()
		}
	}

	function changePlayType(type){
		stopPullStearms()
		gbControl.state.playType = type
		if(type == 3) startPullStearms()
	}

	//启动拉流
	function startPullStearms(){
		let { channel_id ,device,user_device ,channelList} = gbControl.state
		let data = {
			id:device.id,
		}
		if( channel_id ) data.channels = channel_id
		let webrtc = user_device
		if( device.details.device.type === 2 ){
			if( !monitorState.state.channel_id ){
				return Message.warning("请先选择通道")
			}
			channelList.forEach(item=>{
				if( item.gbId === channel_id ){
					webrtc = item.webrtc
				}
			})
		}
		state.loading = true
		startLivePullStearms(data).then(res=>{
			if( res.code !== 200){
				Message.error(res.msg)
				state.loading = false
				return ;
			}
			setTimeout(function(){
				//启动播放
				playGbLive({el:'playBoxdiv',webrtc:webrtc},(player)=>{
					state.loading = false
					gbControl.state.player = player
					state.flowStatus = true
					state.errorCode = null
				},(error)=>{
					state.loading = false
					state.errorCode = error.code
				})
			},1000)
		}).catch(()=>{
			state.loading = false
		})
	}

	//停止拉流
	function stopPullStearms(){
		if( !gbControl.state.player ) return false;
		//七牛播放器停止并释放
		releaseWebrtcPlay()
		
		let param = { id:monitorState.state.live_id }
		if( monitorState.state.channel_id ){
			param.channels = state.play_channel		//当前播放的通道
		}
		stopLivePullStearms(param).then(res=>{
			state.flowStatus = false
			if( res.code === 200 ){
				Message.success("已停止拉流")
				return;
			}
			Message.error(res.msg)
		})
	}

	function releaseWebrtcPlay(){
		if( gbControl.state.player ){
			gbControl.state.player.stop()
			gbControl.state.player.release()
			gbControl.state.player = null
			state.errorCode = null
		}
	}

	function changeActive(type){
		gbControl.state.active = type
	}
	// 设备方向控制
	function controlDevice(type){
		let param = { id:gbControl.state.device_id,cmd:type,speed:5}
		controlLiveDevice(param)
	}

</script>

<style lang="scss" scoped>
	.play-loading,.play-error{
		width: 100%;
		height: 220px;
		position: absolute;
		left: 0;
		top: 0;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.play-error{
		flex-direction: column;
		color: red;
	}
	.active{
		background: #2979ff;
		color: #fff;
	}

	.device-option{
		width: 100%;
		overflow: hidden;
		overflow-x: auto;
		display: flex;
		background: rgba(#333, .1);
		.option-item{
			display: flex;
			flex-direction: column;
			align-items: center;
			color: #333;
			flex-shrink: 0;
			justify-content: center;
		}
		.active{
			background: rgba(#333, .3);
		}
	}
	.play-box{
		width: 100%;
		height: 100%;
		background-color: #000;
		position: relative;
		.play-box-div{
			width: 100%;
			height: 100%;
			border: 2px solid green;
			.pv-status{
				width: 100%;
				height: 100%;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #fff;
			}
			.pv-error{
				color: red;
			}
		}
	}

</style>