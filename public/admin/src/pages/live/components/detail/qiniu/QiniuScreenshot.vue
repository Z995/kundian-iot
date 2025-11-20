<!-- 坤典物联 -七牛云截图管理 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/qiniu/QiniuScreenshot.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 14:27:01  -->
<template>
<div class="qn-screenshot">
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
		<a-button @click="showAutoConfig">配置自动截图</a-button>
	</a-space>
	<a-spin :loading="state.loading" tip="数据加载中..." style="width: 100%;">
		<div class="screenshot-list flex mt15">
			<template v-if="state.info.list.length">
				<div class="screent-item" v-for="(item,index) in state.info.list" :key="index">
					<a-image
						class="img"
					    width="220"
					    :src="item.snap"
					  />
					<div class="time flex-cb mt5">
						<span class="f12">{{formatTime(item.time*1000)}}</span>
						<a-popconfirm content="确认删除截图吗？" @ok="delData(item,index)">
							<a-button type="text" size="mini">删除</a-button>
						</a-popconfirm>
					</div>
				</div>
			</template>
			<kd-empty :height="300" v-else></kd-empty>
		</div>
		<div class="get-more mt10" v-if="state.info.length > state.info.limit || state.info.marker">
			<span v-if="state.info.marker" @click="getList()">加载更多</span>
			<span class="no-more" v-else>没有更多了</span>
		</div>
	</a-spin>
	
	
	<!-- 自动截图配置 -->
	<cqkd-set-auto-screenshot ref="autoRef"></cqkd-set-auto-screenshot>
</div>
</template>

<script setup>
import CqkdSetAutoScreenshot from '../CqkdSetAutoScreenshot.vue';
import { onMounted, reactive, ref } from 'vue';
import dayjs from 'dayjs'
import { useConfigStore } from '@/store/config'
import { useMonitorStore } from '@/store/monitor'
import { getLiveSnapshotsList,deleteLiveSnapshots } from '@/api/kdLive'
import { Message } from '@arco-design/web-vue';
import { formatTime } from '@/util/util'

const monitorState = useMonitorStore()
const configStore = useConfigStore()
const autoRef = ref()
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
		limit:20,
		marker:''
	}
})

onMounted(()=>{
	//根据屏幕决定查询数量
	state.info.limit = Math.floor((window.innerWidth-250)/230) *3
	//默认显示一小时
	state.search.time = [
		dayjs().subtract(1, 'day').format("YYYY-MM-DD HH:mm"),
		dayjs().format("YYYY-MM-DD HH:mm")
	]
})

function getList(page){
	let param = {
		line:state.info.limit,
		type:2,
		id:monitorState.state.live_id,
		start:new Date(state.search.time[0]).getTime()/1000,
		end:new Date(state.search.time[1]).getTime()/1000,
		marker:state.info.marker
	}
	//有通道的监控传入通道id
	if( monitorState.state.channel_id ){
		param.channels = monitorState.state.channel_id
	}
	state.loading = true
	getLiveSnapshotsList(param).then(res=>{
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


//删除截图
function delData(data,index){
	let param = {
		id:monitorState.state.live_id,
		file:data.file
	}
	if( monitorState.state.channel_id ){
		param.channels = monitorState.state.channel_id
	}
	deleteLiveSnapshots(param).then(res=>{
		if( res.code === 200 ){
			Message.success("删除成功")
			state.info.list.splice(index,1)
			return;
		}
		Message.error(res.msg)
	})
}

//自动截图配置
const showAutoConfig = ()=>autoRef.value.show(monitorState.state.live_id,monitorState.state.channel_id,1)



defineExpose({
	getList
})

</script>

<style lang="scss" scoped>
.screenshot-list{
	width: 100%;
	flex-wrap: wrap;
	gap:20px;
	min-height: 300px;
	.screent-item{
		width: 220px;
		height: 150px;
		border-radius: 6px;
		overflow: hidden;
		background: #f7f7f7;
		.img{
			width: 100%;
			height: 120px;
		}
		.time{
			padding: 0 6px;
			color: #777;
		}
	}
}
.channel-num{
	font-size: 12px;
	color: #999;
	margin-left: 5px;
	font-weight: 300;
}
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