<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/list.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-28 14:27:02  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="设备列表" :bordered="false">
			<template #extra>
				<a-space>
					<router-link :to="{path:'/device/deviceAdd'}">
						<a-button type="primary">
							<i class="ri-add-line"></i>新增设备
						</a-button>
					</router-link>
					<a-button :disabled="!state.selectId.length">批量删除</a-button>
				</a-space>
			</template>
			<cqkd-device-search @search="getSearchData"></cqkd-device-search>
			<a-table 
				class="mt10"
				row-key="id" 
				:pagination="false" 
				:bordered="false" 
				:data="state.info.list"
				:loading="state.loading"
				:columns="[
					{title:'设备状态',slotName:'status',width:100},
					{title:'设备名称',slotName:'name'},
					{title:'设备编号',slotName:'code'},
					{title:'网关',slotName:'gateway'},
					{title:'设备模板',slotName:'template'},
					{title:'创建时间',dataIndex:'create_time'},
					{title:'操作',slotName:'action',width:180,fixed:'right'},
				]"
				:row-selection="{
					type: 'checkbox',
					showCheckedAll: true,
					width:50
				}"
				v-model:selectedKeys="state.selectId"
				:scroll="{x:1200}"
			>
				<template #status="{record}">
					<div class="device-status offline" v-if="record.status===0"><span class="dot"></span>离线</div>
					<div class="device-status online" v-else-if="record.status ===1"><span class="dot"></span>在线</div>
					<div class="device-status alarm" v-else-if="record.is_warning==1"><span class="dot"></span>报警</div>
				</template>
				<template #name="{record}">
					<span class="f12">{{record.name}}</span>
				</template>
				<template #code="{record}">
					<span class="f12">{{record.code}}</span>
				</template>
				<template #gateway="{record}">
					<span class="f12">{{record.gateway?.name || ''}}</span>
				</template>
				<template #template="{record}">
					<span class="f12">{{record.template?.name || ''}}</span>
				</template>
				<template #action="{record}">
					<router-link :to="{path:'/device/deviceDetail',query:{id:record.id}}">
						<a-button type="text" size="mini" class="ml-10">查看</a-button>
					</router-link>
					<router-link :to="{path:'/device/deviceAdd',query:{id:record.id}}">
						<a-button type="text" size="mini">编辑</a-button>
					</router-link>
					<a-popconfirm content="确认删除设备吗？" v-if="record.status===0" @ok="delData(record.id)">
						<a-button type="text" size="mini">删除</a-button>
					</a-popconfirm>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
</kd-page-box>
</template>

<script setup>
import { onActivated, reactive } from 'vue';
import CqkdDeviceSearch from './components/CqkdDeviceSearch.vue';
import router from '../../router';
import { getDeviceList,deleteDevice } from '@/api/kdDevice'
import { Message} from '@arco-design/web-vue'
import { onBeforeRouteLeave } from 'vue-router';
import {useConfigStore}  from '@/store/config'
const configState = useConfigStore()
const state = reactive({
	loading:false,
	info:{
		page:1,
		limit:10,
		count:0,
		list:[],
	},
	search:null,
	selectId:[],
})
onActivated(()=>{
	getList(state.info.page,state.info.limit)
})
function getSearchData(data){
	state.search = data
	getList(1,10)
}

//获取设备列表数据
function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	let param = {
		page:state.info.page,
		limit:state.info.limit,
	}
	let search = state.search
	if( search ){
		if( search.name ) param.name = search.name
		if( search.device_status !==null ) param.device_status = search.device_status
	}
	state.loading = true
	getDeviceList(param).then(res=>{
		state.loading = false
		if( res.code === 200 ){
			state.info.list = res.data?.list || []
			state.info.count = res.data?.count || 0
		}
	}).catch(()=>{
		state.loading = false
	})
}

//删除设备
function delData(id){
	deleteDevice({id}).then(res=>{
		if( res.code === 200 ){
			Message.success('删除成功')
			getList()
			return
		}
		Message.error(res.msg)
	})
}
onBeforeRouteLeave((to,form)=>{
    configState.setKeepLive('DeviceList',['/device/deviceDetail','/device/deviceAdd'].includes(to.path))
})
defineOptions({
	name:"DeviceList"
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