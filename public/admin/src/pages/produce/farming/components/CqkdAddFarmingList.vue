<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/produce/farming/components/CqkdAddFarmingList.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-10 16:36:56  -->
<template>
    <div v-if="state.show">
        <a-drawer title="农事添加" v-model:visible="state.show" width="900px" :on-before-ok="saveDate">
            <a-form ref="formRef" :model="state.form" :label-col-props="{flex:'170px'}" :wrapper-col-props="{flex:1}">
                <a-form-item label="作业人员" field="workId" :rules="[{required:true,message:'请选择作业人员'}]">
                    <a-select v-model="state.form.workId" placeholder="请选择">
                        <a-option value="5ScY91745346526153V28kK">张三</a-option>
                        <a-option value="1">李四</a-option>
                    </a-select>
                </a-form-item>
				<a-form-item label="农事类型" field="growthName" :rules="[{required:true,message:'请选择农事类型'}]">
                    <a-select v-model="state.form.groupId" placeholder="请选择">
                        <a-option value="EBSL71741312480839VPMW6">种草</a-option>
                        <a-option value="1">浇水</a-option>
                    </a-select>
                </a-form-item>
				<a-form-item label="目标作物" field="plantIdList" :rules="[{required:true,message:'请选择目标作物'}]">
				    <a-select multiple v-model="state.form.plantIdList" placeholder="请选择">
				        <a-option value="sGp2617453467702660Tdkq">苹果</a-option>
				    </a-select>
				</a-form-item>
				<a-form-item label="作业时间" field="workTime" :rules="[{required:true,message:'请选择作业时间'}]">
				    <a-date-picker v-model="state.form.workTime" style="width: 200px;" />
				</a-form-item>
				<a-form-item label="作业内容" field="workDetail" :rules="[{required:true,message:'请输入作业内容'}]">
				    <a-textarea v-model="state.form.workDetail" placeholder="请输入" allow-clear/>
				</a-form-item>
				<a-form-item label="农事拍照" >
				    <a-upload
				        list-type="picture-card"
				        action="/"
				        :default-file-list="state.picture"
				        image-preview
				    />
				</a-form-item>
                <a-form-item label="使用物料">
					<div style="width: 100%;">
						<a-table :pagination="false" row-key="id" :bordered="false" :data="state.form.marchineList" :columns="[
							{title:'物料名称',dataIndex:'name'},
							{title:'数量',slotName:'count'},
							{title:'操作',slotName:'action',width:180},
						]" >
							<template #count="{record}">
								<a-input-number v-model="record.count" placeholder="数量" class="w240">
									<template #append>
										<a-select v-model="record.unit" placeholder="单位" class="w100">
										    <a-option value="kg">KG</a-option>
										    <a-option value="斤">袋</a-option>
										</a-select>
									</template>
								</a-input-number>
							</template>
							<template #action="{record}">
								<a-button type="text" size="mini" class="ml-10">编辑</a-button>
								<a-popconfirm content="确认删除农事类型吗？" @ok="delData(record.id)">
									<a-button type="text" size="mini">删除</a-button>
								</a-popconfirm>
							</template>
						</a-table>
						<a-button type="text" class="mt10"><i class="ri-add-line"></i>添加一个</a-button>
					</div>
                    <!-- <a-select multiple v-model="state.form.suppliesIdList" placeholder="请选择" allow-clear>
                        <a-option value="sGp2617453467702660Tdkq">有机肥</a-option>
                    </a-select> -->
                </a-form-item>
                <a-form-item label="使用农机" >
                    <a-select multiple v-model="state.form.suppliesIdList" placeholder="请选择" allow-clear>
                        <a-option value="sGp2617453467702660Tdkq">锄头</a-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="是否公开">
                    <a-radio-group v-model="state.form.openState">
                        <a-radio :value="true">是</a-radio>
                        <a-radio :value="false">否</a-radio>
                    </a-radio-group>
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
            farmParamId: null,
            groupId: "",
            machineIdList: [],
            openState: true,
            picture: '',
            plantIdList: [],
            suppliesIdList: [],
            workManList: [],
            workDetail: "",
            workId: "",
            workMan: "",
            workTime: '',
            farmWorkFarmParamList: [],
			marchineList:[
				{name:'有机肥A',count:0,unit:'kg'},
				{name:'有机肥B',count:0,unit:'kg'},
			],	//物料
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