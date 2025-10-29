<!-- 坤典物联-七牛云设备详情 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/live/components/detail/CqkdQiniuDetail -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 16:14:11  -->
<template>
<div>
	<a-tabs v-model:active-key="state.active" @change="changeTab">
		<a-tab-pane key="1" title="基本信息">
			<qiniu-base-info></qiniu-base-info>
		</a-tab-pane>
		<a-tab-pane key="2" title="通道列表" v-if="monitorState.state.detail?.type === 2">
			<qiniu-channel @preview="toPreview"></qiniu-channel>
		</a-tab-pane>
		<a-tab-pane key="3" title="云台控制">
			<qiniu-ptz ref="ptzRef"></qiniu-ptz>
		</a-tab-pane>
		<a-tab-pane key="4" title="截图管理">
			<qiniu-screenshot ref="screenshotRef"></qiniu-screenshot>
		</a-tab-pane>
		<a-tab-pane key="5" title="录制视频">
			<qiniu-recording ref="recordRef"></qiniu-recording>
		</a-tab-pane>
	</a-tabs>
</div>
</template>

<script setup>
import QiniuBaseInfo from './qiniu/QiniuBaseInfo.vue';
import QiniuChannel from './qiniu/QiniuChannel.vue';
import QiniuPtz from './qiniu/QiniuPtz.vue';
import QiniuScreenshot from './qiniu/QiniuScreenshot.vue';
import QiniuRecording from './qiniu/QiniuRecording.vue';
import { reactive, ref } from 'vue';
import { useMonitorStore } from '@/store/monitor'
const monitorState = useMonitorStore()
const screenshotRef = ref()
const recordRef = ref()
const ptzRef = ref()
const state = reactive({
	active:'1'
})

async function changeTab(active){
	if( active ==='2'){
		monitorState.getChannel()
	}
	if( active ==='4'){
		if( !monitorState.state.channelList.length && monitorState.state.detail?.type ===2 ){
			await monitorState.getChannel()
		}
		screenshotRef.value.getList(1)
	}
	if( active ==='5'){
		if( !monitorState.state.channelList.length && monitorState.state.detail?.type ===2 ){
			await monitorState.getChannel()
		}
		recordRef.value.getList(1)
	}
}

//通道预览
function toPreview(data){
	monitorState.state.channel_id = data
	state.active = '3'
	setTimeout(()=>{
		ptzRef.value.changeChannel()
	})
}
</script>

<style lang="scss" scoped>

</style>