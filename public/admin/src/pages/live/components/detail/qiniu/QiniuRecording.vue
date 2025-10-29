<!-- 坤典物联-七牛云本地录制 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/qiniu/QiniuRecording.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 15:23:34  -->
<template>
<div class="hk-recording">
	<a-space>
		<a-select v-model="monitorState.state.channel_id" placeholder="通道id" class="w320"
			v-if="monitorState.state.detail?.type === 2"
		>
			<a-option v-for="(item,index) in monitorState.state.channelList" :key="index" :value="item.gbId">
				{{item.name}}<span class="channel-num">({{ item.gbId}})</span>
			</a-option>
		</a-select>
		<a-range-picker
		    showTime
			class="w360"
			shortcuts-position="left"
			format="YYYY-MM-DD HH:mm"
			v-model="state.search.time"
			:allow-clear="false"
			:shortcuts="state.rangeShortcuts"
		/>
		<a-button type="primary" @click="getList(1,state.info.limit)">查询</a-button>
		<a-button @click="showAutoConfig">配置自动录制</a-button>
	</a-space>
	<a-table class="mt15 kd-small-table" :pagination="false" :bordered="false" :data="state.info.list" 
		:columns="[
			{title:'视频封面',slotName:'snap'},
			{title:'视频格式',slotName:'format'},
			{title:'类型',slotName:'type'},
			{title:'录制起止时间',slotName:'time'},
			{title:'操作',slotName:'action'},
		]"
		:loading="state.loading"
	>
		<template #snap="{record}">
			<img style="width: 100px;height: 60px;" :src="record.snap" alt="">
		</template>
		<template #format="{record}">
			<span v-if="record.format ===1">M3U8</span>
			<span v-if="record.format ===2">FLV</span>
			<span v-if="record.format ===3">MP4</span>
		</template>
		<template #type="{record}">
			<span>{{record.type ==='realTime'?'实时流':'历史流'}}</span>
		</template>
		<template #time="{record}">
			<div>起：{{formatTime(record.start*1000)}}</div>
			<div>止：{{formatTime(record.end*1000)}}</div>
		</template>
		<template #action="{record,rowIndex}">
			<a-button type="text" size="mini" class="ml-10" @click="previewLive(record)">播放</a-button>
			<a-popconfirm content="确认删除视频吗？" @ok="delData(record,rowIndex)">
				<a-button type="text" size="mini" class="ml-10">删除</a-button>
			</a-popconfirm>
		</template>
	</a-table>
	<div class="get-more mt10">
		<span v-if="state.info.marker" @click="getList()">加载更多</span>
		<span class="no-more" v-else>没有更多了</span>
	</div>
	<cqkd-auto-recording ref="autoRef"></cqkd-auto-recording>
	
	<cqkd-preview-local-live ref="previewRef"></cqkd-preview-local-live>
</div>
</template>

<script setup>
import CqkdAutoRecording from './CqkdAutoRecording.vue';
import CqkdPreviewLocalLive from '@/pages/live/components/CqkdPreviewLocalLive.vue'
import { onMounted, reactive, ref } from 'vue';
import dayjs from 'dayjs'
import { getLiveRecordingList,deleteLiveRecordingData } from '@/api/kdLive'
import { useMonitorStore } from '@/store/monitor'
import { formatTime } from '@/util/util'
import { Message } from '@arco-design/web-vue';
const monitorState = useMonitorStore()
const autoRef = ref()
const previewRef = ref()
const state = reactive({
	loading:false,
	search:{
		time:[],
	},
	rangeShortcuts:[
		{label: '最近七日',value: () => [dayjs().subtract(7, 'day'),dayjs()]},
		{label: '最近一个月',value: () => [dayjs().subtract(1, 'month'),dayjs()]},
		{label: '最近三个月',value: () => [dayjs().subtract(3, 'month'),dayjs()]},
		{label: '最近一年',value: () => [dayjs().subtract(1, 'year'),dayjs()]},
	],
	info:{
		page:1,
		count:0,
		list:[],
		limit:10,
		marker:''
	}
})

onMounted(()=>{
	//默认显示一天
	state.search.time = [
		dayjs().subtract(1, 'day').format("YYYY-MM-DD HH:mm"),
		dayjs().format("YYYY-MM-DD HH:mm")
	]
})

function getList(page){
	let param = {
		id:monitorState.state.live_id,
		start_time:new Date(state.search.time[0]).getTime()/1000,
		end_time:new Date(state.search.time[1]).getTime()/1000,
		line:state.info.limit,
		marker:state.info.marker
	}
	//有通道的监控传入通道id
	if( monitorState.state.channel_id ){
		param.channels = monitorState.state.channel_id
	}
	state.loading = true
	getLiveRecordingList(param).then(res=>{
		state.loading = false
		if( res.code === 200 ){
			if( page ===1 ){
				state.info.list = res.data.items || []
			}else{
				state.info.list = state.info.list.concat(res.data.items || [])
			}
			state.info.marker = res.data.marker || ''
			return
		}
		Message.error(res.msg)
		
	}).catch((e)=>{
		Message.error(e.msg)
		state.loading = false
	})
}

//自动录制配置
const showAutoConfig = ()=>autoRef.value.show(monitorState.state.live_id,monitorState.state.channel_id)

//预览监控设备
const previewLive = (data)=>{
	previewRef.value.show({hls:data.url})
}

//删除视频
function delData(data,index){
	let param = {
		id:monitorState.state.live_id,
		file:data.file
	}
	if( monitorState.state.channel_id ){
		param.channels = monitorState.state.channel_id
	}
	deleteLiveRecordingData(param).then(res=>{
		if( res.code === 200 ){
			Message.success("删除成功")
			state.info.list.splice(index,1)
			return;
		}
		Message.error(res.msg)
	})
}

defineExpose({
	getList
})
</script>

<style lang="scss" scoped>
.get-more{
	width: 100%;
	text-align: center;
	color: #165DFF;
	cursor: pointer;
	.no-more{
		color: #999;
	}
}
</style>