<!-- 坤典智慧农场V6-设备详情 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/deviceDetail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-17 10:20:00  -->
<template>
<kd-page-box :loading="state.loading">
	<div class="kd-content">
		<a-tabs v-model:active-key="state.active" >
			<template #extra>
				<a-button @click="$router.go(-1)">返回</a-button>
			</template>
		    <a-tab-pane key="detail" title="设备详情">
				<div class="device-basic flex">
					<div>
						<div class="fb">设备信息</div>
						<div class="device-basic-left flex mt5">
							<img src="/static/img/device/device-model.png" alt="" class="gateway-icon"/>
							<div class="db-info">
								<div class="db-info-name fb">{{ state.info?.name}}</div>
								<div class="db-info-num mt10">{{state.info?.code}}</div>
								<div class="db-info-desc">
									<div class="db-info-desc-item flex">
										<div class="db-title">设备模板：</div>
										<div class="db-value">{{ state.info?.template?.name || '-'}}</div>
									</div>
									<div class="db-info-desc-item flex">
										<div class="db-title">设备网关：</div>
										<div class="db-value">{{ state.info?.gateway || '-'}}</div>
									</div>
									<div class="db-info-desc-item flex">
										<div class="db-title">设备地址：</div>
										<div class="db-value">{{ state.info?.address || '-'}}</div>
									</div>
									<div class="db-info-desc-item flex">
										<div class="db-title">设备标签：</div>
										<div class="db-value">
											<div class="tag-box flex">
												<a-tag color="blue" v-for="(item,index) in state.info?.label" :key="index">
													{{ item.name }}
												</a-tag>
											</div>
										</div>
									</div>
									<div class="db-info-desc-item flex">
										<div class="db-title">设备描述：</div>
										<div class="db-value">{{ state.info?.describe || '-'}}</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div>
						<div class="fb">设备地图</div>
						<div class="desc-map mt5" v-if="state.info">
							<kd-marker-address :longitude="state.info.longitude" :latitude="state.info.latitude"></kd-marker-address>
						</div>
					</div>
				</div>
				<div class="device-data">
					<div class="fb">变量概况</div>
					<div class="dd-slave flex mt10 mb10">
						<div class="dd-slave-item" :class="{active:state.slave_id ===0 }" @click="changeSlave(0)">
							全部
						</div>
						<div class="dd-slave-item" 
							v-for="(item,index) in state.slaveList" :key="index"
							:class="{active:state.slave_id ===item.id }" 
							@click="changeSlave(item.id)"
						>
							{{item.subordinate?.name}}
						</div>
					</div>
					<a-space>
						<a-select placeholder="全部变量" allow-clear class="w160" v-model="state.data.search.type">
							<a-option :value="1">直采变量</a-option>
							<a-option :value="2">运算型变量</a-option>
							<a-option :value="3">录入型变量</a-option>
						</a-select>
						<a-input placeholder="请输入变量名称" v-model="state.data.search.name" class="w240"></a-input>
						<a-button type="primary" @click="changeSlave(state.slave_id)">查询</a-button>
						<a-button @click="resetData">重置</a-button>
					</a-space>
					<a-table class="kd-small-table mt10" :bordered="false" row-key="id" :data="state.data.list" :columns="[
						{title:'变量ID',dataIndex:'id'},
						{title:'变量名称',dataIndex:'name'},
						{title:'变量类型',slotName:'type'},
						{title:'更新时间',dataIndex:'update_time'},
						{title:'当前值',slotName:'value'},
						{title:'操作',slotName:'action',width:200},
					]" :loading="state.data.loading">
						<template #type="{record}">
							{{constantData.dataType[record.type]}}
						</template>
						<template #value="{record}">
							<!-- data_format = 33 时，是开关 -->
							<div v-if="record.templateVariable.data_format ===33 ">
								<a-switch v-model="record.value" 
									checked-value="1" 
									unchecked-value="0"
									@change="changeSwitchValue(record)"
								></a-switch>
							</div>
							<div v-else>
								<!-- read_write_mode 1只读 2读写 -->
								<div class="data-value">
									<span v-if="record.templateVariable.read_write_mode ===1">
										{{record.value}}
										{{record.templateVariable?.unit}}
									</span>
									<span v-else>
										{{record.value}}{{record.templateVariable?.unit}}
										<i class="ri-edit-line kd-link f20 ml5" @click="showUpdate(record)"></i>
									</span>
								</div>
							</div>
						</template>
						<template #action="{record}">
							<router-link :to="{path:'/statistics/history',query:{
									id:record.id,
									device_id:record.device_id,
									slave_id:record.subordinate_id
									}
								}"
							>
								<a-button type="text" size="mini" class="ml-10">历史查询</a-button>
							</router-link>
							<a-popconfirm content="您将主动采集变量，是否继续?" @ok="activeCollectValue(record.id)">
								<a-button type="text" size="mini">主动采集</a-button>
							</a-popconfirm>
						</template>
					</a-table>
				</div>
			</a-tab-pane>
			<a-tab-pane key="live" title="视频监控">
				<div v-if="state.active ==='live'">
					<cqkd-device-live :list="state.info?.monitor"></cqkd-device-live>
				</div>
			</a-tab-pane>
		</a-tabs>
	</div>
	<cqkd-update-data-value ref="updateValue" @success="changeSlave(state.slave_id)"></cqkd-update-data-value>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import CqkdUpdateDataValue from './components/CqkdUpdateDataValue.vue';
import KdMarkerAddress from '../../components/KdSelectMapLocation/KdMarkerAddress.vue';
import CqkdDeviceLive from './components/detail/CqkdDeviceLive.vue';
import { Modal } from '@arco-design/web-vue';
import { getDeviceDetail,getDeviceVariableList,getDeviceVariableValue,updateDeviceDataValue } from '@/api/kdDevice'
import { useRoute } from 'vue-router';
import constantData from '../../util/common/constantData';
import { Message} from '@arco-design/web-vue'
import { watch } from 'vue';
import { useDeviceStore } from '@/store/device'
const deviceStore = useDeviceStore()
const option = useRoute().query
const updateValue = ref()
const state = reactive({
	loading:false,
	active:'detail',
	slave_id:0,				//当前选择从机id
	slaveList:[],			//从机列表
	data:{					//变量数据
		loading:false,
		list:[],
		search:{
			name:"",
			type:null
		}
	}
})

//监控当前数据采集
watch(()=>deviceStore.state.collectInfo ,(val)=>{
	for (var i = 0; i < state.data.list.length; i++) {
		if( state.data.list[i].id === val.data.variable_id ){
			state.data.list[i].value = val.data.value+''
			state.data.list[i].update_time = val.data.date
			break;
		}
	}
})

onMounted(async ()=>{
	state.loading = true
	await getInfo()
	await changeSlave(0)
})

async function getInfo(){
	let res = await getDeviceDetail({id:option.id})
	state.loading = false
	if( res.code === 200 ){
		state.info = res.data
		state.slaveList = state.info.subordinate
	}
}

//显示修改变量数据
const showUpdate= (data)=>updateValue.value.show({id:data.id,value:data.value})

//主动采集变量
function activeCollectValue(id){
	getDeviceVariableValue({variable_id:id}).then(res=>{
		if( res.code === 200 ){
			Message.success('采集成功')
			changeSlave(state.slave_id)
			return
		}
		Message.error(res.msg)
	})
}

//切换从机变量
function changeSlave(id){
	state.slave_id = id
	state.data.loading = true
	let { name,type } = state.data.search
	let param = id ? {subordinate_id:id} :{device_id:option.id }
	
	if( name ) param.name = name
	if( type ) param.type = type
	getDeviceVariableList(param).then(res=>{
		state.data.loading = false
		state.data.list = res.data.list
	}).catch(()=>{
		state.data.loading = false
	})
}

//重置搜索
function resetData(){
	state.data.search = {
		name:'',
		type:null
	}
	changeSlave(state.slave_id)
}

//修改变量值
function changeSwitchValue(data){
	let param = {
		variable_id:data.id,
		value:data.value
	}
	updateDeviceDataValue(param).then(res=>{
		if( res.code === 200 ){
			Message.success('变量值修改成功')
			return;
		}
		Message.error(res.msg)
	})
}

</script>

<style lang="scss" scoped>
.device-basic{
	width: 100%;
	height: 340px;
	gap:40px;
	.device-basic-left{
		width: 600px;
		height: 300px;
		padding-left: 20px;
		border: 1px solid #f4f4f4;
		
		.gateway-icon{
			width: 120px;
			height: 120px;
			position: relative;
			top: 30px;
		}
		.db-info{
			flex: 1;
			padding: 15px 10px;
			.db-info-num{
				color: #666;
				margin-bottom: 20px;
			}
			.db-info-desc{
				width: 100%;
				border-top: 1px solid #f4f4f4;
				padding-top: 15px;
				.db-info-desc-item{
					width: 100%;
					font-size: 12px;
					padding: 6px 0;
					.db-title{
						width: 80px;
						color: #666;
						flex-shrink: 0;
					}
					.db-value{
						font-weight: 300;
						color: #000;
						letter-spacing: 1px;
					}
				}
			}
			
			.tag-box{
				flex-wrap: wrap;
				gap:10px;
			}
		}
	}
	.desc-map{
		width: 530px;
		height: 300px;
		border: 1px solid #f4f4f4;
	}
}
.device-data{
	width: 100%;
	.dd-slave{
		width: 100%;
		.dd-slave-item{
			padding: 0 24px;
			height: 32px;
			line-height: 32px;
			border: 1px solid #f4f4f4;
			cursor: pointer;
			font-size: 12px;
		}
		.active{
			background: #0066FF;
			color: #fff;
			border: 1px solid #0066FF;
		}
	}
}
.data-value{
	font-size: 18px;
}
</style>