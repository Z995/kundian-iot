<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/components/CqkdAddGateway.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-29 10:23:43  -->
<template>
<div v-if="state.show">
	<a-drawer title="添加网关" :width="800" v-model:visible="state.show" :on-before-ok="saveData">
		<a-spin :loading="state.loading">
			<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'120px'}" :wrapper-col-props="{flex:1}" v-if="state.show">
				<a-form-item label="网关名称" field="name" :rules="[{required:true,message:'网关名称必填'}]">
					<a-input v-model="state.form.name" placeholder="请输入网关名称" class="w400"></a-input>
				</a-form-item>
				<a-form-item label="MAC / IMEI" field="mac" :rules="[{required:true,message:'请输入MAC / IMEI'}]">
					<a-input v-model="state.form.mac" placeholder="请输入MAC / IMEI" class="w400"></a-input>
				</a-form-item>
				<a-form-item label="网关型号">
					<a-select placeholder="选择网关型号" allow-clear class="w400" v-model="state.form.marque_id">
						<a-option v-for="(item,index) in state.mouldList" :key="index" :value="item.id">
							{{ item.name }}
						</a-option>
					</a-select>
				</a-form-item>
				<a-form-item label="通讯协议">
					<a-radio-group v-model="state.form.type">
						<a-radio :value="0">TCP透传</a-radio>
						<a-radio :value="1">WebSocket</a-radio>
						<a-radio :value="2">MQTT标准协议</a-radio>
					</a-radio-group>
				</a-form-item>
				<a-form-item label="网络类型">
					<a-radio-group v-model="state.form.network">
						<a-radio :value="1">2G/3G/4G/5G</a-radio>
						<a-radio :value="3">WiFi</a-radio>
						<a-radio :value="2">NB-loT</a-radio>
						<a-radio :value="4">以太网</a-radio>
						<a-radio :value="5">其他</a-radio>
					</a-radio-group>
				</a-form-item>
				<a-form-item label="定位方式">
					<a-radio-group v-model="state.form.locate">
						<a-radio :value="1">手动定位</a-radio>
						<a-radio :value="2">自动定位</a-radio>
					</a-radio-group>
				</a-form-item>
				<a-form-item label="网关地址" v-if="state.form.locate==1">
					<div class="w400" v-if="!state.loading">
						<kd-select-map-location :address="state.form.address" :lngLat="state.location" @success="getMapData"></kd-select-map-location>
					</div>
				</a-form-item>
				<a-form-item label="标签" >
					<div class="label-box flex">
						<div class="label-item" v-for="(item,index) in state.label" :key="index">
							{{ item.name }}
							<div class="del-icon" @click="delLabel(index)"><i class="ri-close-line"></i></div>
						</div>
						<a-button type="outline" @click="showLabel">添加标签</a-button>
					</div>
				</a-form-item>
			</a-form>
		</a-spin>
		<cqkd-select-label ref="labelRef" @success="getLabelData"></cqkd-select-label>
	</a-drawer>
</div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import CqkdSelectLabel from '@/components/select/CqkdSelectLabel.vue';
import KdSelectMapLocation from '@/components/KdSelectMapLocation/index.vue'
import { getGatewayDetailData ,getGatewayMouldList,saveGatewayData,getGatewayMac } from '@/api/kdGateway'
import { Message } from '@arco-design/web-vue';
const emits = defineEmits(['success'])
const labelRef = ref()
const formRef = ref()
const state = reactive({
	show:false,
	loading:false,
	form:{
		id:0,
		marque_id:"",	//网关型号
		type:0,		    //通讯协议 0：TCP协议,1：websocket,2：MQTT
		name:"",		//网关名称
		mac:"",			//mac/IMEI
		network:1,		//网络类型 1:2g/3g/4g/5g, 2:wifi,3:NB-loT, 4:以太网, 5:其他
		locate:1,		//定位类型 1：手动 ，2自动
		longitude:"",	//经度
		latitude:"",	//纬度
		address:"",		//网关地址
		label_ids:[],	//网关标签
	},
	location:[],	//经纬度
	label:[],		//回显标签
})
async function show(id){
	state.show = true
	state.loading = true
	let data = null
	if( id ){
		let res = await getGatewayDetailData({id})
		if( res.data){
			data = res.data
		}
	}
	state.form = {
		id:data?data.id : 0,
		marque_id: data?data.marque_id : null,	
		type: data?data.type : 0,	
		name: data?data.name : "",		
		mac: data?data.mac : "",		
		network: data?data.network : 1,		
		locate:data?data.locate: 1,	
		longitude: data?data.longitude : "",	
		latitude: data?data.latitude : "",	
		address: data?data.address : "",		
		label_ids: data && data.label_ids ?data.label_ids.split(',') : "",	
	}
	
	//判断是否有mac ，没有随机生成
	if( !state.form.mac ){
		getGatewayMac().then(res=>{
			state.form.mac = res.data.mac || ''
		})
	}
	
	//经纬度回显
	state.location = data && data.latitude && data.longitude ? [data.longitude,data.latitude] :[],
	//标签回显
	state.label =data && data.label ? data.label : [],
	
	getGatewayMouldList({page:1,limit:100}).then(res=>{
		state.mouldList = res.data?.list || []
	})
	state.loading = false
}
function showLabel(){
	labelRef.value.show(state.form.label_ids)
}

//获取选中的地址信息
function getMapData(data){
	if( data ){
		state.form.longitude = data.location[0]
		state.form.latitude = data.location[1]
		state.form.address = data.address
	}
}

//获取标签信息
function getLabelData(data){
	if( data){
		state.form.label_ids = data.id
		state.label = data.list
	}
}

//删除标签
function delLabel(index){
	state.form.label_ids.splice(index,1)
	state.label.splice(index,1)
}

//保存数据
async function saveData(){
	let valid = await formRef.value.validate()
	if( valid ) return false
	let form = JSON.parse(JSON.stringify(state.form))
	if( !form.id ) delete form.id
	if( form.label_ids.length ){
		form.label_ids = form.label_ids.join(',')
	}
	let res = await saveGatewayData(form)
	if( res.code === 200 ){
		Message.success("保存成功")
		state.show = false
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
</style>