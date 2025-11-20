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
			<div class="f16 fb">设备基本信息</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备状态</div>
				<div class="qn-value">
					<a-tag color="#00CC66" size="mini">在线</a-tag>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备名称</div>
				<div class="qn-value">坤典科技测试设备 
					<a-button type="text" size="mini" @click="showUpdateName">修改</a-button>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备用户名</div>
				<div class="qn-value">xxxxxxxxx</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备密码</div>
				<div class="qn-value">
					******
					<a-button type="text" size="mini" @click="showUpdatePwd">修改</a-button>
				</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备版本号</div>
				<div class="qn-value">xxxxxxxxx</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备型号</div>
				<div class="qn-value">TP2</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备品牌</div>
				<div class="qn-value">lechange-乐橙设备</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备接入类型</div>
				<div class="qn-value">Paas程序接入</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">支持接入的通道数</div>
				<div class="qn-value">10</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备加密模式</div>
				<div class="qn-value">默认加密</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备最后离线时间</div>
				<div class="qn-value">2022-09-19 16:52:30</div>
			</div>
			<div class="qn-info-item flex-c">
				<div class="qn-title">设备位置</div>
				<div class="qn-value">重庆市渝北区</div>
			</div>
		</div>
		<div class="hk-address">
			<kd-marker-address></kd-marker-address>
		</div>
	</div>
	<!-- 修改设备名称 -->
	<cqkd-update-device-name ref="nameRef"></cqkd-update-device-name>
	<cqkd-update-device-pwd ref="pwdRef"></cqkd-update-device-pwd>
</div>
</template>

<script setup>
import { reactive,ref } from 'vue';
import KdMarkerAddress from '@/components/KdSelectMapLocation/KdMarkerAddress.vue';
import CqkdUpdateDeviceName from '../CqkdUpdateDeviceName.vue';
import CqkdUpdateDevicePwd from '../CqkdUpdateDevicePwd.vue';
const nameRef = ref()
const pwdRef = ref()
const state = reactive({
	statusList:[
		{name:'国标报警',value:0,value_txt:'开启'},
		{name:'本地录像',value:0,value_txt:'开启'},
		{name:'地理位置',value:0,value_txt:'开启'},
		{name:'音频',value:0,value_txt:'开启'},
		{name:'音频转码',value:0,value_txt:'开启'},
		{name:'按需拉流',value:0,value_txt:'开启'},
		{name:'注册成功后启动拉流',value:0,value_txt:'关闭'},
	]
})

//修改设备名称
const showUpdateName = ()=>nameRef.value.show()
//修改设备密码
const showUpdatePwd = ()=>pwdRef.value.show()
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
				width: 140px;
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