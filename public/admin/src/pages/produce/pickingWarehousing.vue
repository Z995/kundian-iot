<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/pickingWarehousing.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-11 11:46:23  -->
<template>
    <kd-page-box>
		<div class="kd-content">
			<a-form :model="state.search" layout="inline">
				<a-form-item label="采摘人员">
					<a-input v-model="state.search.user" placeholder="采摘人员名称" class="w200"></a-input>
				</a-form-item>
				<a-form-item label="采摘时间">
					<a-range-picker format="YYYY-MM-DD" class="w300" />
				</a-form-item>
				<a-form-item label="作物名称">
					<a-input v-model="state.search.name" placeholder="采摘作物名称" class="w300"></a-input>
					<a-button type="primary" class="ml10">搜索</a-button>
					<a-button class="ml10">重置</a-button>
				</a-form-item>
			</a-form>
		</div>
		
        <div class="kd-content mt10">
            <a-button type="primary" class="mb10" @click="showEdit(null)"><i class="ri-add-line"></i>添加采摘</a-button>
            <a-table :pagination="false" row-key="id" :bordered="false" :data="state.info.list" :columns="[
                {title:'ID',dataIndex:'id',width:100},
                {title:'采摘人员',dataIndex:'name'},
                {title:'采摘作物',dataIndex:'cropName'},
                {title:'采摘数量',slotName:'pickingCount'},
                {title:'入库仓库',slotName:'house'},
                {title:'备注',dataIndex:'remark'},
                {title:'采摘时间',dataIndex:'create_time'},
                {title:'操作',slotName:'action',width:180},
            ]" :loading="state.loading">
                <template #pickingCount="{record}">
                    <div>{{ record.pickingCount }}kg</div>
                </template>
                <template #expirationDate="{record}">
                    <div>{{ record.expirationDate || '-' }}</div>
                </template>
                <template #house="{record}">
                    <div>仓库A</div>
                </template>
                <template #action="{record}">
                    <a-button type="text" size="mini" class="ml-10" @click="showEdit(record)">编辑</a-button>
                    <a-popconfirm content="确认删除该供应商吗？" @ok="delData(record.id)">
                        <a-button type="text" size="mini">删除</a-button>
                    </a-popconfirm>
                </template>
            </a-table>
            <kd-pager :pageData="state.info" :event="getList"></kd-pager>
        </div>
        <cqkd-picking-warehousing-add ref="addRef"></cqkd-picking-warehousing-add>
    </kd-page-box>
</template>

<script setup>
    import { onMounted, reactive, ref } from 'vue';
    import CqkdPickingWarehousingAdd from './components/CqkdPickingWarehousingAdd.vue';
    const addRef = ref()
    const state = reactive({
        loading:false,
        info:{
            page:1,
            limit:10,
            count:0,
            list:[],
        },
		search:{
			user:'',
			name:''
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
            {id:1,name:'张三',cropName:'苹果',pickingCount:'100',expirationDate:'12',create_time:'2025/10/10'},
            {id:2,name:'张三',cropName:'苹果',pickingCount:'100',expirationDate:'34',create_time:'2025/10/10'},
            {id:3,name:'张三',cropName:'苹果',pickingCount:'100',expirationDate:'25',create_time:'2025/10/10'},
        ]
    }
    //新增编辑
    function showEdit(data){
        addRef.value.show(data)
    }

    //删除数据
    function delData(id){
        
    }

</script>

<style>
</style>