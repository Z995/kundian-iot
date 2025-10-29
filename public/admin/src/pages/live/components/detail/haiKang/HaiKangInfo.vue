<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/haiKang/HaiKangDetail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 10:41:20  -->
<template>
<div>
	<div class="hk-box">
		<div class="hk-info">
			<div class="f16 fb">设备基本信息</div>
			<div class="hk-item flex-c">
				<div class="hk-title">设备名称</div>
				<div class="hk-value">
					<span>{{props.info.displayName}}</span>
					<a-button type="text" size="mini" @click="showUpdateName">修改</a-button>
				</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">设备序列号</div>
				<div class="hk-value">{{props.info.fullSerial}}</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">设备在线状态</div>
				<div class="hk-value">
					<a-tag v-if="props.info.status===1" color="#00CC66" size="mini">在线</a-tag>
					<a-tag v-else color="#999" size="mini">离线</a-tag>
				</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">是否支持wifi</div>
				<div class="hk-value">
					<span v-if="props.info.supportWifi===0">不支持</span>
					<span v-if="props.info.supportWifi===1">支持</span>
					<span v-if="props.info.supportWifi===2">支持带userId的新的wifi配置方式</span>
					<span v-if="props.info.supportWifi===3">支持smartwifi</span>
				</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">设备协议版本</div>
				<div class="hk-value">{{props.info.releaseVersion}}</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">设备真实版本号</div>
				<div class="hk-value">{{props.info.version}}</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">是否支持云存储</div>
				<div class="hk-value">
					<span v-if="props.info.supportCloud ===0">不支持</span>
					<span v-if="props.info.supportCloud ===1">支持</span>
				</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">设备首次上线时间</div>
				<div class="hk-value">{{props.info.createTime}}</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">设备位置</div>
				<div class="hk-value">{{props.info.address || '-'}}</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">信号强度(%)</div>
				<div class="hk-value">{{props.info.signal }}</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">设备IP地址</div>
				<div class="hk-value">{{props.info.netAddress }}</div>
			</div>
			<div class="hk-item flex-c">
				<div class="hk-title">网络类型</div>
				<div class="hk-value">{{props.info.netType }}</div>
			</div>
		</div>
		<div class="hk-address">
			<kd-marker-address></kd-marker-address>
		</div>
	</div>
	<div class="f16 fb mt10">设备状态信息</div>
	<div class="hk-status flex mt10">
		<div class="hk-status-item" v-for="(item,index) in props.statusList" :key="index">
			<div class="si-val">{{ item.value_txt}}</div>
			<div class="si-title">{{ item.name }}</div>
		</div>
	</div>
	<!-- 修改设备名称 -->
	<cqkd-update-device-name ref="nameRef"></cqkd-update-device-name>
</div>
</template>

<script setup>
import KdMarkerAddress from '@/components/KdSelectMapLocation/KdMarkerAddress.vue';
import CqkdUpdateDeviceName from '../CqkdUpdateDeviceName.vue';
import { reactive,ref } from 'vue';
const nameRef = ref()
const props = defineProps({
	info:{
		type:Object,
		default:null
	},
	statusList:{
		type:Array,
		default:[]
	}
})
	
const state = reactive({
})
//修改设备名称
const showUpdateName = ()=>nameRef.value.show()
</script>

<style lang="scss" scoped>
.hk-box{
	width: 100%;
	display: flex;
	justify-content: space-between;
}
.hk-info{
	width: 600px;
	.hk-item{
		width: 100%;
		height: 38px;
		align-items: center;
		font-size: 12px;
		.hk-title{
			width: 140px;
			font-weight: 200;
			color: #767575;
			letter-spacing: 1px;
		}
		.hk-value{
			flex:1;
			font-weight: 300;
			color: #000;
			letter-spacing: 1px;
			margin-left: 30px;
		}
	}
}

.hk-address{
	width: 500px;
	height: 300px;
}
.hk-status{
	width: 100%;
	.hk-status-item{
		padding: 10px 30px;
		text-align: center;
		border-right: 1px solid #f4f4f4;
		font-size: 12px;
		&:last-child{
			border-right: none;
		}
		.si-title{
			font-weight: 200;
			color: #767575;
			letter-spacing: 1px;
			margin-top: 10px;
		}
	}
}
</style>