<!-- 坤典物联-七牛云设备基础信息 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/qiniu/QiniuBaseInfo.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-16 16:20:43  -->
<template>
<div class="qn-page">
	<div class="qn-box flex">
		<div class="qn-info">
			<div class="f16 fb">接入网关</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">SIP 服务器 ID</div>
				<div class="qn-value">{{monitorState.state.gateway?.sipServerId || '-'}}
					<kd-copy v-if="monitorState.state.gateway?.sipServerId" :value="monitorState.state.gateway?.sipServerId"></kd-copy>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">SIP 服务器 IP</div>
				<div class="qn-value">{{monitorState.state.gateway?.sipServerIP || '-'}}
					<kd-copy v-if="monitorState.state.gateway?.sipServerIP" :value="monitorState.state.gateway?.sipServerIP"></kd-copy>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">SIP 服务器端口号（UDP）</div>
				<div class="qn-value">{{monitorState.state.gateway?.sipServerPort?.[0] || '-'}}
					<kd-copy v-if="monitorState.state.gateway?.sipServerPort?.[0]" :value="monitorState.state.gateway?.sipServerPort?.[0]"></kd-copy>
				</div>
			</div>
			
			<div class="f16 fb mt10">设备基本信息</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备状态</div>
				<div class="qn-value">
					<a-tag color="#999" v-if="monitorState.state.qnDetail?.state === 'offline'">离线</a-tag>
					<a-tag color="#00CC66" v-if="monitorState.state.qnDetail?.state === 'online'">在线</a-tag>
					<a-tag color="#FF9900" v-if="monitorState.state.qnDetail?.state === 'notReg'">未注册</a-tag>
					<a-tag color="#FF0066" v-if="monitorState.state.qnDetail?.state === 'locked'">锁定</a-tag>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备名称</div>
				<div class="qn-value">{{monitorState.state.info?.desc || '-'}} 
					<a-button type="text" size="mini" @click="showUpdateName">修改</a-button>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备类型</div>
				<div class="qn-value">{{monitorState.state.detail?.type === 1 ?'摄像头':'平台'}}</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">所属空间</div>
				<div class="qn-value">{{ monitorState.state.detail?.space_id || '-'}}</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备国标ID</div>
				<div class="qn-value">
					{{ monitorState.state.detail?.gbId || '-'}} 
					<kd-copy v-if="monitorState.state.detail?.gbId" :value="monitorState.state.detail?.gbId"></kd-copy>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">用户名</div>
				<div class="qn-value">
					{{ monitorState.state.qnDetail?.username || '-'}}
					<kd-copy v-if="monitorState.state.qnDetail?.username" :value="monitorState.state.qnDetail?.username"></kd-copy>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">密码</div>
				<div class="qn-value">
					<span>{{ monitorState.state.qnDetail?.password ||'-' }}</span>
					<a-button type="text" size="mini" @click="showUpdatePwd">修改</a-button>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">厂商</div>
				<div class="qn-value">{{ monitorState.state.detail?.vendor || '-'}}</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">创建时间</div>
				<div class="qn-value">{{formatTime(monitorState.state.qnDetail?.createdAt*1000)}}</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">最近心跳时间</div>
				<div class="qn-value">{{formatTime(monitorState.state.qnDetail?.lastKeepaliveAt*1000)}}</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">最近注册时间</div>
				<div class="qn-value">{{formatTime(monitorState.state.qnDetail?.lastRegisterAt*1000)}}</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备位置</div>
				<div class="qn-value">{{monitorState.state.info?.address_format}}</div>
			</div>
		</div>
		<div class="hk-address">
			<kd-marker-address 
				:longitude="monitorState.state.info?.longitude"
				:latitude="monitorState.state.info?.latitude"
			></kd-marker-address>
		</div>
	</div>
	<div class="qn-status-box">
		<div class="f16 fb mt10">设备配置状态</div>
		<div class="qn-status flex mt10">
			<div class="qn-status-item" v-for="(item,index) in getDeviceStatus" :key="index">
				<div class="si-val">{{ item.value_txt}}</div>
				<div class="si-title">{{ item.name }}</div>
			</div>
		</div>
	</div>
	<!-- 修改设备名称 -->
	<cqkd-update-device-name ref="nameRef" @success="monitorState.getInfo(monitorState.state.live_id)"></cqkd-update-device-name>
	<cqkd-update-device-pwd ref="pwdRef" @success="monitorState.getInfo(monitorState.state.live_id)"></cqkd-update-device-pwd>
</div>
</template>

<script setup>
import { computed, onMounted, reactive,ref } from 'vue';
import { useMonitorStore } from '@/store/monitor'
import { formatTime ,lngLatToAddress } from '@/util/util'

// 引入组件
import KdMarkerAddress from '@/components/KdSelectMapLocation/KdMarkerAddress.vue';
import CqkdUpdateDeviceName from '../CqkdUpdateDeviceName.vue';
import CqkdUpdateDevicePwd from '../CqkdUpdateDevicePwd.vue';

const monitorState = useMonitorStore()
const nameRef = ref()
const pwdRef = ref()
const state = reactive({
	
})

onMounted(()=>{
	
})

//获取设备状态信息
const getDeviceStatus = computed(()=>{
	let data =  monitorState.state.qnDetail
	return [
		{name:'国标报警',value:0,value_txt:data?.alarmEnable ? '开启':'关闭'},
		{name:'本地录像',value:0,value_txt:data?.localRecordPushEnable ? '开启':'关闭'},
		{name:'地理位置',value:0,value_txt:data?.location?.enable?'开启':'关闭'},
		{name:'音频',value:0,value_txt:data?.rtpAudio ? '开启':'关闭'},
		{name:'音频转码',value:0,value_txt:data?.rtpAudioTranscode ? '开启':'关闭'},
		{name:'按需拉流',value:0,value_txt:data?.onDemandPull ? '开启':'关闭'},
		{name:'注册成功后启动拉流',value:0,value_txt:data?.pullIfRegister ?'开启':'关闭'},
	]
})

//修改设备名称
const showUpdateName = ()=>nameRef.value.show(monitorState.state.info)
//修改设备密码
const showUpdatePwd = ()=>pwdRef.value.show(monitorState.state.info.id)

</script>

<style lang="scss" scoped>
.qn-box{
	width: 100%;
	justify-content: space-between;
	.qn-info{
		width: 600px;
		padding-left: 16px;
		.qn-info-item{
			width: 100%;
			height: 40px;
			font-weight: 300;
			font-size: 12px;
			.qn-title{
				width: 160px;
				font-weight: 200;
				color: #767575;
				letter-spacing: 1px;
			}
			.qn-value{
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
}
.qn-status-box{
	padding-left: 16px;
	.qn-status{
		width: 100%;
		.qn-status-item{
			padding: 10px 40px;
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
}

</style>