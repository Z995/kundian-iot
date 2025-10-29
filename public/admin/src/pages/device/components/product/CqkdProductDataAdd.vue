<!-- 坤典物联 -  -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/components/product/CqkdProductDataAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-18 14:10:41  -->
<template>
<div v-if="state.show">
	<a-modal title="新增/编辑变量信息" v-model:visible="state.show" width="700px" :on-before-ok="saveData">
		<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'100px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="变量排序" >
				<a-input-number placeholder="请输入变量排序" v-model="state.form.sort"></a-input-number>
			</a-form-item>
			<a-form-item label="变量名称" field="name" :rules="[{required:true,message:'变量名称必填'}]">
				<a-input placeholder="请输入变量名称" v-model="state.form.name" ></a-input>
			</a-form-item>
			<a-form-item label="变量单位" >
				<a-input placeholder="请输入变量单位" v-model="state.form.unit"></a-input>
			</a-form-item>
			<a-form-item label="变量类型" field="type" :rules="[{required:true,message:'变量类型必选'}]">
				<a-radio-group v-model="state.form.type">
					<a-radio :value="parseInt(index)" v-for="(item,index) in constantData.dataType">
						{{ item}}
					</a-radio>
				</a-radio-group>
			</a-form-item>
			<div  v-if="state.type ===2">
				<a-form-item label="寄存器">
					<div>
						<div class="flex-c">
							<a-select placeholder="请选择寄存器" class="w160" v-model="state.form.register_mark" @change="changeRegisterMark">
								<a-option value="do">DO(Coils Status)</a-option>
								<a-option value="di">DI(Discrete Inputs)</a-option>
								<a-option value="ai">AI(Analog input)</a-option>
								<a-option value="ao">AO(Analog output)</a-option>
							</a-select>
							<div>
								<a-input placeholder="寄存器地址" v-model="state.form.register_address" class="w160 ml20"
									@change="changeRegisterMark"
								></a-input>
								<span class="ml20" style="color: #00CC66;">
									<span v-if="state.form.register_mark===0">DO</span>
									<span v-if="state.form.register_mark===1">DI</span>
									<span v-if="state.form.register_mark===3">AI</span>
									<span v-if="state.form.register_mark===4">AO</span>
									00{{state.register_value}}(位)
								</span>
							</div>
						</div>
						<div class="tips mt5" style="padding-left: 180px;">
							寄存器地址范围1-50
						</div>
					</div>
				</a-form-item>
				<a-form-item label="数据格式" field="data_format" :rules="[{required:true,message:'数据格式必填'}]">
					<a-select placeholder="请选择数据格式" v-model="state.form.data_format">
						<a-option :value="33">位</a-option>
					</a-select>
				</a-form-item>
			</div>
			<div v-else>
				<a-form-item label="寄存器" >
					<div>
						<div class="flex-c">
							<a-select placeholder="请选择寄存器" class="w160" v-model="state.form.register_mark" @change="changeRegisterMark">
								<a-option value="0">0(Coils Status)</a-option>
								<a-option value="1">1(Discrete Inputs)</a-option>
								<a-option value="3">3(Input Registers)</a-option>
								<a-option value="4">4(Holding Registers)</a-option>
							</a-select>
							<div>
								<a-input placeholder="寄存器地址" v-model="state.form.register_address" class="w160 ml20"
									@change="changeRegisterMark"
								></a-input>
								<span class="ml20 f12" style="color: #00CC66;">
									{{state.register_value}}({{constantData.dataFormat[state.form.data_format]}})
								</span>
							</div>
						</div>
						<div class="tips mt5 " style="padding-left: 180px;">
							寄存器地址范围1-65536
						</div>
					</div>
				</a-form-item>
				<a-form-item label="数据格式" field="data_format" :rules="[{required:true,message:'数据格式必填'}]">
					<a-select placeholder="请选择数据格式" v-model="state.form.data_format">
						<a-option :value="parseInt(index)" v-for="(item,index) in constantData.dataFormat" :key="index">
							{{ item }}
						</a-option>
					</a-select>
				</a-form-item>
			</div>
			<a-form-item label="采集频率" field="collect_frequency" :rules="[{required:true,message:'采集频率必填'}]">
				<a-select placeholder="请选择采集频率" v-model="state.form.collect_frequency">
					<a-option :value="parseInt(index)" v-for="(item,index) in constantData.collectFrequency" :key="index">
						{{ item }}
					</a-option>
				</a-select>
			</a-form-item>
			<a-form-item label="数字格式" field="fraction" 
				:rules="[{required:true,message:'数字格式必填'}]" 
				v-if="state.form.dataType !=='bit'"
			>
				<a-select placeholder="请选择数字格式" v-model="state.form.fraction">
					<a-option :value="0">整数</a-option>
					<a-option :value="1">保留1位小数</a-option>
					<a-option :value="2">保留2位小数</a-option>
					<a-option :value="3">保留3位小数</a-option>
					<a-option :value="4">保留4位小数</a-option>
				</a-select>
			</a-form-item>
			<a-form-item label="存储方式" field="storage_mode" :rules="[{required:true,message:'存储方式必填'}]">
				<a-radio-group v-model="state.form.storage_mode">
					<a-radio :value="2">全部存储</a-radio>
					<a-radio :value="1">变化存储
						<a-tooltip content="数据有变化的时候存储 。">
							<i class="ri-question-line ri-top2"></i>
						</a-tooltip>
					</a-radio>
				</a-radio-group>
			</a-form-item>
			<a-form-item label="读写方式" field="read_write_mode">
				<a-radio-group v-model="state.form.read_write_mode">
					<a-radio :value="1">只读</a-radio>
					<a-radio :value="2" :disabled="['1','3'].includes(state.form.register_mark)">读写</a-radio>
				</a-radio-group>
			</a-form-item>
			<a-form-item>
				<span class="kd-link f13" @click="state.more =!state.more">
					高级选项
					<icon-down v-if="!state.more" />
					<icon-up v-else/>
				</span>
			</a-form-item>
			<template v-if="state.more">
				<a-form-item>
					<template #label>
						<span>采集公式</span>
						<a-popover>
						    <icon-question-circle-fill />
						    <template #content>
								<div class="f12">
									<div>设备上行数据经采集公式计算后显示 。</div>
									<div>公式中的%s为占位符，是固定字段 。</div>
									<div>如： </div>
									<div>加：%s+10</div>
									<div>减：%s-10</div>
									<div>乘：%s*10</div>
									<div>除：%s/10</div>
									<div>余数：%s%10</div>
								</div>
						    </template>
						</a-popover>
					</template>
					<a-input placeholder="请输入采集公式" v-model="state.form.collect_formula"></a-input>
				</a-form-item>
				<a-form-item>
					<template #label>
						<span>控制公式</span>
						<a-popover>
						    <icon-question-circle-fill />
						    <template #content>
								<div class="f12">
									<div>主动向设备写数据经控制公式计算后下发 。</div>
									<div>公式中的%s为占位符，是固定字段 。</div>
									<div>如： </div>
									<div>加：%s+10</div>
									<div>减：%s-10</div>
									<div>乘：%s*10</div>
									<div>除：%s/10</div>
									<div>余数：%s%10</div>
								</div>
						    </template>
						</a-popover>
					</template>
					<a-input placeholder="请输入控制公式" v-model="state.form.contro_formula"></a-input>
				</a-form-item>
			</template>
		</a-form>
	</a-modal>
</div>
</template>

<script setup>
import { reactive,ref } from 'vue';
import constantData from '@/util/common/constantData';
const emits = defineEmits(['success'])
const formRef = ref()
const state = reactive({
	show:false,
	type:1,		//1 Modbus RTU协议 2其他协议
	form:{
		id:0,
		sort:99,
		name:'',				//变量名称
		unit:'',				//单位
		type:1,					//变量类型 1直采变量 2运算变量 3录入变量
		register_mark:'0',		//寄存器标识 [0,1,2,3,4]
		register_address:"",	//寄存器地址
		data_format:1,			//数据格式
		collect_frequency:3,	//采集频率
		fraction:0,				//数字格式 1:一位,2:二位,3:三位,4:四位,
		storage_mode:2,			//储存方式 1变化存储 2全部存储
		read_write_mode:1,		//读写模式1只读，2读写
		collect_formula:"",		//采集公式
		contro_formula:"",		//控制公式
	},
})

function show(data,protocol){
	state.show = true
	state.title = data ?'编辑变量':'新增变量'
	state.type = protocol ||1
	
	if( data ){
		state.form = {
			...data
		}
		state.register_address = data.register_address
		state.form.register_address = parseInt(data.register_address)
	}else{
		state.form = {
			id:0,
			name:'',				//变量名称
			sort:99,				//变量名称
			unit:'',				//单位
			type:1,					//变量类型 1直采变量 2运算变量 3录入变量
			register_mark:'0',		//寄存器标识 [0,1,2,3,4]
			register_address:"",	//寄存器地址
			data_format:1,			//数据格式
			collect_frequency:3,	//采集频率
			fraction:0,				//数字格式 1:一位,2:二位,3:三位,4:四位,
			storage_mode:2,			//储存方式 1变化存储 2全部存储
			read_write_mode:1,		//读写模式1只读，2读写
			collect_formula:"",		//采集公式
			contro_formula:"",		//控制公式
		}
		if( protocol ===2 && !data){
			state.form.register_mark = "do"
			state.form.data_format = 33
		}
	}
	changeRegisterMark()
}
//保存数据
async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) {
		return false
	}
	let form = JSON.parse(JSON.stringify(state.form))
	if( !form.id ) delete form.id
	form.register_address = state.register_address
	if( parseInt(state.register_value) > 65536  ){
		Message.warning("寄存器地址错误")
		return false
	}
	emits('success',form)
	return true
}

//改变寄存器标志
function changeRegisterMark(){
	let { register_mark,register_address } = state.form
	if( state.type ===1 ){
		let len = 4-register_address.length , str = ''
		if( len > 0 ){
			for (var i = 0; i < len; i++) {
				str+='0'
			}
		}
		state.register_value = state.form.register_mark +str+register_address
		state.register_address = str+register_address
	}else{
		state.register_value = register_address
	}
}

defineExpose({
	show
})
</script>

<style>
</style>