<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/gateway/list.vue  -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-28 17:10:39  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card :bordered="false">
			<template #title>
				<div class="flex-c">
					<span>网关列表</span>
					<!-- <div class="net-total">
						<span class="mr20">网关总数：<span class="num">100</span></span>
						<span class="mr20 online">● 在线：<span class="num">90</span></span>
						<span class="mr20 offline">● 离线：<span class="num">10</span></span>
					</div> -->
				</div>
			</template>
			<template #extra>
				<a-space>
					<a-button type="primary" @click="showAdd(null)">
						<i class="ri-add-line"></i>新增网关
					</a-button>
					<a-popconfirm content="确认删除所选网关吗？" @ok="delData(state.selectId)">
						<a-button :disabled="!state.selectId.length">批量删除</a-button>
					</a-popconfirm>
				</a-space>
			</template>
			<!-- 搜索 -->
			<cqkd-gateway-search @search="getSearchData"></cqkd-gateway-search>
			<!-- 列表 -->
			<a-table 
				class="mt10 kd-small-table"
				row-key="id" 
				:pagination="false" 
				:bordered="false" 
				:data="state.info.list"
				:loading="state.loading"
				:columns="[
					{title:'网关状态',slotName:'status'},
					{title:'网关名称',dataIndex:'name'},
					{title:'MAC',dataIndex:'mac'},
					{title:'注册包',dataIndex:'code'},
					{title:'网关型号',slotName:'marque'},
					{title:'协议类型',slotName:'type'},
					{title:'网络类型',slotName:'network'},
					{title:'关联设备数',slotName:'device_count'},
					{title:'操作',slotName:'action',width:230,fixed:'right'},
				]"
				:row-selection="{
					type: 'checkbox',
					showCheckedAll: true,
					width:50
				}"
				v-model:selectedKeys="state.selectId"
				:scroll="{x:1200}"
			>
				<template #marque="{record}">
					<span>{{ record.marque?.name || '-'}}</span>
				</template>
				<template #status="{record}">
					<div class="offline" v-if="record.status===-1">● 等待初始上线</div>
					<div class="offline" v-if="record.status===0">● 离线</div>
					<div class="online" v-if="record.status===1">● 在线</div>
					<div class="alarm" v-if="record.status===2">● 网关报警</div>
				</template>
				<template #type="{record}">
					<span v-if="record.type ===0">TCP协议</span>
					<span v-if="record.type ===1">websocket</span>
					<span v-if="record.type ===2">MQTT</span>
				</template>
				<template #network="{record}">
					<span v-if="record.network ===1">2g/3g/4g/5g</span>
					<span v-if="record.network ===2">wifi</span>
					<span v-if="record.network ===3">NB-loT</span>
					<span v-if="record.network ===4">以太网</span>
					<span v-if="record.network ===5">其他</span>
				</template>
				<template #device_count="{record}">
					<span class="kd-link" v-if="record.device_count" @click="showLinkDevice(record.device)">{{record.device_count}}</span>
					<span v-else>0</span>
				</template>
				<template #action="{record}">
					<router-link :to="{path:'/gateway/detail',query:{id:record.id}}">
						<a-button type="text" size="mini" class="ml-10">查看</a-button>
					</router-link>
					<a-button type="text" size="mini" @click="showAdd(record.id)">编辑</a-button>
					<a-popconfirm content="确认删除网关吗?" @ok="delData(record.id)">
					    <a-button type="text" size="mini">删除</a-button>
					</a-popconfirm>
					<a-dropdown>
						<a-button type="text" size="mini">更多</a-button>
						<template #content>
							<a-doption>
								<span @click="showConfig(record)">配置</span>
							</a-doption>
							<a-doption>重启网关</a-doption>
							<a-doption>
								<span @click="showFixedTimeSet(record)">定时下发</span>
							</a-doption>
							<a-doption>
								<router-link :to="{path:'/gateway/log',query:{id:record.id}}">
									<span style="color: #000;">网关日志</span>
								</router-link>
							</a-doption>
						</template>
					</a-dropdown>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
	<!-- 关联设备列表 -->
	<cqkd-gateway-link-device ref="linkDeviceRef"></cqkd-gateway-link-device>
	
	<!-- 添加网关 -->
	<cqkd-add-gateway ref="addRef" @success="getList(state.info.page,state.info.limit)"></cqkd-add-gateway>

	<!-- 网关配置 -->
	<cqkd-gateway-config ref="configRef" @success="getList"></cqkd-gateway-config>
	
	<!-- 定时下发 -->
	<cqkd-fixed-time-issued ref="timeRef" @success="getList"></cqkd-fixed-time-issued>
	
</kd-page-box>
</template>

<script setup>
import {onBeforeRouteLeave} from "vue-router";
import { onActivated, onMounted, reactive, ref } from 'vue';
import CqkdGatewaySearch from './components/CqkdGatewaySearch.vue';
import CqkdGatewayLinkDevice from './components/CqkdGatewayLinkDevice.vue';
import CqkdAddGateway from './components/CqkdAddGateway.vue';
import CqkdGatewayConfig from './components/CqkdGatewayConfig.vue';
import CqkdFixedTimeIssued from './components/CqkdFixedTimeIssued.vue';
import { getGatewayListData,deleteGatewayData } from '@/api/kdGateway'
import { Message } from '@arco-design/web-vue';
import router from '../../router';
import {useConfigStore}  from '@/store/config'
const configState = useConfigStore()

const linkDeviceRef = ref()
const addRef = ref()
const configRef = ref()
const timeRef = ref()
const state = reactive({
	loading:false,
	info:{
		page:1,
		limit:10,
		count:0,
		list:[],
	},
	selectId:[],
	search:null,	//搜索参数
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
	if( state.search ){
		if( state.search.gateway_status !==null){
			param.gateway_status = state.search.gateway_status
		}
		if( state.search.marque_id !==null) param.marque_id = state.search.marque_id
		if( state.search.name ) param.name = state.search.name
	}
	state.loading = true
	getGatewayListData(param).then(res=>{
		state.loading = false
		if( res.code === 200 ){
			state.info.list = res.data?.list || []
			state.info.count = res.data?.count || 0
		}
	}).catch(()=>{
		state.loading = false
	})
}

//显示关联设备
const showLinkDevice = (data)=> linkDeviceRef.value.show(data)

//获取搜索参数
function getSearchData(data){
	state.search = data
	getList(1,state.info.limit)
}

//新增，编辑网关
const showAdd=(id)=> addRef.value.show(id)

//配置
const showConfig = (data)=> configRef.value.show(data)

//定时下发
const showFixedTimeSet = (data)=>timeRef.value.show(data)

//查看网关详情
function toGatewayDetail(data){
	router.push({path:'/gateway/detail',query:{id:data.id}})
}
//删除网关
function delData(id){
	deleteGatewayData({id}).then(res=>{
		if( res.code === 200 ){
			Message.success('删除成功')
			getList()
			return
		}
		Message.error(res.msg)
	})
}

onBeforeRouteLeave((to,form)=>{
    configState.setKeepLive('GatewayList',to.path ==='/gateway/log')
})

defineOptions({
    name:"GatewayList"
})
</script>

<style lang="scss" scoped>
.net-total{
	margin-left: 30px;
	font-size:13px;
	padding-top: 4px;
	.num{
		font-weight: bold;
	}
}
.online{
	color: #3bce95;
}
.offline{
	color: #999;
}
.alarm{
	color: orange;
}
.danger{
	color: orangered;
}
.upgrade{
	color: #165dff;
}

</style>