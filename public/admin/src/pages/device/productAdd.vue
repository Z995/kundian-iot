<!-- 坤典物联 - 添加产品库 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/device/productAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-18 10:53:32  -->
<template>
<kd-page-box :loading="state.loading">
	<div class="kd-content">
		<kd-back title="新增/编辑产品"></kd-back>
		<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'130px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="产品名称" field="name" :rules="[{required:true,message:'产品名称必填'}]">
				<a-input placeholder="请输入产品名称" v-model="state.form.name" class="w600"></a-input>
			</a-form-item>
			<a-form-item label="产品协议" field="protocol" required>
				<a-radio-group v-model="state.form.protocol" :disabled="state.form.variable.length">
					<a-radio :value="1">Modbus RTU协议</a-radio>
					<a-radio :value="2">自定义协议</a-radio>
				</a-radio-group>
			</a-form-item>
			<a-form-item label="状态">
				<a-switch v-model="state.form.status" 
					:checked-value="1" :unchecked-value="0"
					checked-text="启用" unchecked-text="禁用"
				></a-switch>
			</a-form-item>
			<a-form-item label="变量列表">
				<a-table class="kd-small-table" style="width:800px;" :pagination="false" :data="state.form.variable" :columns="[
					{title:'ID',dataIndex:'id'},
					{title:'变量名称',dataIndex:'name'},
					{title:'变量类型',slotName:'type'},
					{title:'数值类型',slotName:'data_format'},
					{title:'寄存器',slotName:'register'},
					{title:'读写',slotName:'read_write_mode'},
					{title:'存储方式',slotName:'storage_mode'},
					{title:'排序',dataIndex:'sort'},
					{title:'操作',slotName:'action'},
				]">
					<template #type="{record}">
						<span>{{constantData.dataType[record.type]}}</span>
					</template>
					<template #data_format="{record}">
						<span>{{constantData.dataFormat[record.data_format]}}</span>
					</template>
					<template #register="{record}">
						<span>{{record.register_mark}}{{record.register_address}}</span>
					</template>
					<template #read_write_mode="{record}">
						<span>{{record.read_write_mode === 1?'只读':'读写'}}</span>
					</template>
					<template #storage_mode="{record}">
						<span>{{record.storage_mode === 1?'变化存储':'全部存储'}}</span>
					</template>
					<template #action="{record,rowIndex}">
						<a-button type="text" size="mini" class="ml-10" @click="showProductData(record,rowIndex)">编辑</a-button>
						<a-button type="text" size="mini" class="ml-10" @click="delProductData(rowIndex)">删除</a-button>
					</template>
					<template #footer>
						<div class="flex-cc">
							<a-button type="text" size="mini" @click="showProductData(null,-1)">
								<i class="ri-add-line"></i>新增变量
							</a-button>
						</div>
					</template>
				</a-table>
			</a-form-item>
			<a-form-item>
				<a-button type="primary" class="w120" :loading="state.saving" @click="saveData">保存</a-button>
			</a-form-item>
		</a-form>
	</div>
	<cqkd-product-data-add ref="addRef" @success="getData"></cqkd-product-data-add>
</kd-page-box>
</template>

<script setup>
import constantData from '@/util/common/constantData';
import { onMounted, reactive, ref } from 'vue';
import CqkdProductDataAdd from './components/product/CqkdProductDataAdd.vue';
import { saveProductData,getProductDetail } from '@/api/kdDevice'
import { Message } from '@arco-design/web-vue';
import { useRoute } from 'vue-router';
import router from '../../router';
const addRef = ref()
const formRef = ref()
const option = useRoute().query
const state = reactive({
	loading:false,
	form:{
		id:0,
		name:'',
		protocol:1,
		status:1,
		variable:[]
	},
	dataIndex:-1,	//当前操作变量
	saving:false
})
onMounted(async()=>{
	let data = null
	if( option.id ){
		state.loading = true
		let res = await getProductDetail({id:option.id})
		state.loading = false
		data = res.data
	}
	state.form = {
		id:data?data.id: 0,
		name:data?data.name:'',
		protocol:data?data.protocol:1,
		status:data?data.status:1,
		variable:data?data.variable:[]
	}
})

//新增，编辑变量
const showProductData = (data,index)=>{
	state.dataIndex = index
	addRef.value.show(data,state.form.protocol)
}

function getData(data){
	if( state.dataIndex >=0 ){
		state.form.variable[state.dataIndex] = data
		return false
	}else{
		state.form.variable.push(data)
	}
}

//删除变量
function delProductData(index){
	state.form.variable.splice(index,1)
}

//保存数据
async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) return false
	let form = JSON.parse(JSON.stringify(state.form))
	if( !form.id ) delete form.id
	state.saving = true
	let res = await saveProductData(form)
	state.saving = false
	if( res.code === 200 ){
		Message.success("保存成功")
		setTimeout(function(){
			router.go(-1)
		},500)
		return
	}
	Message.error(res.msg)
}

</script>

<style>
</style>