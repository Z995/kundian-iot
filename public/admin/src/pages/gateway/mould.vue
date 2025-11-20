<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/mould.vue  -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-30 10:42:00  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="网关型号" :bordered="false">
			<template #extra>
				<a-space>
					<a-button type="primary" @click="showAdd(null)">
						<i class="ri-add-line"></i>新增型号
					</a-button>
					<a-popconfirm content="确认删除网关型号吗?" @ok="delData(state.selectId)">
					    <a-button :disabled="!state.selectId.length">批量删除</a-button>
					</a-popconfirm>
				</a-space>
			</template>
			<a-form :model="state.search" layout="inline">
				<a-form-item label="型号名称">
					<a-input placeholder="请输入型号名称" v-model="state.search.name" class="w300"></a-input>
					<a-button type="primary" class="ml10" @click="getList(1,state.info.limit)">查询</a-button>
					<a-button class="ml10" @click="resetData">重置</a-button>
				</a-form-item>
			</a-form>
			<!-- 列表 -->
			<a-table 
				class="mt10 kd-small-table"
				row-key="id" 
				:pagination="false" 
				:bordered="false" 
				:data="state.info.list"
				:loading="state.loading"
				:columns="[
					{title:'ID',dataIndex:'id'},
					{title:'网关型号',dataIndex:'name'},
					{title:'更新时间',dataIndex:'update_time'},
					{title:'操作',slotName:'action'},
				]"
				:row-selection="{
					type: 'checkbox',
					showCheckedAll: true,
					width:50
				}"
				v-model:selectedKeys="state.selectId"
			>
				<template #action="{record}">
					<a-button type="text" size="mini" @click="showAdd(record.id)">编辑</a-button>
					<a-popconfirm content="确认删除网关型号吗?" @ok="delData(record.id)">
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
import { onMounted, reactive, ref } from 'vue';
import router from '../../router';
import { getGatewayMouldList,deleteGatewayMouldData } from '@/api/kdGateway'
import { Message } from '@arco-design/web-vue';
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
onMounted(()=>{
	getList(1,10)
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
	getGatewayMouldList(param).then(res=>{
		state.loading = false
		if( res.code === 200 ){
			state.info.list = res.data?.list || []
			state.info.count = res.data?.count || 0
		}
	}).catch(()=>{
		state.loading = false
	})
}

//新增，编辑网关型号
function showAdd(id){
	if( id ){
		router.push({path:'/gateway/mouldAdd',query:{id}})
		return
	}
	router.push({path:'/gateway/mouldAdd'})
}

function resetData(){
	state.search.name = ''
	getList(1,state.info.limit)
}
//删除网关型号
function delData(id){
	deleteGatewayMouldData({id}).then(res=>{
		if( res.code === 200 ){
			Message.success('删除成功')
			getList()
			return
		}
		Message.error(res.msg)
	})
}
</script>

<style lang="scss" scoped>

</style>