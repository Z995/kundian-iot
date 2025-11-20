<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/list.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-07 15:48:02  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="监控列表" :bordered="false">
			<template #extra>
				<a-space>
					<a-button type="primary" @click="showAdd(null)">
						<i class="ri-add-line"></i>新增监控
					</a-button>
					<a-popconfirm content="确认删除监控吗?" @ok="delData(state.selectId)">
					</a-popconfirm>
				</a-space>
			</template>
			<a-form :model="state.search" layout="inline">
				<a-form-item label="监控名称">
					<a-input placeholder="请输入监控名称" v-model="state.search.name" class="w300"></a-input>
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
					{title:'ID',dataIndex:'id',width:80},
					{title:'状态',slotName:'status',width:120},
					{title:'监控信息',slotName:'name'},
					{title:'监控协议',slotName:'related_type'},
					{title:'操作',slotName:'action',width:230,fixed:'right'},
				]"
			>
				<template #status="{record}">
					<div v-if="record.related_type ==='local'">-</div>
					<div v-if="record.related_type ==='kun_dian'">
						<a-tag v-if="record.state ==='online'" color="#00CC66" size="mini">在线</a-tag>
						<a-tag v-if="record.state ==='offline'" color="gray" size="mini">离线</a-tag>
						<a-tag v-if="record.state ===''" color="gray" size="mini">未知</a-tag>
						<a-tag v-if="record.state ==='notReg'" color="#FF9933" size="mini">未注册</a-tag>
						<a-tag v-if="record.state ==='locked'" color="#FF3333" size="mini">锁定</a-tag>
					</div>
				</template>
				<template #related_type="{record}">
					<span>{{constantData.livePlatform[record.related_type]}}</span>
				</template>
				<template #name="{record}">
					<div v-if="record.related_type ==='local'">
						<div>{{record.name}}</div>
						<div  class="flex src-list">
							<div class="src-item" :class="{active:record.hls}">M3U8</div>
							<div class="src-item" :class="{active:record.flv}">FLV</div>
						</div>
					</div>
					<div v-else-if="record.related_type ==='kun_dian'">
						<div>{{record.desc || record.name }}</div>
					</div>
					<div v-else>
						<div>{{ record.name }}</div>
					</div>
				</template>
				<template #action="{record}">
					<a-button type="text" size="mini" v-if="record.related_type==='local'" 
						@click="previewLocalLive(record)">
							预览
					</a-button>
					<template v-else>
						<router-link :to="{path:'/live/detail',query:{type:record.related_type,id:record.id}}">
							<a-button type="text" size="mini">详情</a-button>
						</router-link>
					</template>
					<router-link :to="{path:'/live/addLive',query:{type:record.related_type,id:record.id}}"
						 v-if="record.related_type!=='kun_dian'"
					>
						<a-button type="text" size="mini">编辑</a-button>
					</router-link>
					<a-popconfirm content="确认删除监控吗?" @ok="delData(record.id)">
					    <a-button type="text" size="mini">删除</a-button>
					</a-popconfirm>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
	<cqkd-select-platform ref="platformRef"></cqkd-select-platform>
	<cqkd-preview-local-live ref="previewRef"></cqkd-preview-local-live>
</kd-page-box>
</template>

<script setup>
import { onActivated, reactive, ref } from 'vue';
import { getLiveList ,deleteLiveData } from '@/api/kdLive'
import CqkdSelectPlatform from './components/list/CqkdSelectPlatform.vue';
import CqkdPreviewLocalLive from './components/CqkdPreviewLocalLive.vue';
import constantData from '@/util/common/constantData';
import { Message } from '@arco-design/web-vue';
import { onBeforeRouteLeave } from 'vue-router';
import {useConfigStore}  from '@/store/config'

const configState = useConfigStore()
const platformRef = ref()
const previewRef = ref()
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
	getLiveList(param).then(res=>{
		state.loading = false
		if( res.code === 200 ){
			state.info.list = res.data?.list || []
			state.info.count = res.data?.count || 0
			if( import.meta.env.MODE === 'development' ){
				state.info.list = state.info.list.concat([
					{id:'01',name:'萤石云测试数据不是真的-静态的',related_type:3,address:'重庆市渝北区',create_time:'2025-07-07 12:00:00',info:{
						deviceSerial:"33010343992967895520"
					}},
					{id:'02',name:'乐橙测试数据不是真的-静态的',related_type:6,address:'重庆市渝北区',create_time:'2025-07-07 12:00:00',info:{
						Dsn:"31011500991320000028",status:'on'
					}},
				])
			}
		}
	}).catch(()=>{
		state.loading = false
	})
}

function delData(id){
	deleteLiveData({id}).then(res=>{
		if( res.code === 200 ){
			Message.success('删除成功')
			getList()
			return
		}
		Message.error(res.msg)
	})
}
//显示新增监控
function showAdd(){
	platformRef.value.showModal()
	return false;
}

//搜索重置
function resetData(){
	state.search.name = ''
	getList(1,state.info.limit)
}

//预览普通监控设备
const previewLocalLive = (data)=>previewRef.value.show(data)

onBeforeRouteLeave((to,form)=>{
    configState.setKeepLive('LiveList',['/live/detail','/live/addLive'].includes(to.path))
})
defineOptions({
	name:"LiveList"
})
</script>

<style lang="scss" scoped>
.src-list{
	margin-top: 5px;
	.src-item{
		display: inline-block;
		font-size: 10px;
		padding: 1px 5px;
		background: #e3e3e3;
		color: #999;
		margin-right: 5px;
		border-radius: 4px;
	}
	.active{
		background: #0066FF;
		color: #fff;
	}
}

</style>