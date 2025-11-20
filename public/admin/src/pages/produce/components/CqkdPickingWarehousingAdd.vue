<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/components/CqkdPickingWarehousingAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-10 17:52:43  -->
<template>
    <div v-if="state.show && state.form">
        <a-modal :title="state.form.id?'编辑采摘':'添加采摘'" v-model:visible="state.show" width="600px" :on-before-ok="saveDate">
            <a-spin :loading="state.loading" style="width: 100%;">
                <a-form ref="formRef" :model="state.form" :label-col-props="{flex:'100px'}" :wrapper-col-props="{flex:1}">
                    <a-form-item label="采摘人员" field="name" :rules="[{required:true,message:'请选择采摘人员'}]">
                        <a-select v-model="state.form.name" placeholder="请选择">
                            <a-option value="张三">张三</a-option>
                        </a-select>
                    </a-form-item>
                    <a-form-item label="采摘作物" field="cropName" :rules="[{required:true,message:'请选择采摘作物'}]">
                        <a-select v-model="state.form.cropName" placeholder="请选择">
                            <a-option value="苹果">苹果</a-option>
                        </a-select>
                    </a-form-item>
                    <a-form-item label="采摘数量" field="pickingCount" :rules="[{required:true,message:'请输入采摘数量'}]">
                        <a-input-number v-model="state.form.pickingCount" placeholder="请输入采摘数量" :min="0" :max="100" class="w300">
							<template #append>
								<a-select v-model="state.form.unit" placeholder="请选择" class="w100">
								    <a-option value="kg">kg</a-option>
								    <a-option value="斤">斤</a-option>
								    <a-option value="框">框</a-option>
								    <a-option value="袋">袋</a-option>
								</a-select>
							</template>
						</a-input-number>
                    </a-form-item>
                    <!-- <a-form-item label="生成溯源批次">
                        <a-radio-group v-model="state.form.roots">
                            <a-radio value="1">是</a-radio>
                            <a-radio value="0">否</a-radio>
                        </a-radio-group>
                        <div class="flex-c ml5" v-if="state.form.roots !== '0'">
                            <div style="width: 100%;">保质期 (时长及存储条件)</div>
                            <a-input v-model="state.form.expirationDate" placeholder="请输入生成溯源批次"></a-input>
                        </div>
                    </a-form-item> -->
                    <a-form-item label="采摘时间" field="time" :rules="[{required:true,message:'请选择采摘时间'}]">
                        <a-date-picker v-model="state.form.time" />
                    </a-form-item>
					<a-form-item label="入库仓库" >
					    <a-select v-model="state.form.house" placeholder="请选择">
					        <a-option value="1">仓库A</a-option>
					        <a-option value="2">仓库B</a-option>
					        <a-option value="3">仓库C</a-option>
					    </a-select>
					</a-form-item>
                    <a-form-item label="备注">
                        <a-textarea placeholder="请输入" allow-clear/>
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
        loading:false,
        form:{
            id:0,
            name:'',
            cropName:'',
            pickingCount:'',
            expirationDate:'',
            roots:'1'
        }
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