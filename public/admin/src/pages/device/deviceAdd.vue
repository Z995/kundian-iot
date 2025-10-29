<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/deviceAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 11:48:53  -->
<template>
<kd-page-box :loading="state.loading">
	<div class="kd-content">
		<kd-back title="新增设备"></kd-back>
		<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'110px'}" :wrapper-col-props="{flex:1}">
			<div class="device-add-box">
				<div class="f13 mb20">基本信息</div>
				<a-form-item label="设备名称" field="name" :rules="[{required:true,message:'设备名称必填'}]">
					<a-input v-model="state.form.name" placeholder="请输入设备名称" class="w600"></a-input>
				</a-form-item>
				<a-form-item label="设备描述">
					<a-input v-model="state.form.describe" placeholder="请输入设备描述" class="w600"></a-input>
				</a-form-item>
				<a-form-item label="设备标签">
					<div class="label-box flex">
						<div class="label-item" v-for="(item,index) in state.label" :key="index">
							{{ item.name }}
							<div class="del-icon" @click="delLabel(index)"><i class="ri-close-line"></i></div>
						</div>
						<a-button type="outline" @click="showLabel">添加标签</a-button>
					</div>
				</a-form-item>
				<a-form-item label="设备位置">
					<a-radio-group v-model="state.form.location">
						<a-radio :value="2">网关定位</a-radio>
						<a-radio :value="1">手动定位</a-radio>
					</a-radio-group>
				</a-form-item>
				<a-form-item label="设备地图" v-if="state.form.location===1">
					<div class="w600">
						<div class="w400" v-if="!state.loading">
							<kd-select-map-location :lngLat="state.location" @success="getMapData"></kd-select-map-location>
						</div>
					</div>
				</a-form-item>
			</div>
			<div class="device-add-box mt20">
				<div class="f13 mb20">数据设置</div>
				<a-form-item label="关联设备模板">
					<div>
						<div>
							<span v-if="state.form.template_id && state.mouldInfo">
								<span>{{ state.mouldInfo.name}}</span>
								<a-popconfirm content="确认请删除所选模板数据吗？" @ok="clearTemplate">
									<span class="kd-link ml20 mr20">删除</span>
								</a-popconfirm>
							</span>
							<a-button type="outline" @click="showSelectMould">选择模板</a-button>
						</div>
						<div class="cong-ji-box mt10" v-if="state.mouldInfo">
							<div class="cj-item flex-c mt10" v-for="(item,index) in state.mouldInfo.subordinate" :key="index">
								<div class="cj-name fb f12">{{ item.name}}</div>
								<div class="cj-content flex-c ml20">
									<span>从机地址：</span>
									<a-input v-model="item.subordinate_address" placeholder="从机地址" class="w200"></a-input>
								</div>
							</div>
						</div>
					</div>
				</a-form-item>
			</div>
			<div class="device-add-box mt20 mb20">
				<div class="f13 mb20">联网设置</div>
				<a-form-item label="管理网关" v-model="state.form.gateway_id">
					<a-select class="w600" placeholder="请选择网关" allow-clear
						v-model="state.form.gateway_id"
						@search="getGatewayList" :filter-option="true" :allow-search="true">
						<a-option v-for="(item,index) in state.gatewayList" :value="item.id" :key="index">
						  {{item.name}}
						</a-option>
					</a-select>
				</a-form-item>
			</div>
			<div class="device-add-box mt20 mb20">
				<div class="f13 mb20">绑定监控</div>
				<a-form-item label="选择监控" >
					<div>
						<a-table class="w600 kd-small-table" :pagination="false" :data="state.live" :columns="[
							{title:'ID',dataIndex:'id'},
							{title:'监控名称',slotName:'name'},
							{title:'操作',slotName:'action'},
						]">
							<template #name="{record}">
								<div v-if="record.related_type ==='local'">
									<div>{{record.name}}</div>
								</div>
								<div v-else-if="record.related_type ==='kun_dian'">
									<div>{{record.desc || record.name }}</div>
								</div>
								<div v-else>
									<div>{{ record.name }}</div>
								</div>
							</template>
							<template #action="{record,rowIndex}">
								<a-button type="text" size="mini" class="ml-12" @click="delLive(rowIndex)">删除</a-button>
							</template>
						</a-table>
						<div class="w600 add-live" @click="showLive">
							<i class="ri-add-line"></i>添加一个
						</div>
					</div>
				</a-form-item>
			</div>
			<a-form-item>
				<a-button type="primary" class="w120 ml20" :loading="state.saving" @click="saveData">
					保存
				</a-button>
			</a-form-item>
		</a-form>
	</div>
	<kd-select-device-mould ref="mouldRef" @success="getMould"></kd-select-device-mould>
	<cqkd-select-label ref="labelRef" @success="getLabelData"></cqkd-select-label>
	<cqkd-select-live ref="liveRef" @select="getBindLiveData"></cqkd-select-live>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import KdSelectDeviceMould from './components/mould/KdSelectDeviceMould.vue';
import KdDeviceLabel from "./components/CqkdDeviceLabel.vue"
import KdSelectMapLocation from '@/components/KdSelectMapLocation/index.vue'
import CqkdSelectLabel from '@/components/select/CqkdSelectLabel.vue';
import CqkdSelectLive from '../../components/select/CqkdSelectLive.vue';
import { useRoute } from 'vue-router';
import { getGatewayListData } from '@/api/kdGateway'
import { getDeviceDetail,saveDeviceData } from '@/api/kdDevice'
import { Message } from '@arco-design/web-vue';
import router from '../../router';
const mouldRef = ref()
const labelRef = ref()
const formRef = ref()
const liveRef = ref()
const option = useRoute().query
const state = reactive({
	loading:false,
	saving:false,
	form:{
		id:0,
		gateway_id:null,	//设备网关
		template_id:null,	//设备模板
		name:"",			//设备名称
		describe:"",		//设备描述
		label_ids:[],		//设备标签
		location:2,			//定位方式 1手动定位 2网关定位
		longitude:"",		//经度
		latitude:"",		//纬度
		monitor_ids:"",		//绑定监控
		subordinate:[],		//从机
	},
	location:[],			//定位经纬度
	label:[],				//选中的标签
	gatewayList:[],			//网关数据
	mouldInfo:null,			//设备模板数据
	template:null,			//回显的模板数据
	
	live:[],				//绑定的监控数据
})
onMounted(async()=>{
	state.loading = true
	getGatewayList()
	let data = null
	if( option.id ){
		let res2 = await getDeviceDetail({id:option.id})
		if( res2.data?.id ) data = res2.data
	}
	state.form = {
		id:data?data.id : 0,
		gateway_id:data?data.gateway_id : null,	
		template_id:data?data.template_id : null,	
		name:data?data.name : "",			
		describe:data?data.describe : "",		
		monitor_ids:data?data.monitor_ids : "",		
		label_ids: [],		
		location:data?data.location : 2,			
		longitude:data?data.longitude : "",		
		latitude:data?data.latitude : "",		
		subordinate: [],		
	}
	if( data ){
		state.mouldInfo = {
			name:data.template?.name || '',
			subordinate:[],
		}
		if( data.label.length ) state.label = data.label
		if( data.location ===1 ) state.location = [data.longitude,data.latitude]
		if( data.label_ids && typeof data.label_ids ==='string'){
			state.form.label_ids = data.label_ids.split(',')
		}
		
		//从机数据回显
		if( data?.subordinate.length ){
			let arr = []
			data.subordinate.forEach(item=>{
				arr.push({
					template_subordinate_id:item.template_subordinate_id,
					name:item.subordinate?.name,
					serial_port:item.serial_port,			
					subordinate_address:item.subordinate_address,	
					protocol:item.protocol,	
				})
			})
			state.mouldInfo.subordinate = arr
		}
		state.template = data.template || null
		
		if( data.monitor ) state.live = data.monitor
		
	}
	state.loading = false
})

//显示选择模板弹框
const showSelectMould = ()=> mouldRef.value.open(state.template)
//显示选择标签
const showLabel = ()=>labelRef.value.show(state.form.label_ids)
//显示选择监控
const showLive = ()=>liveRef.value.show()

//获取标签信息
function getLabelData(data){
	if( data){
		state.form.label_ids = data.id
		state.label = data.list
	}
}
//获取网关列表数据
function getGatewayList(value){
	let param = {page:1,limit:20}
	if( value ) param.name = value
	getGatewayListData(param).then(res=>{
		state.gatewayList = res.data?.list || []
	})
}

//获取选中的地址信息
function getMapData(data){
	if( data ){
		state.form.longitude = data.location[0]
		state.form.latitude = data.location[1]
	}
}

//获取设备模板数据
function getMould(data){
	state.form.template_id = data.template_id
	let subordinate = []
	data.data.forEach(item=>{
		subordinate.push({
			template_subordinate_id:item.subordinate_id,
			protocol:item.protocol,	
			name:item.name,
			serial_port:"",			//串口序号
			subordinate_address:"",	//从机地址
		})
	})
	state.mouldInfo = {
		name:data.template_name,
		subordinate:subordinate
	}
}

//清除所选模板数据
function clearTemplate(){
	state.form.template_id = null
	state.form.subordinate = []
	state.mouldInfo = null
}

//获取绑定的监控信息
function getBindLiveData(data){
	console.log(data);
	state.live = state.live.concat(data.list)
}

//删除绑定的监控
function delLive(index){
	state.live.splice(index,1)
}

//保存数据
async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) return false
	let form = JSON.parse(JSON.stringify(state.form))
	if( !form.id ) delete form.id
	if( state.mouldInfo ){
		let list = []
		state.mouldInfo.subordinate.forEach(item=>{
			list.push({
				serial_port:item.serial_port,
				subordinate_address:item.subordinate_address,
				template_subordinate_id:item.template_subordinate_id,
			})
		})
		form.subordinate = list
	}
	if( form.label_ids && form.label_ids.length){
		form.label_ids = form.label_ids.join(',')
	}else{
		form.label_ids = ""
	}
	
	//组装监控数据
	if( state.live && state.live.length ){
		let monitor_ids = []
		state.live.forEach(item=>{
			monitor_ids.push(item.id)
		})
		form.monitor_ids = monitor_ids.join(',')
	}
	
	state.saving = true
	try{
		let res = await saveDeviceData(form)
		state.saving = false
		if( res.code === 200 ){
			Message.success("保存成功")
			setTimeout(function(){
				router.go(-1)
			},500)
			return
		}
		Message.error(res.msg)
	}catch(e){
		console.log('js错误:',e);
	}
}
</script>

<style lang="scss" scoped>
.device-add-box{
	padding: 20px 20px 0 20px;
	border: 1px solid #f4f4f4;
	
	.cong-ji-box{
		width: 100%;
		max-height: 240px;
		overflow: hidden;
		overflow-y: auto;
		.cj-item{
			width: 100%;
			.cj-name{
				width: 120px;
			}
			.cj-content{
				color: #666;
				font-size: 13px;
			}
		}
	}
	.label-box{
		width: 100%;
		flex-wrap: wrap;
		gap:15px;
		
		.label-item{
			padding: 6px 10px 6px 15px;
			background: #efefef;
			border-radius: 3px;
			color: #777;
			cursor: pointer;
			position: relative;
			.del-icon{
				display: inline-block;
				width: 16px;
				height: 16px;
				border-radius: 50%;
				text-align: center;
				font-size: 14px;
				line-height: 16px;
				margin-left: 2px;
				color:#999;
				cursor: pointer;
				&:hover{
					background: #cecece;
					color: #555;
				}
			}
		}
	}
	
	.add-live{
		width: 100%;
		text-align: center;
		margin-top: 15px;
		cursor: pointer;
		color: #3366FF;
	}

}
</style>