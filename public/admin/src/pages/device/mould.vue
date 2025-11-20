<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/mould.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-28 17:13:02  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="设备模板" :bordered="false">
			<template #extra>
				<a-space>
					<router-link :to="{path:'/device/mouldAdd'}">
						<a-button type="primary">
							<i class="ri-add-line"></i>新增模板
						</a-button>
					</router-link>
					<a-button :disabled="!state.selectId.length">批量删除</a-button>
				</a-space>
			</template>
			<a-form :model="state.search" layout="inline">
				<a-form-item label="模板名称">
					<a-input v-model="state.search.name" placeholder="请输入模板名称" class="w240"></a-input>
					<a-button type="primary" class="ml10" @click="getList(1,state.info.limit)">查询</a-button>
					<a-button class="ml10" @click="resetData">重置</a-button>
				</a-form-item>
			</a-form>
			<a-table 
				class="mt10 kd-small-table"
				row-key="id" 
				:pagination="false" 
				:bordered="false" 
				:data="state.info.list"
				:loading="state.loading"
				:columns="[
					{title:'ID',dataIndex:'id',width:90},
					{title:'模板名称',dataIndex:'name'},
					{title:'变量总数',dataIndex:'variable_count',width:100},
					{title:'关联设备数',slotName:'device_count',width:120},
					{title:'采集方式',slotName:'collect',width:100},
					{title:'更新时间',dataIndex:'update_time'},
					{title:'操作',slotName:'action',width:140,fixed:'right'},
				]"
				:row-selection="{
					type: 'checkbox',
					showCheckedAll: true,
					width:50
				}"
				v-model:selectedKeys="state.selectId"
				:scroll="{x:1000}"
			>
				<template #collect="{record}">
					<span>{{ record.collect === 1 ?'云端轮询':''}}</span>
				</template>
				<template #device_count="{record}">
					<span v-if="!record.device_count">0</span>
					<span v-else class="kd-link" @click="showDevice(record.device)">{{ record.device_count}}</span>
				</template>
				<template #action="{record}">
					<router-link :to="{path:'/device/mouldAdd',query:{id:record.id}}">
						<a-button type="text" size="mini" class="ml-10">编辑</a-button>
					</router-link>
					<a-popconfirm content="确认删除设备模板吗？" @ok="delData(record.id)">
						<a-button type="text" size="mini">删除</a-button>
					</a-popconfirm>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
	<cqkd-mould-link-device ref="linkRef"></cqkd-mould-link-device>
</kd-page-box>
</template>

<script setup>
import { onActivated, reactive, ref } from 'vue';
import CqkdDeviceSearch from './components/CqkdDeviceSearch.vue';
import router from '../../router';
import { getDeviceMouldList ,deleteDeviceMouldData } from '@/api/kdDevice'
import CqkdMouldLinkDevice from './components/CqkdMouldLinkDevice.vue';
import { Message} from '@arco-design/web-vue'
import { onBeforeRouteLeave } from 'vue-router';
import {useConfigStore}  from '@/store/config'
const configState = useConfigStore()

const linkRef = ref()
const state = reactive({
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
})
onActivated(()=>{
	getList(state.info.page,state.info.limit)
})

function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	let param = {
		page:state.info.page,
		limit:state.info.limit,
	}
	if( state.search.name ) param.name = state.search.name
	state.loading = true
	getDeviceMouldList(param).then(res=>{
		state.loading = false
		if( res.code === 200 ){
			state.info.list = res.data?.list || []
			state.info.count = res.data?.count || 0
		}
	}).catch(()=>{
		state.loading = false
	})
}

//删除模板
function delData(id){
	deleteDeviceMouldData({id}).then(res=>{
		if( res.code === 200 ){
			Message.success('删除成功')
			getList()
			return
		}
		Message.error(res.msg)
	})
}

//重置搜索
function resetData(){
	state.search.name = ''
	getList(1,state.info.limit)
}

//显示关联设备模板
const showDevice = (data)=> linkRef.value.show(data)

onBeforeRouteLeave((to)=>{
	configState.setKeepLive('DeviceMouldList',to.path ==='/device/mouldAdd')
})
defineOptions({
	name:"DeviceMouldList"
})
</script>

<style lang="scss" scoped>
.device-status{
	font-size: 12px;
	.dot{
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: var(--color);
		display: inline-block;
		margin-right: 5px;
	}
}
.online{
	color: #3bce95;
	--color:#3bce95;
}
.offline{
	color: #999;
	--color:#999;
}
.alarm{
	color: red;
	--color:red;
}
.noset{
	color: orange;
	--color:orange;
}
</style>