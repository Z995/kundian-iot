<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/alarm/components/KdMouldTriggerAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 09:57:54  -->
<template>
<div v-if="state.show">
	<a-drawer title="添加模板触发器" v-model:visible="state.show" :on-before-ok="saveData" width="800px">
		<a-spin :loading="state.loading" tip="数据加载中...">
			<a-form :model="state.form" ref="formRef" :label-col-props="{flex:'130px'}"
				:wrapper-col-props="{flex:1}" v-if="state.show" :rules="state.rules"
			>
				<a-form-item label="触发器名称" field="name" :rules="[{required:true,message:'触发器名称必填'}]">
					<a-input v-model="state.form.name" placeholder="请输入触发器名称" class="w500"></a-input>
				</a-form-item>
				<a-form-item label="选择模板和变量" field="template_subordinate_variable_id">
					<a-space>
						<a-select placeholder="请选择模板" class="w160" v-model="state.form.template_id" @change="getSlave">
							<a-option v-for="(item,index) in state.deviceMould" :key="index" :value="item.id">
								{{ item.name}}
							</a-option>
						</a-select>
						<a-select placeholder="请选择从机" class="w160" v-model="state.form.template_subordinate_id" @change="getDeviceData">
							<a-option v-for="(item,index) in state.slaveList" :key="index" :value="item.id">
								{{ item.name}}
							</a-option>
						</a-select>
						<a-select placeholder="请选择变量" class="w160" v-model="state.form.template_subordinate_variable_id">
							<a-option v-for="(item,index) in state.deviceData" :key="index" :value="item.id">
								{{ item.name}}
							</a-option>
						</a-select>
					</a-space>
				</a-form-item>
				<a-form-item label="触发条件" field="condition">
					<a-space>
						<a-select v-model="state.form.condition" placeholder="请选择触发条件" class="w160">
							<a-option :value="index" v-for="(item,index) in constantData.triggerCondition">
								{{item}}
							</a-option>
						</a-select>
						<template v-if="['2','4','5','6'].includes(state.form.condition)">
							<a-input placeholder="请输入A的值" class="w160" v-model="state.form.condition_parameter.A"></a-input>
						</template>
						<template v-if="['3','4','5'].includes(state.form.condition)">
							<a-input placeholder="请输入B的值" class="w160" v-model="state.form.condition_parameter.B"></a-input>
						</template>
					</a-space>
				</a-form-item>
				<a-form-item label="报警死区" v-if="['2','3','4','5','6'].includes(state.form.condition)">
					<a-input placeholder="请输入触发阈值" class="w500" v-model="state.form.dead_zone"></a-input>
				</a-form-item>
				<a-form-item label="报警">
					<a-switch v-model="state.form.is_alarm" :checked-value="1" :unchecked-value="0"></a-switch>
				</a-form-item>
				<template v-if="state.form.is_alarm===1">
					<a-form-item label="报警推送内容">
						<a-textarea placeholder="请输入报警推送内容" style="height: 80px;"
							v-model="state.form.alarm_push"
							 :max-length="50" allow-clear show-word-limit class="w500"
						></a-textarea>
					</a-form-item>
					<a-form-item label="恢复正常推送内容">
						<a-textarea placeholder="请输入恢复正常推送内容" style="height: 80px;"
							v-model="state.form.resume_push"
							 :max-length="50" allow-clear show-word-limit class="w500"
						></a-textarea>
					</a-form-item>
				</template>
				<a-form-item label="联动">
					<a-switch v-model="state.form.is_linkage" :checked-value="1" :unchecked-value="0"
						checked-text="联动" unchecked-text="不联动"
					></a-switch>
				</a-form-item>
				<template v-if="state.form.is_linkage===1">
					<a-form-item label="联动类型" required>
						<a-radio-group v-model="state.form.linkage_type">
							<a-radio :value="1">采集</a-radio>
							<a-radio :value="2">控制</a-radio>
						</a-radio-group>
					</a-form-item>
					<a-form-item label="联动变量" field="linkage_subordinate_variable_id">
						<div>
							<a-space>
								<a-select placeholder="请选择从机" class="w160" v-model="state.form.linkage_subordinate_id"
									@change="changeLinkSlave"
								>
									<a-option v-for="(item,index) in state.slaveList" :key="index" :value="item.id">
										{{ item.name}}
									</a-option>
								</a-select>
								<a-select placeholder="请选择变量" class="w160" v-model="state.form.linkage_subordinate_variable_id">
									<a-option v-for="(item,index) in state.linkDeviceData" :key="index" :value="item.id">
										{{ item.name}}
									</a-option>
								</a-select>
							</a-space>
							<div class="mt20" v-if="state.form.linkage_type ===2">
								<a-radio-group v-model="state.form.control_type">
									<a-radio :value="1">手动下发</a-radio>
									<a-radio :value="2">取报警值</a-radio>
								</a-radio-group>
							</div>
							<div  class="mt20" v-if="state.form.linkage_type ===2 && state.form.control_type ===1">
								<a-textarea placeholder="请输入下发数据（十进制）" style="height: 80px;"
									v-model="state.form.number"
									:max-length="50" allow-clear show-word-limit class="w500"
								></a-textarea>
							</div>
						</div>
					</a-form-item>
				</template>
			</a-form>
		</a-spin>
	</a-drawer>
</div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import constantData from '../../../util/common/constantData';
import { getDeviceMouldList } from '@/api/kdDevice'
import { saveMouldTriggerData ,getMouldTriggerDetail } from '@/api/kdTrigger'
import { Message } from '@arco-design/web-vue';
const emits = defineEmits(['success'])
const formRef = ref()
const state = reactive({
	show:false,
	loading:false,
	form:{
		id:0,
		name:'',
		template_id:null,						//模板id
		template_subordinate_id:null,			//模板从机id
		template_subordinate_variable_id:null,	//模板变量id
		condition:'',							//触发条件 
		condition_parameter:{
			A:"",
			B:""
		},										//条件参数
		dead_zone:"",							//报警死区
		is_alarm:0,								//是否报警
		alarm_push:"",							//报警推送
		resume_push:"",							//恢复推送
		is_linkage:0,							//是否联动 0不联动，1联动
		linkage_type:1,							//联动类型 1采集，2控制
		linkage_subordinate_id:null,			//联动从机id
		linkage_subordinate_variable_id:null,	//联动变量id
		control_type:1,							//控制类型 1手动下发，2报警值
		number:"",								//手动下发值
	},
	rules:{
		template_subordinate_variable_id:[
			{required:true,message:'设备变量必选'},
			{
				validator:(value,cb)=>{
					if( !state.form.template_id || !state.form.template_subordinate_id || !state.form.template_subordinate_variable_id){
						cb('设备模板变量必选')
					}else{
						cb()
					}
				}
			}
		],
		condition:[
			{required:true,message:'触发条件必选'},
			{
				validator:(value,cb)=>{
					if( !state.form.condition ){
						cb('触发条件必选')
					}else if(['2','6'].includes(state.form.condition) && !state.form.condition_parameter.A ){
						cb('请输入A的值')
					}else if(state.form.condition === '3' && !state.form.condition_parameter.B){
						cb('请输入B的值')
					}else if(['4','5'].includes(state.form.condition) && (!state.form.condition_parameter.B || !state.form.condition_parameter.A)){
						cb('请输入A和B的值')
					}else{
						cb()
					}
				}
			}
		],
		linkage_subordinate_variable_id:[
			{required:true,message:'联动变量必选'},
			{
				validator:(value,cb)=>{
					if( !state.form.linkage_subordinate_id || !state.form.linkage_subordinate_variable_id){
						cb('联动变量必选')
					}else{
						cb()
					}
				}
			}
		]
	},
	deviceMould:[],		//设备模板
	slaveList:[],		//设备从机
	deviceData:[],		//设备变量
	linkDeviceData:[],	//联动设备变量
})
async function show(id){
	state.show = true
	
	//获取设备模板
	getDeviceMouldList({page:1,limit:999}).then(res=>{
		state.deviceMould = res.data.list
	})
	let data = null
	if( id ){
		state.loading = true
		let result = await getMouldTriggerDetail({id})
		data = result.data
	}
	state.form = {
		id:id,
		name:data?data.name: "",
		template_id:data?data.template_id:null,						
		template_subordinate_id:data?data.template_subordinate_id:null,			
		template_subordinate_variable_id:data?data.template_subordinate_variable_id:null,	
		condition:data?data.condition+'':'',							
		condition_parameter:{
			A:data?data.condition_parameter.A:"",
			B:data?data.condition_parameter.B:""
		},										
		dead_zone:data?data.dead_zone:"",							
		resume_push:data?data.resume_push:"",							
		alarm_push:data?data.alarm_push:"",							
		is_linkage:data?data.is_linkage:0,							
		is_alarm:data?data.is_alarm:0,							
		linkage_type:data?data.linkage_type:1,							
		linkage_subordinate_id:data?data.linkage_subordinate_id:null,			
		linkage_subordinate_variable_id:data?data.linkage_subordinate_variable_id:null,	
		control_type:data?data.control_type:1,							
		number:data?data.number:"",	
	}
	state.loading = false
}

//获取设备从机列表
function getSlave(){
	state.slaveList = []
	state.deviceData = []
	state.form.template_subordinate_id = null
	state.form.template_subordinate_variable_id = null
	let { template_id } = state.form
	let data = state.deviceMould.find(item=>item.id == template_id)
	if( data && data.subordinate?.length){
		state.slaveList = data.subordinate
		state.form.template_subordinate_id = state.slaveList[0].id
		getDeviceData()
	}
}

//获取设备变量
function getDeviceData(){
	state.deviceData = []
	state.form.template_subordinate_variable_id = null
	let { template_subordinate_id } = state.form
	let data = state.slaveList.find(item=>item.id === template_subordinate_id )
	if( data && data.variable?.length ){
		state.deviceData = data.variable
		state.form.template_subordinate_variable_id = state.deviceData[0].id
	}
}

//切换联动从机
function changeLinkSlave(){
	state.linkDeviceData = []
	state.form.linkage_subordinate_variable_id = null
	let { linkage_subordinate_id } = state.form
	let data = state.slaveList.find(item=>item.id === linkage_subordinate_id )
	if( data && data.variable?.length ){
		state.linkDeviceData = data.variable
		state.form.linkage_subordinate_variable_id = state.linkDeviceData[0].id
	}
}

async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) return false
	let form = JSON.parse(JSON.stringify(state.form))
	if( !form.id ) delete form.id
	let res = await saveMouldTriggerData(form)
	if( res.code ===200 ){
		Message.success("保存成功")
		emits("success")
		return true;
	}
	Message.error(res.msg)
	return false;
}

defineExpose({
	show
})
</script>

<style lang="scss" scoped>
</style>