<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/components/detail/CqkdGatewayDetail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-29 11:39:09  -->
<template>
<div class="gateway-info">
	<div class="f15 fb flex-cb">
		<span>网关信息</span>
		<a-space>
			<a-button type="primary">重启网关</a-button>
			<a-dropdown>
				<a-button type="primary">更多<icon-down class="ml5"/></a-button>
					<template #content>
						<a-doption>
							<span @click="showAdd()">编辑网关</span>
						</a-doption>
						<a-doption>删除网关</a-doption>
						<a-doption>在离线记录</a-doption>
					</template>
			</a-dropdown>
		</a-space>
	</div>
	<div class="gi-content flex mt10">
		<div class="gi-left flex">
			<div class="gi-img">
				<img src="/static/img/device/gateway-model.png" alt="" class="gateway1-icon"/>
			</div>
			<div class="gi-info">
				<div class="name mb10">
					<div class="f14 fb">
						<span>{{props.info.name}}</span>
						<a-tag color="#00CC66" size="mini" class="ml10" v-if="props.info.online===1">在线</a-tag>
						<a-tag color="#999" size="mini" class="ml10" v-else>离线</a-tag>
					</div>
					<div class="desc mt10 f12">{{ props.info.mac }}</div>
				</div>
				<div class="other-li flex-c fw">
					<div class="li-title">网关型号：</div><span>{{props.info.marque?.name || '-'}}</span>
				</div>
				<div class="other-li flex-c fw">
					<div class="li-title">协议类型：</div>
					<span v-if="props.info.type ===0">TCP协议</span>
					<span v-if="props.info.type ===1">websocket</span>
					<span v-if="props.info.type ===2">MQTT</span>
				</div>
				<div class="other-li flex-c fw">
					<div class="li-title">网络类型：</div>
					<span v-if="props.info.network ===1">2g/3g/4g/5g</span>
					<span v-if="props.info.network ===2">wifi</span>
					<span v-if="props.info.network ===3">NB-loT</span>
					<span v-if="props.info.network ===4">以太网</span>
					<span v-if="props.info.network ===5">其他</span>
				</div>
				<div class="other-li flex-c fw">
					<div class="li-title">网关地址：</div><span>{{props.info.address}}</span>
				</div>
				<div class="other-li flex-c fw" v-if="props.info.label">
					<div class="li-title">网关标签：</div>
					<div class="tag-box flex">
						<a-tag color="blue" v-for="(item,index) in props.info.label" :key="index">
							{{ item.name }}
						</a-tag>
					</div>
				</div>
			</div>
		</div>
		<div class="gi-map">
			<div class="map-box" id="mapBox"></div>
		</div>
	</div>
	<!-- 添加网关 -->
	<cqkd-add-gateway ref="addRef" @success="refreshPage"></cqkd-add-gateway>
</div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import CqkdAddGateway from '../CqkdAddGateway.vue';
import { useTianDiMap } from '@/util/common/TianDiMap'
const { tianState,initMap ,setMapCenter,addMarker } = useTianDiMap()
const emits = defineEmits(['refresh'])
const props = defineProps({
	info:{
		type:Object,
		default:null
	}
})
const addRef = ref()
const state = reactive({
	
})
onMounted(()=>{
	initMap({
		el:"mapBox",
	},()=>{
		//初始化完成，设置中心点标记
		let { longitude,latitude } = props.info
		if( !longitude || !latitude ){
			setMapCenter(106.557100,29.667460)
		}else{
			setMapCenter(longitude,latitude)
			addMarker(longitude, latitude,{draggable:false})
		}
	})
})

//编辑网关
const showAdd = ()=>addRef.value.show(props.info.id)

//刷新页面
const refreshPage = ()=>emits('refresh')
</script>

<style lang="scss" scoped>
.gateway-info{
	border: 2px solid #f4f4f4;
	padding: 10px 20px;
	.gi-content{
		width: 100%;
		gap:20px;
		.gi-left{
			width: 580px;
			border: 1px solid #f4f4f4;
			height: 260px;
		}
		.gi-img{
			width: 180px;
			height: 220px;
			text-align: center;
			padding-top: 30px;
			.gateway1-icon{
				width: 140px;
			}
		}
		
		.gi-info{
			width: 400px;
			border-left: 1px solid #f4f4f4;
			padding: 20px;
			height: 260px;
			.desc{
				color: #555;
			}
			.name{
				border-bottom: 1px solid #f4f4f4;
				padding-bottom: 20px;
			}
			.other-li{
				width: 100%;
				padding: 8px 0;
				font-size: 12px;
				.li-title{
					width: 70px;
				}
				.tag-box{
					flex-wrap: wrap;
					gap:10px;
				}
			}
		}
		
		.gi-map{
			flex: 1;
			height: 260px;
		}
	}

	.map-box{
		width: 540px;
		height: 257px;
		border: 1px solid #f4f4f4;
	}
}
</style>