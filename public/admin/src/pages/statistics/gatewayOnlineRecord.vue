<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/gatewayOnlineRecord.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 14:27:46  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="网关上下线" :bordered="false">
			<!-- 查询参数 -->
			<a-space>
				<a-select class="w160" placeholder="请选择网关." @search="searchGateway"
					v-model="state.search.gateway_id"
					:filter-option="false"
					allow-search
					allow-clear
					@dropdown-reach-bottom="getDevice(state.gateway.page+1)"
				>
					<a-option v-for="(item,index) in state.gateway.list" :value="item.id" :key="item.id">
						{{item.name}}
					</a-option>
				</a-select>
				<a-range-picker
					v-model="state.search.time"
				    class="w340"
				    show-time
				    format="YYYY-MM-DD HH:mm"
				/>
				<a-button type="primary" @click="getList(1,state.info.limit)">查询</a-button>
				<a-button @click="resetData">重置</a-button>
			</a-space>
			<a-table class="kd-small-table mt20" :pagination="false" row-key="id" :bordered="false" :data="state.info.list"
				:columns="[
					{title:'ID',dataIndex:'id'},
					{title:'网关名称',dataIndex:'name'},
					{title:'注册包',dataIndex:'code'},
					{title:'时间',dataIndex:'create_time'},
					{title:'状态',slotName:'status'},
					{title:'下线原因',slotName:'reason'},
				]"
				:loading="state.loading"
			>
				<template #status="{record}">
					<a-tag color="green" v-if="record.online===1">上线</a-tag>
					<a-tag color="orangered" v-else>下线</a-tag>
				</template>
				<template #reason="{record}">
					<span>{{record.reason || '-'}}</span>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive } from "vue";
import { getGatewayListData } from '@/api/kdGateway'
import { getGatewayOnlineLogList } from '@/api/kdStatistics'
const state = reactive({
	loading:false,
	search:{
		gateway_id:null,
		time:[],
	},
	info:{
		page:1,
		limit:10,
		count:0,
		list:[],
	},
	gateway:{
		list:[],
		page:1,
		key:'',
	},
	selectId:[],
})
onMounted(()=>{
	getGateway(1)
	getList(1,10)
})

function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit 
	let param = {
		page:state.info.page,
		limit:state.info.limit,
	}
	let { gateway_id,time } = state.search
	if( gateway_id ) param.gateway_id = gateway_id
	if( time && time.length){
		param.start_time = time[0]
		param.end_time = time[1]
	}
	state.loading = true
	getGatewayOnlineLogList(param).then(res=>{
		state.loading = false
		state.info.list = res.data.list || []
		state.info.count = res.data.count || 0
	}).catch(()=>{
		state.loading = false
	})
}


//获取设备列表
function searchGateway(key){
	state.gateway.key = key
	state.gateway.page = 1
	let param = {
		page:1,
		limit:30,
		name:key
	}
	getGatewayListData(param).then(res=>{
		state.gateway.list = res.data.list
	})
}

function getGateway(page){
	state.gateway.page = 1
	let param = {
		page:page,
		limit:30,
	}
	if( state.gateway.key ) param.name = state.gateway.key
	getGatewayListData(param).then(res=>{
		if( page ===1 ){
			state.gateway.list = res.data.list
		}else{
			state.gateway.list = state.gateway.list.concat(res.data.list)
		}
	})
}

//重置数据
function resetData(){
	state.search = {
		device_id:null,
		time:[],
	}
	getList(1,state.info.limit)
}
</script>

<style lang="scss" scoped>
</style>