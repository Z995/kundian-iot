<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/mouldAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-30 10:52:45  -->
<template>
<kd-page-box :loading="state.loading">
	<div class="kd-content mould-add">
		<kd-back title="新增网关型号">
			<a-button type="primary" :loading="state.saving" @click="saveData">保存</a-button>
		</kd-back>
		<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'140px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="网关型号" field="name" :rules="{required:true,message:'网关型号必填'}">
				<a-input placeholder="请输入网关型号" v-model="state.form.name" class="w600"></a-input>
			</a-form-item>
			<a-form-item label="型号功能">
				<a-table class="w600 kd-small-table" row-key="id" :pagination="false" :data="state.form.instruct" :columns="[
					{title:'功能名称',slotName:'name',width:200},
					{title:'AT指令',slotName:'command_str'},
					{title:'附加参数',slotName:'type'},
					{title:'操作',slotName:'action'},
				]"
				>
					<template #name="{record,rowIndex}">
						<a-select placeholder="请选择指令" v-model="record.instruct_id" class="w180">
							<a-option v-for="(item,index) in state.commandList" :key="index" :value="item.id">
								{{ item.name}}
							</a-option>
						</a-select>
						
					</template>
					<template #command_str="{record}">
						<a-input v-model="record.command" placeholder="请输入附加参数"></a-input>
					</template>
					<template #type="{record}">
						<div v-if="record.instruct_id && isParam(record.instruct_id)">
							<a-input v-model="record.parameter" placeholder="请输入附加参数"></a-input>
						</div>
						<a-tag color="blue" v-else>无参数</a-tag>
					</template>
					<template #action="{record,rowIndex}">
						<a-button type="text" size="mini" class="ml-10" @click="addOrDel(rowIndex)">删除</a-button>
					</template>
					<template #footer>
						<div class="add-line" @click="addOrDel(-1)"><i class="ri-add-line"></i>新增指令</div>
					</template>
				</a-table>
			</a-form-item>
		</a-form>
	</div>
</kd-page-box>
</template>
<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import AT_LIST from '../../assets/AT';
import { getGatewayCommandList ,saveGatewayMouldData,getGatewayMouldDetail } from '@/api/kdGateway'
import { Message } from '@arco-design/web-vue';
import router from '../../router';
import { useRoute } from 'vue-router';
const option =useRoute().query
const formRef = ref()
const state = reactive({
	loading:false,
	form:{
		id:0,
		name:"",
		instruct:[
			{instruct_id:null,command:"",parameter:""},
		],
	},
	commandList:[],
	saving:false,
})

//判断功能是否有参数
const isParam = computed(()=>{
	return function (instruct_id){
		let index = state.commandList.findIndex(item=>item.id == instruct_id )
		return state.commandList[index].type ===1 ? false :true 
	}
})

onMounted(async ()=>{
	state.loading = true
	let res = await getGatewayCommandList()
	if( res.code === 200 ){
		state.commandList = res.data?.list || []
	}
	if( option.id ){
		let result = await getGatewayMouldDetail({id:option.id})
		if( result.code === 200 ){
			let {id ,name ,instruct } = result.data
			
			//过滤一下返回的源数据
			let commandList = []
			instruct.forEach(item=>{
				commandList.push({
					instruct_id:item.instruct_id,
					command:item.command,
					parameter:item.parameter,
					id:item.id
				})
			})
			state.form = {
				id:id,
				name:name,
				instruct:commandList,
			}
		}
	}
	state.loading = false
})

//新增，删除指令
function addOrDel(index){
	if( index === -1 ){
		state.form.instruct.push({instruct_id:null,command:"",parameter:""})
		return false;
	}
	state.form.instruct.splice(index,1)
}

//保存型号
async function saveData(){
	let valid =await formRef.value.validate()
	console.log('valid',valid);
	if( valid ) return false
	let form = JSON.parse(JSON.stringify(state.form))
	if( !form.id ) delete form.id
	//验证型号功能内容是否填写完整
	let flag = false
	for (var i = 0; i < form.instruct.length; i++) {
		if( !form.instruct[i].instruct_id || !form.instruct[i].command ){
			flag = true
			break
		}
	}
	if( flag ){
		Message.warning("请将型号功能内容填写完整后再提交!")
		return false
	}
	//保存操作
	state.saving = true
	try{
		let res = await saveGatewayMouldData(form)
		state.saving = false
		if( res.code == 200 ){
			Message.success("添加成功")
			setTimeout(function(){
				router.back()
			},500)
			return
		}
		Message.error(res.msg)
	}catch(e){
		state.saving = false
	}
}
</script>

<style lang="scss" scoped>
.mould-add{
	:deep(.arco-card-header){
		background: #f2f3f5;
	}
	.mould-item{
		width: 100%;
		background: #f8f8f8;
		padding: 15px 0 1px 0;
	}
	.add-line{
		width: 100%;
		display: flex;
		justify-content: center;
		color: #0066FF;
		cursor: pointer;
	}
}

</style>