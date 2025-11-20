<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/index/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-28 09:20:43  -->
<template>
<kd-page-box>
	<div class="index-page">
		<div class="ic-title fb">运营概况</div>
		<div class="ip-card flex">
			<div class="total-item" v-for="(item,index) in state.list" :key="index">
				<div class="ti-header flex-c">
					<img :src="'/static/img/home/'+item.icon" class="icon-img" alt="" />
					<div class="item-right">
						<div class="title">{{ item.name }}</div>
						<div class="desc">{{ item.english }}</div>
					</div>
				</div>
				<div class="ti-val">
					<span class="fb val-data">{{item.value}}</span>
					<!-- <span class="offline" v-if="item.offline">离线 <span class="fb">{{ item.offline}}</span></span> -->
				</div>
			</div>
		</div>
		<div class="ip-content flex mt10">
			<div class="ip-map-box">
				<div class="ic-title fb">设备位置</div>
				<cqkd-device-map></cqkd-device-map>
			</div>
			<div class="ip-other">
				<div class="ic-title fb">官方客服</div>
				<cqkd-service></cqkd-service>
				<div class="ic-title fb mt10">更新日志</div>
				<cqkd-update-log></cqkd-update-log>
				<cqkd-advert></cqkd-advert>
			</div>
		</div>
	</div>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import CqkdService from './components/CqkdService.vue';
import CqkdUpdateLog from './components/CqkdUpdateLog.vue';
import CqkdAdvert from './components/CqkdAdvert.vue';
import CqkdDeviceMap from './components/CqkdDeviceMap.vue';
import { getHomeTotal } from '@/api/kdStatistics'
const state = reactive({
	list:[
		
	]
})

onMounted(()=>{
	getHomeTotal().then(res=>{
		console.log(res.data);
		let data = res.data
		state.list = [
			{name:'设备总数 (台)',english:'Total number of devices',value:data.device_count ||0,icon:'device.png',offline:1},
			{name:'网关总数 (台)',english:'Total number of gateways',value:data.gateway_count || 0,icon:'gateway.png',offline:2},
			{name:'用户总数 (台)',english:'Total number of users',value:data.admin_count || 0,icon:'user.png'},
			{name:'今日变量报警 (台)',english:'Today varible alarm',value:data.warning_count || 0,icon:'alarm.png'},
			{name:'监控总数 (台)',english:'Total number of monitoring',value:data.monitor_count || 0,icon:'live.png',offline:3},
			{name:'今日联动数量 (台)',english:'Today linkage quantity',value:data.linkageLog_count || 0,icon:'link.png'},
		]
	})
	
})
</script>

<style lang="scss" scoped>
.index-page{
	width: 100%;
	height: calc(100% - 50px);
	gap:20px;
	.ic-title{
		position: relative;
		padding-left: 12px;
		margin-bottom: 10px;
		&:before{
			position: absolute;
			content: '';
			left: 0;
			top: 1px;
			width: 4px;
			height: 14px;
			background: #0066FF;
		}
	}
	.ip-card{
		gap:20px;
		.total-item{
			flex: 1;
			background: #fff;
			padding: 15px 10px;
			border-radius: 10px;
			.icon-img{
				width: 2.2vw;
				height: 2.2vw;
				margin-right: 0.8vw;
			}
			.offline{
				color: #8c98a5;
				font-size: 0.75vw;
				margin-left: 0.6vw;
				span{
					color: red;
				}
			}
			.title{
				font-size:0.85vw;
				color: #8c98a5;
				font-weight: bold;
				letter-spacing: 2px;
			}
			.desc{
				margin-top: 7px;
				font-size: 0.65vw;
				color:#8c98a5;
			}
			.ti-val{
				padding-left: 2.8vw;
				margin-top: 10px;
				font-size: 1.3vw;
			}
		}
	}
	
	.ip-content{
		width: 100%;
		flex: 1;
		justify-content: space-between;
		gap:20px;
		.ip-other{
			width: 300px;
			height: 100%;
		}
		.ip-map-box{
			flex: 1;
		}
	}
}
</style>