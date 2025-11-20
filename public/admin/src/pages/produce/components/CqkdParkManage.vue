<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/park/CqkdParkManage.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-07 13:49:43  -->
<template>
    <div v-if="state.show && state.form">
        <a-drawer title="修改园区" v-model:visible="state.show" width="800px" :on-before-ok="saveDate">
            <a-spin :loading="state.loading">
                <a-form ref="formRef" :model="state.form" :label-col-props="{flex:'110px'}" :wrapper-col-props="{flex:1}">
                    <a-form-item label="园区名称" field="name" :rules="[{required:true,message:'园区名称必填'}]">
                        <a-input v-model="state.form.name" placeholder="请输入园区名称" class="w600"></a-input>
                    </a-form-item>
                    <a-form-item label="园区面积" field="area" :rules="[{required:true,message:'园区面积必填'}]">
                        <a-input v-model="state.form.area" placeholder="请输入园区面积(㎡)" class="w600">
                            <template #append>㎡</template>
                        </a-input>
                    </a-form-item>
                    <a-form-item label="负责人">
                        <a-input v-model="state.form.responsiblePerson" placeholder="请输入负责人" class="w600"></a-input>
                    </a-form-item>
                    <a-form-item label="联系方式">
                        <a-input v-model="state.form.phone" placeholder="请输入联系方式" class="w600"></a-input>
                    </a-form-item>
                    <a-form-item label="地址">
                        <a-input v-model="state.form.address" placeholder="请输入地址" class="w600"></a-input>
                    </a-form-item>
                    <a-form-item label="园区大屏可视化标题名称">
                        <a-input v-model="state.form.title" placeholder="请输入园区大屏可视化标题名称" class="w600"></a-input>
                    </a-form-item>
                    <a-form-item label="园区封面图">
                        <a-upload
                            list-type="picture-card"
                            action="/"
                            :default-file-list="state.fileList"
                            image-preview
                        />
                    </a-form-item>
                </a-form>
            </a-spin>
        </a-drawer>
    </div>
</template>
<script setup>
    import { reactive,ref } from 'vue';
    const formRef = ref()
    const state = reactive({
        loading:false,
        show:false,
        form:null,
        fileList:[]
    })

    function show(data){
        state.form = data
        state.show = true
    }

    async function saveDate(){

    }

    defineExpose({
        show
    })
</script>
<style>
</style>