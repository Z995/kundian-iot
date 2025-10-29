<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/mouldAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 09:21:06  -->
<template>
<kd-page-box :loading="state.loading">
	<div class="kd-content">
		<kd-back title="新增/编辑设备模板"></kd-back>
		<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'130px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="模板名称" field="name" :rules="[{required:true,message:'模板名称必填'}]">
				<a-input placeholder="请输入模板名称" v-model="state.form.name" class="w600"></a-input>
			</a-form-item>
			<a-form-item label="采集方式">
				<span>云端轮询</span>
			</a-form-item>
			<a-form-item label="状态配置">
				<a-radio-group v-model="state.form.status_config">
					<a-radio :value="1">网关</a-radio>
					<a-radio :value="2">设备数据</a-radio>
				</a-radio-group>
			</a-form-item>
			<a-form-item label="选择时长" v-if="state.form.status_config===2">
				<div class="flex-c">
					<span class="mr10">设备下</span>
					<a-select placeholder="请选择时长" class="w160" v-model="state.form.space_time">
						<a-option :value="3">3分钟</a-option>
						<a-option :value="5">5分钟</a-option>
						<a-option :value="10">10分钟</a-option>
						<a-option :value="60">1小时</a-option>
					</a-select>
					<span class="ml10">无数据时则判断该设备离线</span>
				</div>
			</a-form-item>
			<div class="data-box mb20">
				<div class="f15">
					<span class="fb">设备变量</span>
				</div>
			
				<!-- 多从机模式 -->
				<div class="single-box mt20">
					<div class="s-index-box flex">
						<div class="flex">
							<div class="s-index-item" :class="{active:state.currentIndex===index}" 
								v-for="(item,index) in state.form.subordinate" :key="index"
								@click.stop="state.currentIndex = index"
							>
								<span class="mr10">{{ item.name || '从机名称'}}</span>
								<icon-close @click="addSIndex(index)"/>
							</div>
						</div>
						<a-button type="text" class="ml10" @click="addSIndex(-1)"><icon-plus />添加从机</a-button>
					</div>
					<template v-for="(item,index) in state.form.subordinate" :key="index">
						<div v-if="index === state.currentIndex">
							<a-form-item label="从机名称" required>
								<a-input v-model="state.form.subordinate[state.currentIndex].name" placeholder="请输入从机名称" class="w600"></a-input>
							</a-form-item>
							<a-form-item label="协议和产品" required>
								<a-select placeholder="请选择协议和产品" class="w600" 
									v-model="state.form.subordinate[state.currentIndex].product_id"
									:disabled="!!state.form.subordinate[state.currentIndex].variable.length"
									@change="changeProtocol"
								>
									<a-option :value="item.id" v-for="(item,index) in state.productList" :key="index">
										{{ item.name}}
									</a-option>
								</a-select>
							</a-form-item>
							
							<a-space class="mb20">
								<a-button type="primary" class="w120" @click="showAdd(null)">添加变量</a-button>
								<!-- <a-button type="primary" class="w120">导入变量</a-button> -->
								<!-- <a-button type="primary" class="w120" >导出变量</a-button> -->
								<a-popconfirm content="确认删除选择的变量数据吗?" @ok="delBatchData">
									<a-button type="primary" class="w120">批量删除</a-button>
								</a-popconfirm>
							</a-space>
							<kd-add-mould-data :index="index" :list="item.variable"
								@edit="showAdd"
								@deleteBatch="(data)=>state.selectId = data"
								@delete="delData"
							></kd-add-mould-data>
						</div>
					</template>
				</div>
			</div>
			<a-form-item>
				<a-button type="primary" class="w120" :loading="state.saving" @click="saveData">保存</a-button>
			</a-form-item>
		</a-form>
		
		<kd-data-add ref="addRef" @success="getMouldData"></kd-data-add>
	</div>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive,ref } from 'vue';
import KdAddMouldData from './components/mould/KdAddMouldData.vue';
import KdDataAdd from './components/mould/KdDataAdd.vue';
import { saveDeviceMouldData,getDeviceMouldDetail,getProductList,getProductDetail } from '@/api/kdDevice'
import { Message } from '@arco-design/web-vue';
import { useRoute } from 'vue-router';
import router from '../../router';
import {getRandom } from '@/util/util'
const addRef = ref()
const formRef = ref()
const option = useRoute().query
const state = reactive({
	loading:false,
	form:{
		id:0,
		name:"",				//模板名称
		collect:1,				//采集方式 1：云端轮询
		status_config:1,		//状态判断方式 1：网关 2：设备数据
		space_time:"",			//时长分钟数
		subordinate:[			//从机
			{
				template_id:'',
				name:'从机名称',		//从机名称
				protocol:'',		//从机协议
				product_id:null,	//产品协议
				variable:[],		//变量
			}
		]
	},
	currentIndex:0,
	dataIndex:0,	//当前操作变量
	saving:false,
	productList:[],
	selectId:[],
})

onMounted(async()=>{
	state.loading = true
	let data = null
	if( option.id ) {
		let res = await getDeviceMouldDetail({id:option.id})
		if( res.data && res.data.id ){
			data = res.data
		}
	}
	
	if( data && data.subordinate ){
		data.subordinate.forEach(item=>{
			if( item.variable ){
				item.variable.forEach(val=>{
					val.unique_key = val.id
				})
			}
		})
	}
	
	state.form = {
		id:data ?data.id:0,
		name:data?data.name:"",				
		collect:data?data.collect:1,				
		status_config:data?data.status_config : 1,		
		space_time:data?(data.space_time || null) :"",			
		subordinate:data?data.subordinate : [			
			{
				template_id:'',
				name:'从机名称',		
				protocol:'',
				product_id:null,		
				variable:[],		
			}
		]
	}
	state.form.subordinate.forEach(item=>{
		if( !item.product_id && item.protocol ){
			if( item.protocol ===2 ){
				item.product_id = -2
			}
			if( item.protocol ===1 ){
				item.product_id = -1
			}
		}
	})
	
	state.loading = false
	getProductList({page:1,limit:99,status:1}).then(res=>{
		state.productList = [
			{id:-1,name:'Modbus RTU',protocol:1},
			{id:-2,name:'自定义协议',protocol:2},
			...res.data.list
		]
	})
})

//添加从机
function addSIndex(index){
	if( index===-1){
		state.form.subordinate.push({
			template_id:'',
			name:'从机名称',		//从机名称
			protocol:'',		//从机协议
			variable:[],		//变量
		})
	}else{
		state.form.subordinate.splice(index,1)
	}
}
//添加变量
function showAdd(data){
	let protocol = state.form.subordinate[state.currentIndex].protocol
	if( !protocol ){
		Message.warning('请先选择从机协议和产品')
		return false
	}
	if( data ===null ){
		state.dataIndex = -1
		addRef.value.show(null,protocol)
	}else{
		state.dataIndex = data
		addRef.value.show(state.form.subordinate[state.currentIndex].variable[data])
	}
}

//获取变量数据
function getMouldData(data){
	let i = state.currentIndex
	if( state.dataIndex >=0 ){
		state.form.subordinate[i].variable[state.dataIndex] = data
		return false
	}
	if( !data.id ){
		state.form.subordinate[i].variable.push({
			...data,
			unique_key:getRandom(6),	//用作批量删除唯一key
		})
	}else{
		let idx = state.form.subordinate[i].variable.findIndex(item=>item.id == data.id)
		state.form.subordinate[i].variable[idx] = data
	}
}
//删除变量
function delData(index){
	state.form.subordinate[state.currentIndex].variable.splice(index,1)
}

//批量删除
function delBatchData(){
	if( !state.selectId ) return false;
	//全部删除
	if( state.selectId === true ){
		state.selectId = []
		state.form.subordinate[state.currentIndex].variable = []
		return;
	}
	for (var i = 0; i < state.selectId.length; i++) {
		let idx = state.form.subordinate[state.currentIndex].variable.findIndex( item=> item.unique_key === state.selectId[i] )
		if( idx >=0 ){
			delData(idx)
		}
	}
	state.selectId = []
}

//切换产品协议
function changeProtocol(data){
	if( data > 0 ){
		getProductDetail({id:data}).then(res=>{
			state.form.subordinate[state.currentIndex].protocol = res.data.protocol
			let list = []
			let variable = res.data?.variable || []
			variable.forEach(item=>{
				list.push({
					unique_key:getRandom(6),
					name:item.name,								//变量名称
					unit:item.unit,								//单位
					type:item.type,								//变量类型 1直采变量 2运算变量 3录入变量
					register_mark:item.register_mark,			//寄存器标识 [0,1,2,3,4]
					register_address:item.register_address,		//寄存器地址
					data_format:item.data_format,				//数据格式
					collect_frequency:item.collect_frequency,	//采集频率
					fraction:item.fraction,						//数字格式 1:一位,2:二位,3:三位,4:四位,
					storage_mode:item.storage_mode,				//储存方式 1变化存储 2全部存储
					read_write_mode:item.read_write_mode,		//读写模式1只读，2读写
					collect_formula:item.collect_formula,		//采集公式
					contro_formula:item.contro_formula			//控制公式
				})
			})
			state.form.subordinate[state.currentIndex].variable = list
		})
	}else{
		if( data ===-1 ) state.form.subordinate[state.currentIndex].protocol=1
		if( data ===-2 ) state.form.subordinate[state.currentIndex].protocol=2
	}
}

//保存数据
async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) return false
	let form = JSON.parse(JSON.stringify(state.form))
	
	state.saving = true
	
	//删除unikey 参数
	form.subordinate.forEach(item=>{
		item.variable.forEach(val=>{
			if( val.unique_key ) delete val.unique_key
		})
	})
	
	let res = await saveDeviceMouldData(form)
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

<style lang="scss" scoped>
.data-box{
	width: 100%;
	border: 1px solid #f4f4f4;
	padding: 20px;
	
	.s-index-box{
		width: 100%;
		height: 40px;
		margin-bottom: 20px;
		align-items: center;
		.s-index-item{
			height: 100%;
			line-height: 40px;
			padding: 0 10px;
			border: 1px solid #f4f4f4;
			border-right: none;
			cursor: pointer;
			font-size: 13px;
			&:last-child{
				border-right: 1px solid #f4f4f4;
			}
			&:first-child{
				border-right: 1px solid #f4f4f4;
			}
		}
		.active{
			border: 1px solid #0066FF;
			color: #0066FF;
			background: rgba(#0066FF, .1);
			border-right: 1px solid #0066FF!important;
		}
	}
}
</style>