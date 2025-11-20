<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/material/components/CqkdWarehouseAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-11 10:03:13  -->
<template>
    <div>
        <a-modal :title="state.form.id ?'编辑仓库管理':'新增仓库管理'" v-model:visible="state.show" width="600px" :on-before-ok="saveData">
            <a-form :model="state.form" ref="formRef" :label-col-props="{flex:'120px'}" :wrapper-col-props="{flex:1}">
                <a-form-item label="仓库名称" field="name" :rules="[{required:true,message:'仓库名称必填'}]">
                    <a-input v-model="state.form.name" placeholder="请输入仓库名称"></a-input>
                </a-form-item>
            </a-form>
        </a-modal>
    </div>
</template>

<script setup>
    import { reactive, ref } from 'vue';
    const formRef = ref()
    const state = reactive({
        show:false,
        form:{
            id:0,
            name:'',
        }
    })

    function show(data){
        state.show = true
        state.form = {
            id:data?data.id : 0,
            name:data?data.name : ''
        }
    }

    async function saveData(){
        let valid = await formRef.value.validate()
        if( valid ) return;
        let form = JSON.parse(JSON.stringify(state.form))
        if( !form.id ) delete form.id
        
        //保存数据操作
    }
    defineExpose({
        show
    })
</script>

<style>
</style>