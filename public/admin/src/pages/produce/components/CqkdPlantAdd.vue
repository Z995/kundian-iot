<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/components/CqkdAccoundAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-07 15:20:43  -->
<template>
    <div v-if="state.show && state.form">
        <a-modal title="添加/编辑种植" v-model:visible="state.show" width="800px" :on-before-ok="saveDate">
            <a-spin :loading="state.loading">
                <a-form ref="formRef" :model="state.form" :label-col-props="{flex:'110px'}" :wrapper-col-props="{flex:1}">
                   <a-form-item label="所属大棚" field="plot_id" :rules="[{required:true,message:'作物生长标准必填'}]">
                       <a-select v-model="state.form.plot_id" placeholder="请选择">
                           <a-option>大棚A</a-option>
                           <a-option>大棚B</a-option>
                       </a-select>
                   </a-form-item>
					<a-form-item label="生长标准" field="type" :rules="[{required:true,message:'作物生长标准必填'}]">
                        <a-select v-model="state.form.type" placeholder="请选择">
                            <a-option>苹果</a-option>
                            <a-option>桃子</a-option>
                        </a-select>
                    </a-form-item>
					<a-form-item label="名称" field="name" :rules="[{required:true,message:'请输入名称'}]">
                        <a-input v-model="state.form.name" placeholder="请输入名称" class="w600"></a-input>
                    </a-form-item>
                    <a-form-item label="规模">
                        <a-input v-model="state.form.scale" placeholder="请输入规模" class="w600"></a-input>
                    </a-form-item>
                    <a-form-item label="种植日期">
                        <a-date-picker v-model="state.form.time" />
                    </a-form-item>
                    <a-form-item label="作物照片">
                        <a-upload
                            list-type="picture-card"
                            action="/"
                            :default-file-list="state.fileList"
                            image-preview
                        />
                    </a-form-item>
                    <a-form-item label="环境数据">
                        <a-checkbox-group :default-value="['1']">
                            <a-checkbox value="1">1号大棚</a-checkbox>
                            <a-checkbox value="2">2号大棚</a-checkbox>
                            <a-checkbox value="3">3号大棚</a-checkbox>
                        </a-checkbox-group>
                    </a-form-item>
                    <a-form-item label="备注">
                        <a-input v-model="state.form.remarks" placeholder="请输入备注" class="w600"></a-input>
                    </a-form-item>
                </a-form>
            </a-spin>
        </a-modal>
    </div>
</template>
<script setup>
    import { reactive } from 'vue';

    const state = reactive({
        show:false,
        form:null,
        loading:false,
        fileList:[]
    })

    function show(data){
        state.form = data?data:{
            image:'',
            name:'',
            plot_id:null,
            type:null,
            grow:'1',
            residualGrowthTime:"",
            scale:0,
            remarks:'',
            time:''
        }
        state.show = true
    }

    defineExpose({
        show
    })
</script>
<style>
</style>