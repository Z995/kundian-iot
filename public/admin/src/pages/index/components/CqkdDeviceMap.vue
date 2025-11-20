<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/index/components/CqkdDeviceMap.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-23 14:53:02  -->
<template>
<div class="device-map">
	<div class="device-map-box" id="deviceMapBox"></div>
	<div class="device-list">
		<div class="dl-title flex-cc">
			<img src="/static/img/menu/device.png" class="title-icon mr10" alt="">
			<span>设备列表</span>
		</div>
		<div class="dl-data">
			<div class="data-item flex-cb" v-for="(item,index) in state.info.listData" :key="index"
				@click="showMapMarker(item)"
			>
				<div class="name">{{item.name}}</div>
				<span class="offline" v-if="item.status===0"> ● 离线</span>
				<span class="status" v-else-if="item.status===1"> ● 在线</span>
				<span class="warning" v-else-if="item.is_warning===1"> ● 报警</span>
			</div>
		</div>
		<div class="dl-page" v-if="state.info.count >state.info.limit">
			<kd-pager :pageData="state.info" :event="getList" :simple="true" size="mini" :showJumper="true"></kd-pager>
		</div>
	</div>
	
	<!-- 设备变量信息 -->
	<div class="device-modal" :class="state.device.show ? 'animate__bounceIn device-modal-show':'animate__bounceOut'" >
		<template v-if="state.device.show && state.device.info">
			<div class="close-icon" @click="state.device.show= false"><i class="ri-close-line"></i></div>
			<div class="dm-title">
				<div class="devce-name f14 fb">{{ state.device.info.name }}</div>
				<div class="flex-cb mt10">
					<span>{{state.device.info.code}}</span>
					<router-link :to="{path:'/device/deviceDetail',query:{id:state.device.info.id}}">
						<span class="more">查看详情 <i class="ri-arrow-right-s-line"></i></span>
					</router-link>
				</div>
			</div>
			<div class="dm-salve flex-c">
				<div class="salve-item" 
					:class="{active:state.device.salve_id === item.id}"
					v-for="(item,index) in state.device.info.subordinate" :key="index"
					@click="changeSalve(item.id)"
				>
					{{item.subordinate?.name}}
				</div>
			</div>
			<div class="dm-data">
				<div class="data-list flex-cb" v-for="(item,index) in state.device.data" :key="item.id">
					<div class="dl-name">
						<div class="f12 fb">{{ item.name}}</div>
						<div class="mt10 time">{{item.update_time}}</div>
					</div>
					<div class="dl-val f20">
						{{item.value}} {{item.templateVariable?.unit}}
					</div>
					<div class="pull-icon">
						<i class="ri-refresh-line f20" @click="collectData(item.id)"></i>
					</div>
				</div>
			</div>
		</template>
	</div>
</div>
</template>

<script setup>
import { computed, onMounted, reactive, watch } from 'vue';
import { useTianDiMap } from '@/util/common/TianDiMap'
const { tianState,initMap ,setMapCenter ,addBatchMarker,getCurrentLocation } = useTianDiMap()
import { getDeviceList,getDeviceVariableList,getDeviceVariableValue} from '@/api/kdDevice'
import { Message } from '@arco-design/web-vue';
const state = reactive({
	info:{
		page:1,
		limit:15,
		count:0,
		list:[],
		listData:[],
	},
	device:{
		show:false,
		info:null,
		data:[],
		salve_id:0
	}
})

onMounted(()=>{
	initMap({
		el:'deviceMapBox',
		isSatellite:true
	},()=>{
		getCurrentLocation()		//初始化定位
		//初始化完成
		getDevice()
	})
})

function getDevice(){
	let param = {
		page:1,
		limit:999
	}
	getDeviceList(param).then(res=>{
		state.info.list = res.data.list
		state.info.count = res.data.count
		getList(1,state.info.limit)
		
		//绘制设备标记
		addBatchMarker(state.info.list,{isClick:true},(markerEvent)=>{
			console.log(markerEvent);
			state.device.show = true
			state.device.info = markerEvent
			if( state.device.info.subordinate.length ){
				changeSalve(state.device.info.subordinate[0].id)
			}
		})
	})
}

function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	// 计算起始索引
	const startIndex = (state.info.page - 1) * state.info.limit;
	// 计算结束索引
	const endIndex = startIndex + state.info.limit;
	// 使用slice方法获取当前页的数据
	state.info.listData = state.info.list.slice(startIndex, endIndex);
}

//点击左侧设备，定位到当前设备
const showMapMarker = (data)=>{
	if( !data.longitude || !data.latitude ){
		Message.warning("当前设备未设置定位信息")
		return false;
	}
	setMapCenter(data.longitude,data.latitude)
}

//切换从机
function changeSalve(id){
	state.device.salve_id = id
	getDeviceData(id)
}

//获取从机变量数据
function getDeviceData(id){
	getDeviceVariableList({subordinate_id:id}).then(res=>{
		state.device.data = res.data.list
	})
}

function collectData(id){
	getDeviceVariableValue({variable_id:id}).then(res=>{
		if( res.code === 200 ){
			Message.success('采集成功')
			changeSalve(state.device.salve_id)
			return
		}
		Message.error(res.msg)
	})
}

</script>

<style lang="scss" scoped>
.device-map{
	width: 100%;
	height: calc(100% - 26px);
	position: relative;
	border-radius: 8px;
	overflow: hidden;
	background: #fff;
	padding: 8px;
	.device-map-box{
		width: 100%;
		height: 100%;
		border-radius: 8px;
	}
	.device-list{
		position: absolute;
		width: 200px;
		height: calc(100% - 40px);
		background: rgba(#000,.7);
		top: 20px;
		left: 20px;
		z-index: 9999;
		border-radius: 8px;
		overflow: hidden;
		.dl-title{
			width: 100%;
			height: 40px;
			background: rgba(#000,.7);
			color: #fff;
			font-size: 13px;
			.title-icon{
				width: 14px;
				position: relative;
				top: -2px;
			}
		}
		.dl-page{
			width: 100%;
			height: 40px;
			color:#fff;
		}
		.dl-data{
			width: 100%;
			height: calc(100% - 80px);
			overflow: hidden;
			overflow-y: auto;
			&::-webkit-scrollbar{
				display: none;
			}
			.data-item{
				width: 100%;
				padding: 0 15px;
				color: #fff;
				height: 36px;
				font-size: 11px;
				letter-spacing: 1px;
				cursor: pointer;
				font-weight: 300;
				&:hover{
					background: rgba(#fff,.2);
				}
				.status{
					font-size: 10px;
					color: #00CC66;
				}
				.offline{
					font-size: 10px;
					color: #cbcbcb;
				}
				.warning{
					font-size: 10px;
					color: #FF6633;
				}
				.name{
					width: 72%;
					white-space: nowrap;
					overflow: hidden;
					text-overflow: ellipsis;
				}
			}
			.active{
				background: rgba(#fff,.2);
			}
		}
	}
	
	.device-modal{
		display: none;
		position: absolute;
		width: 500px;
		height: 400px;
		background: rgba(#000,.7);
		top:100px;
		left: calc( (100% - 500px)/2 );
		z-index: 9999;
		border-radius: 8px;
		.close-icon{
			width: 24px;
			height: 24px;
			border-radius: 50%;
			right: -8px;
			top:-8px;
			position: absolute;
			cursor: pointer;
			font-size: 20px;
			text-align: center;
			background: #3366FF;
			color: #fff;
			
		}
		.dm-title{
			width: 100%;
			height: 70px;
			background: rgba(#000,.7);
			color: #fff;
			padding: 15px;
			font-size: 12px;
			letter-spacing: 1px;
			.more{
				color: #3366FF;
				cursor: pointer;
			}
		}
		.dm-salve{
			width: 100%;
			height: 40px;
			flex-shrink: 0;
			.salve-item{
				padding: 0 10px;
				color: #fff;
				height: 100%;
				line-height: 40px;
				font-size: 12px;
				cursor: pointer;
			}
			.active{
				background: rgba(#fff,.4);
				font-size: 13px;
				font-weight: bold;
			}
		}
		.dm-data{
			width: 100%;
			height: calc(100% - 110px);
			overflow: hidden;
			overflow-y: auto;
			&::-webkit-scrollbar{
				display: none;
			}
			
			.data-list{
				width: 100%;
				height: 60px;
				padding: 0 20px;
				color: #fff;
				cursor: pointer;
				&:hover{
					background: rgba(#fff,.2);
				}
				.time{
					color: #b3b3b3;
					font-size: 10px;
				}
			}
		}
	}
	
	.device-modal-show{
		display: block;
	}
}
:deep(.dl-page .arco-pagination-simple .arco-pagination-jumper){
	color: #fff;
}
</style>