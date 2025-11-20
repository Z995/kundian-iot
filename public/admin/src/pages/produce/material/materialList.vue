<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/material/materialList.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-11 10:12:23  -->
<template>
    <kd-page-box>
		<div class="kd-content">
			<a-form :model="state.search" layout="inline">
				<a-form-item label="物料类别">
					<a-select v-model="state.search.type" placeholder="请选择" allow-clear class="w200">
					    <a-option value="0">肥料</a-option>
					    <a-option value="1">农药</a-option>
					    <a-option value="2">种子</a-option>
					    <a-option value="3">兽药</a-option>
					    <a-option value="4">饲料</a-option>
					    <a-option value="5">其他</a-option>
					</a-select>
				</a-form-item>
				<a-form-item label="所在仓库">
					<a-select v-model="state.search.house" placeholder="请选择" allow-clear class="w200">
					    <a-option value="0">仓库A</a-option>
					    <a-option value="1">仓库B</a-option>
					</a-select>
				</a-form-item>
				<a-form-item label="物料名称">
					<a-input v-model="state.search.name" placeholder="物料名称" class="w300"></a-input>
					<a-button type="primary" class="ml10">搜索</a-button>
					<a-button class="ml10">重置</a-button>
				</a-form-item>
			</a-form>
		</div>
        <div class="kd-content mt10">
            <a-button type="primary" class="mb10" @click="showEdit(null)"><i class="ri-add-line"></i>添加物料</a-button>
            <a-table :pagination="false" row-key="id" :bordered="false" :data="state.info.list" :columns="[
                {title:'ID',dataIndex:'id',width:100},
                {title:'图片',slotName:'cover',width:100},
                {title:'名称',dataIndex:'name'},
                {title:'类别',dataIndex:'type'},
                {title:'规格',slotName:'attr'},
                {title:'所在仓库',slotName:'house'},
                {title:'创建时间',dataIndex:'create_time'},
                {title:'操作',slotName:'action',width:180},
            ]" :loading="state.loading">
                <template #cover="{record}">
                    <a-image class="ml-10" :rec="record.img" width="36"></a-image>
                </template>
                <template #data="{record}">
                    <div>{{ record.name }}</div>
                </template>
                <template #attr="{record}">
                    <div>{{ record.attr || '-' }}</div>
                </template>
                <template #house="{record}">
                    <div>仓库A</div>
                </template>
                <template #action="{record}">
                    <a-button type="text" size="mini" class="ml-10" @click="showEdit(record)">编辑</a-button>
                    <a-popconfirm content="确认删除该物料吗？" @ok="delData(record.id)">
                        <a-button type="text" size="mini">删除</a-button>
                    </a-popconfirm>
                </template>
            </a-table>
            <kd-pager :pageData="state.info" :event="getList"></kd-pager>
        </div>
        <cqkd-material-add ref="materialRef"></cqkd-material-add>
    </kd-page-box>
</template>

<script setup>
    import { onMounted, reactive, ref } from 'vue';
    import CqkdMaterialAdd from './components/CqkdMaterialAdd.vue';
    const materialRef = ref()
    const state = reactive({
        loading:false,
        info:{
            page:1,
            limit:10,
            count:0,
            list:[],
        },
		search:{
			type:null,
			house:null,
			name:'',
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
            {id:1,name:'21',type:'肥料',img:'',attr:'',create_time:'2025/10/10 12:00'},
            {id:2,name:'4442',type:'肥料',img:'',attr:'',create_time:'2025/10/10 12:00'},
            {id:3,name:'113',type:'肥料',img:'',attr:'',create_time:'2025/10/10 12:00'},
        ]
    }
    //新增编辑
    function showEdit(data){
        materialRef.value.show(data)
    }

    //删除数据
    function delData(id){
        
    }

</script>

<style>
</style>