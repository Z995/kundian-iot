<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/material/warehouseList.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-11 10:01:13  -->
<template>
    <kd-page-box>
        <div class="kd-content">
            <a-card :bordered="false">
                <template #title>
                    <div class="flex-c">
                        <span>仓库管理</span>
                    </div>
                </template>
                <template #extra>
                    <a-button type="primary" @click="showEdit(null)"><i class="ri-add-line"></i>添加仓库</a-button>
                </template>
            </a-card>
            <a-table :pagination="false" row-key="id" :bordered="false" :data="state.info.list" :columns="[
                {title:'ID',dataIndex:'id',width:120},
                {title:'名称',dataIndex:'name'},
                {title:'创建时间',dataIndex:'create_time'},
                {title:'操作',slotName:'action',width:260},
            ]" :loading="state.loading">
                <template #action="{record}">
                    <a-button type="text" size="mini" class="ml-10" @click="showGoods(record.id)">作物</a-button>
                    <a-button type="text" size="mini" class="ml-10" @click="showTool(record.id)">物料</a-button>
                    <a-button type="text" size="mini" class="ml-10" @click="showEdit(record)">编辑</a-button>
                    <a-popconfirm content="确认删除该仓库吗？" @ok="delData(record.id)">
                        <a-button type="text" size="mini">删除</a-button>
                    </a-popconfirm>
                </template>
            </a-table>
            <kd-pager :pageData="state.info" :event="getList"></kd-pager>
        </div>
        <cqkd-warehouse-add ref="warehouseRef"></cqkd-warehouse-add>
		<cqkd-warehouse-goods-list ref="goodsListRef"></cqkd-warehouse-goods-list>
		<cqkd-warehouse-material-list ref="materialListRef"></cqkd-warehouse-material-list>
    </kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import CqkdWarehouseAdd from './components/CqkdWarehouseAdd.vue';
import CqkdWarehouseGoodsList from './components/CqkdWarehouseGoodsList.vue'
import CqkdWarehouseMaterialList from './components/CqkdWarehouseMaterialList.vue'
const warehouseRef = ref()
const goodsListRef = ref()
const materialListRef = ref()
const state = reactive({
	loading:false,
	info:{
		page:1,
		limit:10,
		count:0,
		list:[],
	}
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
	state.info.list = [
		{id:1,name:'仓库1',status:1,create_time:'2025/10/10 12:00',rank:10},
		{id:2,name:'仓库2',status:0,create_time:'2025/10/10 12:00',rank:10},
		{id:3,name:'仓库3',status:1,create_time:'2025/10/10 12:00',rank:10},
	]
}
//新增编辑
function showEdit(data){
	warehouseRef.value.show(data)
}

//删除数据
function delData(id){
	
}

const showGoods = (id)=>goodsListRef.value.show()
const showTool = (id)=>materialListRef.value.show()

</script>

<style>
</style>