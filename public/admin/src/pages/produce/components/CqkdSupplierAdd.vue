<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/produce/material/components/CqkdMaterialAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-11 11:32:56  -->
<template>
    <div v-if="state.show">
        <a-drawer :title="state.form.id?'供应商编辑':'供应商添加'" v-model:visible="state.show" width="60%" :on-before-ok="saveDate">
            <a-form ref="formRef" :model="state.form" :label-col-props="{flex:'170px'}" :wrapper-col-props="{flex:1}">
                <a-form-item label="供应商名称" field="name" :rules="[{required:true,message:'请输入供应商名称'}]">
                    <a-input v-model="state.form.name" placeholder="请输入供应商名称"></a-input>
                </a-form-item>
                <a-form-item label="供应产品" field="name" :rules="[{required:true,message:'请输入供应产品'}]">
                    <a-input v-model="state.form.name" placeholder="请输入供应产品"></a-input>
                </a-form-item>
                <a-form-item label="联系地址" field="name" :rules="[{required:true,message:'请输入联系地址'}]">
                    <a-input v-model="state.form.name" placeholder="请输入联系地址"></a-input>
                </a-form-item>
                <a-form-item label="联系人" field="name" :rules="[{required:true,message:'请输入联系人'}]">
                    <a-input v-model="state.form.name" placeholder="请输入联系人"></a-input>
                </a-form-item>
                <a-form-item label="电话" field="name" :rules="[{required:true,message:'请输入电话'}]">
                    <a-input v-model="state.form.name" placeholder="请输入电话"></a-input>
                </a-form-item>
                <a-form-item label="备注">
                    <a-textarea placeholder="请输入" allow-clear/>
                </a-form-item>
            </a-form>
        </a-drawer>
    </div>
</template>

<script setup>
    import { reactive } from 'vue';

    const state = reactive({
        show:false,
        form:{
            id:0,
            type:null,
            name:'',
            attr:'',
            warehouse:null
        },
        picture:[]
    })

    function show(){
        state.show = true
    }

    defineExpose({
        show
    })

</script>

<style>

</style>