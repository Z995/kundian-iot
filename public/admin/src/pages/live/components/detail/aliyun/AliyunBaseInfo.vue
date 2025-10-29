<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/aliyun/AliyunBaseInfo.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-17 10:00:24  -->
<template>
<div class="al-page">
	<div class="al-box flex">
		<div class="al-info">
			<div class="f16 fb">设备基本信息</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备状态</div>
				<div class="al-value">
					<a-tag color="#00CC66" size="mini">在线</a-tag>
				</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备名称</div>
				<div class="al-value">坤典科技测试设备 
					<a-button type="text" size="mini" @click="showUpdateName">修改</a-button>
				</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备类型</div>
				<div class="al-value">摄像头</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备序列号</div>
				<div class="al-value">7D0*****4C0</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备国标 ID</div>
				<div class="al-value">
					31000000****00000002
					<kd-copy></kd-copy>
				</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备端口</div>
				<div class="al-value">8080</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备 IP</div>
				<div class="al-value">10.10.10.10</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">用户名</div>
				<div class="al-value">admin</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">密码</div>
				<div class="al-value">
					<span>123456</span>
					<a-button type="text" size="mini" @click="showUpdatePwd">修改</a-button>
				</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备描述</div>
				<div class="al-value">-</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备厂商</div>
				<div class="al-value">大华</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备注册协议</div>
				<div class="al-value">gb28181</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">创建时间</div>
				<div class="al-value">2024-10-15 14:05:42</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">注册时间</div>
				<div class="al-value">2024-10-15 14:05:42</div>
			</div>
			<div class="al-info-item flex-c">
				<div class="al-title">设备位置</div>
				<div class="al-value">重庆市渝北区</div>
			</div>
		</div>
		<div class="hk-address">
			<kd-marker-address></kd-marker-address>
		</div>
	</div>
	<div class="al-status-box">
		<div class="f16 fb mt10">设备配置状态</div>
		<div class="al-status flex mt10">
			<div class="al-status-item" v-for="(item,index) in state.statusList" :key="index">
				<div class="si-val">{{ item.value_txt}}</div>
				<div class="si-title">{{ item.name }}</div>
			</div>
		</div>
	</div>
	
	<!-- 修改设备名称 -->
	<cqkd-update-device-name ref="nameRef"></cqkd-update-device-name>
	<cqkd-update-device-pwd ref="pwdRef"></cqkd-update-device-pwd>
</div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import KdMarkerAddress from '@/components/KdSelectMapLocation/KdMarkerAddress.vue';
import CqkdUpdateDeviceName from '../CqkdUpdateDeviceName.vue';
import CqkdUpdateDevicePwd from '../CqkdUpdateDevicePwd.vue';
const nameRef = ref()
const pwdRef = ref()
const state = reactive({
	statusList:[
		{name:'自动启动流',value:0,value_txt:'开启'},
		{name:'设备是否启用',value:0,value_txt:'开启'},
		{name:'位置订阅',value:0,value_txt:'开启'},
	]
})

//修改设备名称
const showUpdateName = ()=>nameRef.value.show()
//修改设备密码
const showUpdatePwd = ()=>pwdRef.value.show()
</script>

<style lang="scss" scoped>
.al-box{
	width: 100%;
	justify-content: space-between;
	.al-info{
		width: 600px;
		padding-left: 16px;
		.al-info-item{
			width: 100%;
			height: 40px;
			font-weight: 300;
			font-size: 12px;
			.al-title{
				width: 140px;
				font-weight: 200;
				color: #767575;
				letter-spacing: 1px;
			}
			.al-value{
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
.al-status-box{
	padding-left: 16px;
	.al-status{
		width: 100%;
		.al-status-item{
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