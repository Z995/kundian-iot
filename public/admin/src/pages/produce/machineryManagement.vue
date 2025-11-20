<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/machineryManagement.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-10 17:31:53  -->
<template>
    <kd-page-box>
        <div class="kd-content">
            <a-card :bordered="false">
                <template #title>
                    <div class="flex-c">农机管理</div>
                </template>
                <template #extra>
                    <a-button type="primary" @click="showEdit(null)">
                        <i class="ri-add-line"></i>添加
                    </a-button>
                </template>
                <a-table :pagination="false" row-key="id" :bordered="false" :data="state.info.list" :columns="[
                    {title:'ID',dataIndex:'id',width:100},
                    {title:'图片',slotName:'cover',width:50},
                    {title:'农机名称',dataIndex:'name'},
                    {title:'农机类别',slotName:'type'},
                    {title:'农机品牌',dataIndex:'brand'},
                    {title:'供应商',dataIndex:'supplier'},
                    {title:'采购人员',dataIndex:'procurement'},
                    {title:'采购日期',dataIndex:'create_time'},
                    {title:'操作',slotName:'action',width:180},
                ]" :loading="state.loading">
                    <template #cover="{record}">
                        <div class="flex">
                            <a-image width="36" :src="record.img"></a-image>
                        </div>
                    </template>
                    <template #type="{record}">
                        <a-tag color="red">拖拉机</a-tag>
                    </template>
                    <template #action="{record}">
                        <a-button type="text" size="mini" class="ml-10">编辑</a-button>
                        <a-popconfirm content="确认删除该农机吗？" @ok="delData(record.id)">
                            <a-button type="text" size="mini">删除</a-button>
                        </a-popconfirm>
                    </template>
                </a-table>
                <kd-pager :pageData="state.info" :event="getList"></kd-pager>
            </a-card>
        </div>
        <cqkd-machinery-add ref="machineryRef"></cqkd-machinery-add>
    </kd-page-box>
</template>
<script setup>
    import { onMounted, reactive, ref } from 'vue';
    import CqkdMachineryAdd from './components/CqkdMachineryAdd.vue';
    const machineryRef = ref()
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
            {id:1,name:'拖拉机1',brand:'品牌',supplier:'供应商A',status:1,procurement:'张三',create_time:'2025/10/10 12:00',img:'https://kundian.cqkundian.com/uploads/farm/2024/9/27/24a1407a77e443d91f2edd15ecbf7068278bfa154e15cce38e786b5824aa7985e23ced191600240383.jpg',expand:''},
            {id:2,name:'拖拉机2',brand:'品牌',supplier:'供应商B',status:0,procurement:'张三',create_time:'2025/10/10 12:00',img:'https://kundian.cqkundian.com/uploads/farm/2024/9/27/24a1407a77e443d91f2edd15ecbf7068278bfa154e15cce38e786b5824aa7985e23ced191600240383.jpg',expand:''},
            {id:3,name:'拖拉机3',brand:'品牌',supplier:'供应商C',status:1,procurement:'张三',create_time:'2025/10/10 12:00',img:'https://kundian.cqkundian.com/uploads/farm/2024/9/27/24a1407a77e443d91f2edd15ecbf7068278bfa154e15cce38e786b5824aa7985e23ced191600240383.jpg',expand:''}
        ]
    }
    //新增编辑
    function showEdit(){
        machineryRef.value.show()
    }

    //删除数据
    function delData(id){
        
    }
</script>
<style>

</style>