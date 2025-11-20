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
		<a-range-picker
		    showTime
			class="w360"
			shortcuts-position="left"
			format="YYYY-MM-DD HH:mm"
			v-model="state.search.time"
			:allow-clear="false"
			:shortcuts="state.rangeShortcuts"
		/>
		<a-button type="primary">查询</a-button>
		<a-button>重置</a-button>
		<a-button @click="showAutoConfig">配置自动截图</a-button>
	</a-space>
	
	<div class="screenshot-list flex mt15">
		<div class="screent-item" v-for="(item,index) in state.info.limit" :key="index">
			<a-image
				class="img"
			    width="220"
			    src="http://img.farmkd.com/snapshot/jpg/3nm4x0wdhtsas/31011500991320065283/ondemand/1752541717791.jpg?e=1752648066&token=d8BDvxRsw76vxrUHxZ5k1tPXva_TpO2h1BdrZU4z:49zBsQC5-rZqnSvZ5Dez0G7LH20"
			  />
			<div class="time flex-cb mt5">
				<span class="f12">2025-07-15 09:08:37</span>
				<a-button type="text" size="mini">删除</a-button>
			</div>
		</div>
	</div>
	<kd-pager :page-data="state.info" :show-size="false"></kd-pager>
	
	<!-- 自动截图配置 -->
	<cqkd-set-auto-screenshot ref="autoRef"></cqkd-set-auto-screenshot>
</div>
</template>

<script setup>
import CqkdSetAutoScreenshot from '../CqkdSetAutoScreenshot.vue';
import { onMounted, reactive, ref } from 'vue';
import dayjs from 'dayjs'
import { useConfigStore } from '@/store/config'
const configStore = useConfigStore()
const autoRef = ref()
const state = reactive({
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

//自动截图配置
const showAutoConfig = ()=>autoRef.value.show()
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
</style>