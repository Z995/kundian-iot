<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/log.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-24 10:37:56  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<kd-back title="网关日志"></kd-back>
		<div class="search-item flex-c mb10">
			<div>年份：</div>
			<a-radio-group type="button" size="small" v-model="state.search.year"
				@change="getDayOption"
			>
				<a-radio v-for="(item,index) in state.year" :value="item">{{item}}年</a-radio>
			</a-radio-group>
		</div>
		<div class="search-item flex-c mb10">
			<div>月份：</div>
			<a-radio-group type="button" size="small" v-model="state.search.month"
				@change="getDayOption"
			>
				<a-radio v-for="(item,index) in state.month" :key="index" :value="item.value">
					{{item.name}}
				</a-radio>
			</a-radio-group>
		</div>
		<div class="search-item flex-c">
			<div>日期：</div>
			<div class="search-item-day flex">
				<div class="day-item" v-for="(item,index) in state.day"
					:class="{active:item === state.search.day }"
					@click="changeDay(item)"
				>
					<span v-if="item ===0 ">全部</span>
					<span v-else>{{ item }}</span>
				</div>
			</div>
		</div>
	</div>
	<div class="kd-content mt10">
		<a-table class="kd-small-table" :pagination="false" :bordered="false" :data="state.info.list" 
			:columns="[
				{title:'ID',dataIndex:'id'},
				{title:'协议类型',slotName:'type'},
				{title:'数据内容',dataIndex:'val'},
				{title:'时间',dataIndex:'create_time'},
			]"
			:loading="state.loading"
		>
			<template #type="{record}">
				<!-- 0：TCP协议,1：websocket,2：MQTT  -->
				<span v-if="record.type ===0">TCP协议</span>
				<span v-if="record.type ===1">websocket</span>
				<span v-if="record.type ===2">MQTT</span>
			</template>
		</a-table>
		<kd-pager :page-data="state.info" :event="getList"></kd-pager>
	</div>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { getGatewayLog} from '@/api/kdGateway'
const option = useRoute().query
const state = reactive({
	loading:false,
	year:[],
	month:[
		{name:'一月',value:1},
		{name:'二月',value:2},
		{name:'三月',value:3},
		{name:'四月',value:4},
		{name:'五月',value:5},
		{name:'六月',value:6},
		{name:'七月',value:7},
		{name:'八月',value:8},
		{name:'九月',value:9},
		{name:'十月',value:10},
		{name:'十一月',value:11},
		{name:'十二月',value:12},
	],
	day:[],
	search:{
		gateway_id:0,
		year:'',
		month:'',
		day:0
	},
	info:{
		page:1,
		list:[],
		limit:10,
		count:0,
	}
})

onMounted(()=>{
	state.search.gateway_id = option.id
	getYearOption()
})

function getYearOption(){
	const date = new Date()
	let year = date.getFullYear()
	state.search.year = year
	state.year = [year-2,year-1,year,year+1]
	state.search.month = date.getMonth()+1
	getDayOption()
	getList(1,state.info.limit)
}

function getDayOption(){
	state.search.day = 0
	let day =new Date(state.search.day, state.search.month, 0).getDate()
	let arr = []
	for (var i = 0; i <= day; i++) {
		i===0 ? arr.push(i) :arr.push(day-i+1)
	}
	state.day = arr
	getList(1,state.info.limit)
}

function changeDay(day){
	state.search.day = day
	getList(1,state.info.limit)
}

function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	let { year,month,day,gateway_id } = state.search
	let param = {
		page:state.info.page,
		limit:state.info.limit,
		gateway_id,
		year,
		month:month <10 ?'0'+month :month,
	}
	if( day >0 ) param.day = day<10 ? '0'+day : day
	state.loading = true
	getGatewayLog(param).then(res=>{
		state.loading = false
		state.info.list = res.data.list
		state.info.count = res.data.count
	}).catch(()=>{
		state.loading = false
	})
}


</script>

<style lang="scss" scoped>
.search-item-day{
	background: #f7f7f7;
	padding:2px 6px;
	.day-item{
		cursor: pointer;
		padding: 3px 8px;
		font-size: 14px;
		color: rgb(78,89,105);
	}
	.active{
		color: #0066FF;
		background: #fff;
		border-radius: 2px;
	}
}
</style>