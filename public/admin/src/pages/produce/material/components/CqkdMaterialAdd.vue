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
<!-- @time:2025-11-11 10:40:56  -->
<template>
    <div v-if="state.show">
        <a-drawer :title="state.form.id?'物料编辑':'物料添加'" v-model:visible="state.show" width="60%" :on-before-ok="saveDate">
            <a-form ref="formRef" :model="state.form" :label-col-props="{flex:'170px'}" :wrapper-col-props="{flex:1}">
                <a-form-item label="物料类别" field="type" :rules="[{required:true,message:'请选择物料类别'}]">
                    <a-select v-model="state.form.type" placeholder="请选择">
                        <a-option value="0">肥料</a-option>
                        <a-option value="1">农药</a-option>
                        <a-option value="2">种子</a-option>
                        <a-option value="3">兽药</a-option>
                        <a-option value="4">饲料</a-option>
                        <a-option value="5">其他</a-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="物料名称" field="name" :rules="[{required:true,message:'请输入物料名称'}]">
                    <a-input v-model="state.form.name" placeholder="请输入物料名称"></a-input>
                </a-form-item>
                <a-form-item label="物料规格" field="attr" :rules="[{required:true,message:'请输入物料规格'}]">
                    <a-input v-model="state.form.attr" placeholder="请输入物料规格"></a-input>
                </a-form-item>
                <a-form-item label="所在仓库" field="warehouse" :rules="[{required:true,message:'请选择所在仓库'}]">
                    <a-select multiple v-model="state.form.warehouse" placeholder="请选择">
                        <a-option value="1">仓库1</a-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="实物图片" field="picture" :rules="[{required:true,message:'请上传实物图片'}]">
                    <a-upload
                        list-type="picture-card"
                        action="/"
                        :default-file-list="state.picture"
                        image-preview
                    />
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