<!-- 坤典物联 - 产品库 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/product.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-18 10:43:03  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="产品库" :bordered="false">
			<template #extra>
				<a-space>
					<router-link :to="{path:'/device/productAdd'}">
						<a-button type="primary">
							<i class="ri-add-line"></i>新增产品
						</a-button>
					</router-link>
				</a-space>
			</template>
			<a-form :model="state.search" layout="inline">
				<a-form-item label="产品名称">
					<a-input v-model="state.search.name" placeholder="请输入产品名称" class="w240"></a-input>
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
					{title:'产品名称',dataIndex:'name'},
					{title:'变量数',dataIndex:'link_data'},
					{title:'状态',slotName:'status'},
					{title:'创建时间',dataIndex:'create_time'},
					{title:'操作',slotName:'action',width:130,fixed:'right'},
				]"
			>
				<template #status="{record}">
					<a-switch v-model="record.status" :checked-value="1" :unchecked-value="0"
						checked-text="启用" unchecked-text="禁用"
						@change="changeStatus(record)"
					></a-switch>
				</template>
				<template #action="{record}">
					<router-link :to="{path:'/device/productAdd',query:{id:record.id}}">
						<a-button type="text" size="mini">编辑</a-button>
					</router-link>
					<a-popconfirm content="确认删除产品吗？" @ok="delData(record.id)">
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
import { onActivated, onMounted, reactive } from 'vue';
import { getProductList ,deleteProductData,updateProductStatus } from '@/api/kdDevice'
import { Message } from '@arco-design/web-vue';
import { onBeforeRouteLeave } from 'vue-router';
import {useConfigStore}  from '@/store/config'
const configState = useConfigStore()
const state = reactive({
	loading:false,
	search:{
		name:'',
	},
	info:{
		page:1,
		limit:10,
		count:0,
		list:[],
	}
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
	getProductList(param).then(res=>{
		state.loading = false
		state.info.list = res.data.list
		state.info.count = res.data.count
	}).catch(()=>{
		state.loading = false
	})
}

//删除产品
function delData(id){
	deleteProductData({id}).then(res=>{
		if( res.code === 200 ){
			Message.success("删除成功")
			getList()
			return
		}
		Message.error(res.msg)
	})
}

//改变状态
async function changeStatus(data){
	let res = await updateProductStatus({id:data.id,status:data.status})
}

//重置数据
function resetData(){
	state.search.name = ''
	getList(1,state.info.limit)
}
onBeforeRouteLeave((to)=>{
	configState.setKeepLive('DeviceProductList',to.path ==='/device/productAdd')
})

defineOptions({
	name:"DeviceProductList"
})
</script>

<style>
</style>