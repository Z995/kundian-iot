<!-- 坤典物联-七牛云云平台 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/qiniu/QiniuPtz.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 11:03:46  -->
<template>
<div class="qn-ptz">
	<div class="qn-header flex">
		<a-select v-if="monitorState.state.detail?.type ===2"
			v-model="monitorState.state.channel_id" placeholder="通道id" 
			class="w360"
			@change="changeChannel"
		>
			<a-option v-for="(item,index) in monitorState.state.channelList" 
				:key="index" :value="item.gbId"
			>
				{{item.name}}<span class="channel-num">({{ item.gbId}})</span>
			</a-option>
		</a-select>
		<a-space>
			<a-button type="primary" @click="doSnap">截取当前画面</a-button>
			<a-button type="primary" @click="startRecording" :loading="state.record.loading">
				<span v-if="!state.record.loading">开始录制</span>
				<span v-else>正在录制({{state.record.count}}s)...</span>
			</a-button>
			<a-button type="primary" @click="stoptRecording" :loading="state.record.saving" v-if="state.record.loading">
				<span v-if="!state.record.saving">结束录制</span>
				<span v-else>结束中...</span>
			</a-button>
		</a-space>
	</div>
	<div class="qn-ptz-box flex mt15">
		<div class="play-box">
			<div style="width: 100%;height: 100%;" v-if="state.play_type ===1">
				<a-spin :loading="state.loading" style="width: 100%;height: 100%;" tip="监控加载中...">
					<div class="play-box-view" id="playBoxView">
						<div class="pv-status" v-if="!state.loading && !state.flowStatus">离线</div>
						<div class="pv-status pv-error" v-if="state.errorCode">
							{{ constantData.qiuNiuSdkError[state.errorCode]}}
						</div>
					</div>
				</a-spin>
			</div>
			<template v-else-if="state.play_type ===3">
				<cqkd-preview-live :src="monitorState.state.hls" :autoplay="true"></cqkd-preview-live>
			</template>
			<template v-else>
				<cqkd-preview-live :src="monitorState.state.flv"></cqkd-preview-live>
			</template>
		</div>
		<div class="control-board-view">
			<a-alert type="warning">
				<div>
					PTZ操作使用WebRTC播放体验最佳，操作后预计延迟 5 秒左右可以看到操作后画面
				</div>
				<div>
					HLS和FLV播放PTZ操作延迟较高，不建议使用。
				</div>
			</a-alert>
			<div class="control-board flex">
				<div class="cb-item" v-for="(item,index) in state.controlBtn" :key="index"
					@mousedown="controlLive(item.at)"
					@mouseup="controlLive('stop')"
				>
					<i class="ri-icon" v-if="item.icon" :class="item.icon"></i>
					<span v-if="item.text">{{ item.text}}</span>
				</div>
			</div>
		</div>
		
	</div>
	<div class="qn-ptz-option">
		<a-radio-group v-model="state.play_type" type="button" @change="changePlayType">
			<a-radio :value="1">webrtc</a-radio>
			<a-radio :value="3">HLS</a-radio>
			<a-radio :value="2">FLV</a-radio>
		</a-radio-group>
		<a-button type="primary" :disabled="state.play_type ===2" 
			v-if="!state.flowStatus" class="ml10" 
			@click="startPullStearms" 
			:loading="state.loading"
		>
			启动拉流
		</a-button>
		<a-button type="primary" 
			:disabled="state.play_type ===2" 
			v-if="state.flowStatus" class="ml10" 
			@click="stopPullStearms"
		>
			停止拉流
		</a-button>
	</div>
	<!-- 预置位 -->
	<div class="flex" style="gap:35px;">
		<div class="play-url-box">
			<div class="f14 fb flex-c mb10">
				<span class="mr10">播放地址</span>
			</div>
			<div class="url-item">
				<div>HLS：</div>
				<div class="url-txt">
					{{monitorState.state.hls}} <kd-copy :value="monitorState.state.hls" class="ml10"></kd-copy>
				</div>
			</div>
			<div class="url-item">
				<div>FLV：</div>
				<div class="url-txt">
					{{monitorState.state.flv}} <kd-copy :value="monitorState.state.flv" class="ml10"></kd-copy>
				</div>
			</div>
			<div class="url-item">
				<div>WebRTC：</div>
				<div class="url-txt">
					{{monitorState.state.webrtc}} <kd-copy :value="monitorState.state.webrtc" class="ml10"></kd-copy>
				</div>
			</div>
		</div>
		<div class="preset-box mt15">
			<div class="f14 fb flex-c mb10">
				<span class="mr10">预置位</span>
				<a-button size="mini" @click="showAddPreset"><i class="ri-add-line"></i>新增预置位</a-button>
			</div>
			<a-table class="kd-small-table" :pagination="false" :bordered="false" :data="state.preset.list"
				:columns="[
					{title:'ID',dataIndex:'id',width:100},
					{title:'预置位名称',dataIndex:'name'},
					{title:'操作',slotName:'action'},
				]"
				:loading="state.preset.loading"
			>
				<template #action="{record}">
					<a-button type="text" size="mini" class="ml-10" @click="changePreset(record.id,'goto')">调用</a-button>
					<a-popconfirm content="确认删除预置位吗？">
						<a-button type="text" size="mini" @click="changePreset(record.id,'remove')">删除</a-button>
					</a-popconfirm>
				</template>
				<template #empty>
					<div class="preset-none">
						<span @click="getPreset">点击获取预置位</span>
					</div>
				</template>
			</a-table>
		</div>
	</div>
	
	<!-- 添加预置位 -->
	<cqkd-add-preset ref="presetRef" @success="getPreset"></cqkd-add-preset>
</div>
</template>

<script setup>
import CqkdAddPreset from '../CqkdAddPreset.vue';
import CqkdPreviewLive from '@/components/live/CqkdPreviewLive.vue';
import { computed, reactive, ref } from 'vue';
import { useMonitorStore } from '@/store/monitor'
import { Message } from '@arco-design/web-vue';
import { 
	startLivePullStearms,stopLivePullStearms,getLivePresetList,
	changeLivePresetData,controlLiveDevice,saveLiveSnapshots,
	startLiveRecording,stopLiveRecording
} from '@/api/kdLive'
import { playGbLive } from '@/util/util'
import constantData from '@/util/common/constantData'
import { onUnmounted } from 'vue';
const monitorState = useMonitorStore()
const presetRef = ref()
const state = reactive({
	loading:false,
	play_type:1,
	controlBtn:[
		{icon:'ri-arrow-left-up-fill',at:'leftup'},
		{icon:'ri-arrow-up-fill',at:'up'},
		{icon:'ri-arrow-right-up-fill',at:'rightup'},
		{icon:'ri-arrow-left-fill',at:'left'},
		{icon:'',at:-1},
		{icon:'ri-arrow-right-fill',at:'right'},
		{icon:'ri-arrow-left-down-fill',at:'leftdown'},
		{icon:'ri-arrow-down-fill',at:'down'},
		{icon:'ri-arrow-right-down-fill',at:'rightdown'},
		{icon:'ri-subtract-line',at:'zoomout'},
		{icon:'',at:-1,text:'变倍'},
		{icon:'ri-add-line',at:'zoomin'},
	],

	preset:{
		loading:false,
		list:[],		//预置位
	},		
	
	player:null,		//播放对象
	flowStatus:false,	//是否在播放
	errorCode:null,		//播放错误代码
	play_channel:'',	//播放的通道id
	
	record:{
		loading:false,	
		saving:false,	
		count:0,		//录制时间
		timer:0,
	}
})

//获取预置位
function getPreset(){
	let param = {
		id:monitorState.state.live_id
	}
	if( monitorState.state.detail.type ===2 ){
		param.channels = monitorState.state.channel_id
	}
	state.preset.loading = true
	getLivePresetList(param).then(res=>{
		state.preset.loading = false
		state.preset.list = res.data?.items || []
		if( !state.preset.list.length ){
			Message.warning("暂无预置位数据")
		}
	}).catch(()=>{
		state.preset.loading = false
	})
}

//调用，删除预置位
function changePreset(id,cmd){
	let param = {
		id:monitorState.state.live_id,
		cmd:cmd,
		presetId:id
	}
	if( monitorState.state.detail.type ===2 ){
		param.channels = monitorState.state.channel_id
	}
	
	changeLivePresetData(param).then(res=>{
		if( res.code !==200 ){
			Message.error(res.msg)
			return;
		}
		if( cmd ==='remove' ){
			Message.success("删除成功")
			getPreset()
		}
	})
}

//添加预置位
const showAddPreset = ()=>presetRef.value.show(monitorState.state.live_id,monitorState.state.channel_id)

//启动拉流
function startPullStearms(){
	if( state.player ) return false;
	let param = { id:monitorState.state.live_id }
	let webrtc = monitorState.state.webrtc
	//平台设备要传递通道信息
	if( monitorState.state.detail.type ===2 ){
		if( !monitorState.state.channel_id ){
			return Message.warning("请先选择通道")
		}
		param.channels = monitorState.state.channel_id		//通道id
		state.play_channel = param.channels					//重置当前播放通道
	}
	state.loading = true
	startLivePullStearms(param).then(res=>{
		if( res.code !== 200){
			Message.error(res.msg)
			state.loading = false
			return ;
		}
		setTimeout(function(){
			//启动播放
			playGbLive({el:'playBoxView',webrtc:webrtc},(player)=>{
				state.loading = false
				state.player = player
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
	if( !state.player ) return false;
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

//切换播放类型
function changePlayType(data){
	//webrtc播放特殊处理 , 已经拉过流的，直接播放
	if( data ===1 && state.flowStatus ){
		state.loading = true
		//重新播放
		setTimeout(function(){
			//启动播放
			let webrtc = monitorState.state.webrtc
			playGbLive({el:'playBoxView',webrtc:webrtc},(player)=>{
				state.loading = false
				state.player = player
				state.flowStatus = true
				state.errorCode = null
			},(error)=>{
				state.loading = false
				state.errorCode = error.code
			})
		},1000)
	}else{
		releaseWebrtcPlay()
	}
}

//切换通道
function changeChannel(){
	monitorState.getPlayUrl()
	if( state.flowStatus ){
		stopPullStearms()
	}
	
	//预置位置空
	state.preset.list = []
}

//webrtc播放释放
function releaseWebrtcPlay(){
	if( state.player ){
		state.player.stop()
		state.player.release()
		state.player = null
		state.errorCode = null
	}
}


//云台控制
function controlLive(cmd){
	let param = {
		id:monitorState.state.live_id,
		cmd:cmd,
		speed:5,
	}
	if( monitorState.state.detail.type ===2 ){
		param.channels = monitorState.state.channel_id
	}
	controlLiveDevice(param).then(res=>{})
}

//截取当前画面
function doSnap(){
	let param = {
		id:monitorState.state.live_id,
	}
	if( monitorState.state.detail.type ===2 ){
		param.channels = monitorState.state.channel_id
	}
	saveLiveSnapshots(param).then(res=>{
		if( res.code === 200){
			Message.success("截图成功")
			return;
		}
		Message.error(res.msg)
	})
}

//视频录制
function startRecording(){
	let param = {
		id:monitorState.state.live_id,
	}
	if( monitorState.state.detail.type ===2 ){
		param.channels = monitorState.state.channel_id
	}
	state.record.loading = true
	startLiveRecording(param).then(res=>{
		if( res.code === 200 ){
			//开始计时
			state.record.timer = setInterval(()=>{
				state.record.count ++
			},1000)
			return;
		}
		Message.error(res.msg)
	})
}

//停止录制
function stoptRecording(){
	let param = {
		id:monitorState.state.live_id,
	}
	if( monitorState.state.detail.type ===2 ){
		param.channels = monitorState.state.channel_id
	}
	state.record.saving = true
	stopLiveRecording(param).then(res=>{
		state.record.saving = false
		if( res.code === 200 ){
			Message.success("已结束录制")
			state.record.loading = false
			clearInterval(state.record.timer)
			state.record.timer = 0
			state.record.count = 0
			return;
		}
		Message.error(res.msg)
	}).catch(()=>{
		state.record.saving = false
	})
}

onUnmounted(()=>{
	//释放播放
	releaseWebrtcPlay()
})

defineExpose({
	changeChannel
})
</script>

<style lang="scss" scoped>
.channel-num{
	font-size: 12px;
	color: #999;
	margin-left: 5px;
	font-weight: 300;
}
.qn-ptz{
	width: 100%;
	.qn-header{
		width: 50%;
		justify-content: space-between;
	}
	.qn-ptz-box{
		width: 100%;
	}
	.play-box{
		width: 50%;
		aspect-ratio: 16 / 9;
		background-color: #000;
		position: relative;
		
		.play-box-view{
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
	.qn-ptz-option{
		width: 50%;
		margin-top: 10px;
		display: flex;
		justify-content: space-between;
	}
	.control-board-view{
		flex: 1;
		margin-left: 35px;
		padding-left: 20px;
	}
	.control-board{
		width: 19vw;
		flex-wrap: wrap;
		gap:1vw;
		align-content: flex-start;
		margin-top: 20px;
		.cb-item{
			width: 4.5vw;
			height: 4.5vw;
			border: 2px solid #ebebeb;
			text-align: center;
			line-height: 5vw;
			color: #636363;
			cursor: pointer;
			&:nth-child(5){
				border: none;
			}
			&:nth-child(11){
				border: none;
			}
			&:nth-child(14){
				border: none;
			}
			.ri-icon{
				font-size: 32px;
			}
		}
	}
	
	.play-url-box{
		width: 50%;
		padding: 10px;
		border: 2px dashed #f7f7f7;
		margin-top: 15px;
		.url-item{
			width: 100%;
			word-break: break-all;
			margin-bottom: 15px;
			.url-txt{
				font-size: 13px;
				color: #999;
				line-height: 20px;
			}
		}
	}
	.preset-box{
		flex:1;
		border: 2px dashed #f7f7f7;
		padding: 10px;
	}
}
.preset-none{
	width: 100%;
	height: 100px;
	text-align: center;
	line-height: 100px;
	span{
		cursor: pointer;
		color: rgb(22,93,255);
		letter-spacing: 2;
	}
}

</style>