<!-- 坤典智慧农场V6 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/produce/growthAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-19 14:53:02  -->
<template>
<kd-page-box :loading="state.loading">
	<div class="kd-content mould-add">
		<kd-back title="新增/编辑作物生长标准"></kd-back>
		<a-form ref="formRef" :model="form" :label-col-props="{flex:'140px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="生长标准名称" field="name" :rules="{required:true,message:'生长标准名称必填'}">
				<a-input placeholder="请输入生长标准名称" v-model="form.name" class="w600"></a-input>
			</a-form-item>
			<a-form-item label="生长阶段">
				<div style="width: 1000px;">
					<a-table :pagination="false" :data="form.list" :columns="[
						{title:'阶段图片',slotName:'cover'},
						{title:'阶段名称',slotName:'name'},
						{title:'持续时间',slotName:'time'},
						{title:'阶段类型',slotName:'type'},
						{title:'备注',slotName:'remark'},
						{title:'操作',slotName:'action'},
					]">
						<template #cover="{record}">
							<div>
								<a-upload
								    list-type="picture-card"
								    action="/"
								    image-preview
								/>
							</div>
						</template>
						<template #name="{record}">
							<a-input class="w120" placeholder="阶段名称" v-model="record.name"></a-input>
						</template>
						<template #time="{record}">
							<a-input-number class="w160" placeholder="阶段持续时间" v-model="record.name">
								<template #append>天</template>
							</a-input-number>
						</template>
						<template #type="{record}">
							<a-select class="w120" v-model="record.type">
								<a-option :value="1">一次性阶段</a-option>
								<a-option :value="2">循环阶段</a-option>
							</a-select>
							
						</template>
						<template #remark="{record}">
							<a-input class="w120" placeholder="备注" v-model="record.name"></a-input>
						</template>
						<template #action="{record}">
							<a-button type="text" size="mini">删除</a-button>
						</template>
					</a-table>
					<a-button type="text" size="mini"class="mt10"><i class="ri-add-line"></i>添加一个</a-button>
				</div>
			</a-form-item>
			<a-form-item>
				<a-button type="primary" class="w120" :loading="state.saving" @click="saveData">保存</a-button>
			</a-form-item>
		</a-form>
	</div>
</kd-page-box>
</template>

<script setup>
import { reactive } from 'vue';
const state = reactive({
	loading:false,
	saving:false,
})
const form = reactive({
	name:'',
	list:[
		{name:'',cover:'',time:null,remark:'',type:1},
		{name:'',cover:'',time:null,remark:'',type:1},
	]
})
</script>

<style>
</style>