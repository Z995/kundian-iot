<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/components/select/CqkdSelectLive.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-08-22 14:33:47  -->
<template>
<div v-if="state.show">
	<a-modal title="选择监控" v-model:visible="state.show" width="800px" :on-before-ok="saveData">
		<a-space>
			<a-input placeholder="请输入监控名称" v-model="state.search.name" class="w300"></a-input>
			<a-button type="primary" class="ml10" @click="getList(1,state.info.limit)">查询</a-button>
			<a-button class="ml10" @click="resetData">重置</a-button>
		</a-space>
		<div class="live-box">
			<a-table
				class="mt10 kd-small-table"
				row-key="id" 
				:pagination="false" 
				:bordered="false" 
				:data="state.info.list"
				:loading="state.loading"
				:columns="[
					{title:'ID',dataIndex:'id',width:80},
					{title:'监控名称',slotName:'name'},
					{title:'监控协议',slotName:'related_type'},
					{title:'操作',slotName:'action',width:100,fixed:'right'},
				]"
			>
				<template #related_type="{record}">
					<span>{{constantData.livePlatform[record.related_type]}}</span>
				</template>
				<template #name="{record}">
					<div v-if="record.related_type ==='local'">
						<div>{{record.name}}</div>
					</div>
					<div v-else-if="record.related_type ==='kun_dian'">
						<div>{{record.desc || record.name }}</div>
					</div>
					<div v-else>
						<div>{{ record.name }}</div>
					</div>
				</template>
				<template #action="{record}">
					<a-button :type="state.selectId.includes(record.id) ?'primary':'dashed'" 
						size="mini" 
						@click="selectLive(record)"
					>
						{{ state.selectId.includes(record.id) ?'已选择':'选择' }}
					</a-button>
				</template>
			</a-table>
		</div>
		<kd-pager :page-data="state.info" :event="getList"></kd-pager>
	</a-modal>
</div>
</template>

<script setup>
import { reactive } from 'vue';
import { getLiveList } from '@/api/kdLive'
import constantData from '@/util/common/constantData';
import { Message } from '@arco-design/web-vue';
const emits = defineEmits(['select'])
const state = reactive({
	show:false,
	loading:false,
	search:{
		name:""
	},
	info:{
		page:1,
		limit:10,
		count:0,
		list:[],
	},
	selectId:[],
	selectLive:[],
})
function show(){
	state.show = true
	state.selectId = []
	state.selectLive = []
	getList(1,10)
}

function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	let param = {
		page:state.info.page,
		limit:state.info.limit,
	}
	if( state.search.name ) param.name = state.search.name
	state.loading = true
	getLiveList(param).then(res=>{
		state.loading = false
		if( res.code === 200 ){
			state.info.list = res.data?.list || []
			state.info.count = res.data?.count || 0
		}
	}).catch(()=>{
		state.loading = false
	})
}

function resetData(){
	state.search.name = ''
	getList(1,state.info.limit)
}

//选择监控，取消选择监控
function selectLive(data){
	if( state.selectId.includes(data.id) ){
		let idx = state.selectId.findIndex(item=>item === data.id )
		state.selectId.splice(idx,1)
		let idx1 = state.selectLive.findIndex((item)=>item.id === data.id)
		state.selectLive.splice(idx1,1)
	}else{
		state.selectId.push(data.id)
		state.selectLive.push(JSON.parse(JSON.stringify(data)))
	}
}

//确认选择
function saveData(){
	console.log(state.selectId);
	console.log(state.selectLive);
	if( !state.selectId.length ){
		Message.warning("请选择监控")
		return false;
	}
	emits("select",{
		ids:state.selectId ,
		list:JSON.parse(JSON.stringify(state.selectLive)) 
	})
	return true
}
defineExpose({
	show
})
</script>

<style lang="scss" scoped>
.live-box{
	width: 100%;
	height: 480px;
}
</style>