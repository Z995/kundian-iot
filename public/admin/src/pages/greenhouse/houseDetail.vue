<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/greenhouse/houseDetail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-20 14:02:13  -->
<template>
<kd-page-box :loading="state.loading">
	<div class="kd-content house-detail">
		<kd-back title="一号大棚"></kd-back>
		<div class="hd-info flex">
			<div class="house-map"></div>
			<div class="left-info-box ml20">
				<div class="fw">总面积：100平方米</div>
				<div class="mt15 fw">种植面积：10平方米</div>
				<div class="mt15 fw flex-c">种植品种：<span class="link flex-c fb">5种</span></div>
				<div class="mt15 fw">传感器：5</div>
				<div class="mt15 fw">绑定监控：5台</div>
			</div>
		</div>
		<div class="plant-list mt20">
			<div class="f15 fb mb10">种植列表
				<a-button type="text" size="small" class="ml10" @click="showAdd(null)"><i class="ri-add-line"></i>新增种植</a-button>
				<a-button type="text" size="small" class="ml10">查看历史种植（已结束的种植）</a-button>
			</div>
			<a-table :pagination="false" row-key="id" :bordered="false" :data="plant.list" :columns="[
			    {title:'ID',dataIndex:'id',width:80},
			    {title:'种植名称',dataIndex:'name'},
			    {title:'种植规模',dataIndex:'area'},
			    {title:'种植日期',dataIndex:'time'},
			    {title:'种植天数',dataIndex:'day'},
			    {title:'当前阶段',dataIndex:'current'},
			    {title:'状态',slotName:'status'},
			    {title:'已采摘',dataIndex:'pick'},
			    {title:'操作',slotName:'action',width:180},
			]" :loading="state.loading">
				<template #status="{record}">
					<a-tag color="#00CC66" v-if="record.status===1">种植中</a-tag>
					<a-tag color="#ccc" v-else>已结束</a-tag>
				</template>
			    <template #action="{record}">
			        <a-button type="text" size="mini" class="ml-10" @click="showPick(record)">采摘</a-button>
			        <a-button type="text" size="mini" class="ml-10" @click="showDetail(record)">详情</a-button>
			    </template>
			</a-table>
			<kd-pager :pageData="plant" :event="getPlantList"></kd-pager>
		</div>
		<div class="hd-device flex mt20">
			<div class="device-live">
				<div class="f14 fb mb10">监控设备</div>
				<div class="device-box">
					<div class="live-item flex-cc" v-for="(item,index) in 6" :key="index">
						<i class="ri-play-fill"></i>
					</div>
				</div>
			</div>
			<div class="device-live">
				<div class="f14 fb mb10">传感器设备</div>
				<div class="device-box">
					<div class="device-item" v-for="(item,index) in 6" :key="index">
						<div class="di-val f18 fb">18℃</div>
						<div class="di-title mt5">温度</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<cqkd-plant-add ref="plantaddRef"></cqkd-plant-add>
	<cqkd-plant-detail ref="detailRef"></cqkd-plant-detail>
	<cqkd-picking-warehousing-add ref="pickRef"></cqkd-picking-warehousing-add>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import CqkdPickingWarehousingAdd from '@/pages/produce/components/CqkdPickingWarehousingAdd.vue';
import CqkdPlantAdd from '@/pages/produce/components/CqkdPlantAdd.vue';
import CqkdPlantDetail from '@/pages/produce/components/plant/CqkdPlantDetail.vue';
const plantaddRef = ref()
const detailRef = ref()
const pickRef = ref()
const state = reactive({
	loading:false
})

//种植列表
const plant = reactive({
	loading:false,
	page:1,
	limit:10,
	count:0,
	list:[],
})

onMounted(()=>{
	getPlantList(1,10)
})
function getPlantList(page,limit){
	plant.list = [
		{id:1,name:'草莓',time:"2025-01-01",day:100,area:'100亩',pick:'100kg',current:'开花期',status:1},
		{id:2,name:'草莓',time:"2025-01-01",day:100,area:'100亩',pick:'100kg',current:'开花期',status:1},
		{id:3,name:'草莓',time:"2025-01-01",day:100,area:'100亩',pick:'100kg',current:'开花期',status:1},
		{id:3,name:'草莓',time:"2025-01-01",day:100,area:'100亩',pick:'100kg',current:'已结束',status:2},
	]
}

function showAdd(data){
	plantaddRef.value.show(data)
}
function showDetail(data){
	detailRef.value.show(data)
}
function showPick(data){
	pickRef.value.show(data)
}
</script>

<style lang="scss" scoped>
.hd-info{
	width: 100%;
	.house-map{
		width: 300px;
		height: 150px;
		border: 2px dashed #e6e6e6;
	}
}
.hd-device{
	width: 100%;
	gap:60px;
	.device-live{
		width: 50%;
		.device-box{
			width:100%;
			display: flex;
			flex-wrap: wrap;
			gap:20px;
		}
		.live-item{
			width: 160px;
			height: 100px;
			background: #000;
			border-radius: 10px;
			color: #fff;
			font-size: 32px;
			cursor: pointer;
		}
		
		.device-item{
			width: 160px;
			height: 80px;
			background: #f8f8f8;
			text-align: center;
			padding-top: 20px;
			border-radius: 10px;
			.di-title{
				color: #888;
			}
		}
	}
}

</style>