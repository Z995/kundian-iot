<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/produce/components/plant/CqkdPlantDetail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-19 16:03:49  -->
<template>
<div v-if="state.show">
	<a-modal title="种植详情" v-model:visible="state.show" width="1000px" :footer="null">
		<div class="plant-detail">
			<a-tabs v-model:active-key="state.active">
			    <a-tab-pane key="1" title="基本信息">
					<a-timeline class="mt20" direction="horizontal" mode="bottom" labelPosition='relative'>
					    <a-timeline-item label="育苗生长期" line-type="solid" dot-type="solid" line-color="#4073FA">
							<div>
								<div>持续时间：1500天</div>
								<a-tag>一次性阶段</a-tag>
							</div>
						</a-timeline-item>
						<a-timeline-item label="花芽分化期" line-type="dashed" dot-type="hollow">
							<div>
								<div>持续时间：100天</div>
								<a-tag >循环阶段</a-tag>
								<div class="mt10">
									<a-tag color="#4073FA">当前阶段</a-tag>
								</div>
							</div>
						</a-timeline-item>
						<a-timeline-item label="苹果花期" line-type="dashed" dot-type="hollow">
							<div>
								<div>持续时间：100天</div>
								<a-tag>循环阶段</a-tag>
							</div>
						</a-timeline-item>
						<a-timeline-item label="初结果期" line-type="dashed" dot-type="hollow">
							<div>
								<div>持续时间：100天</div>
								<a-tag>循环阶段</a-tag>
							</div>
						</a-timeline-item>
						<a-timeline-item label="成熟结果期" line-type="dashed" dot-type="hollow">
							<div>
								<div>持续时间：100天</div>
								<a-tag>循环阶段</a-tag>
							</div>
						</a-timeline-item>
						<a-timeline-item label="休眠期" line-type="dashed" dot-type="hollow">
							<div>
								<div>持续时间：100天</div>
								<a-tag>循环阶段</a-tag>
							</div>
						</a-timeline-item>
					</a-timeline>
					<div class="flex-c mt20">
						<div class="plant-item flex-c">
							<div class="pi-title f14">种植名称</div>
							<div class="pi-value f14">草莓</div>
						</div>
						<div class="plant-item flex-c">
							<div class="pi-title f14">作物生长模型</div>
							<div class="pi-value f14">草莓</div>
						</div>
					</div>
					<div class="flex-c">
						<div class="plant-item flex-c">
							<div class="pi-title f14">种植日期</div>
							<div class="pi-value f14">2025-10-10</div>
						</div>
						<div class="plant-item flex-c">
							<div class="pi-title f14">已种植天数</div>
							<div class="pi-value f14">150天 <a-tag class="ml10" color="#00b42a" size="small">挂果</a-tag></div>
						</div>
					</div>
					<div class="flex-c">
						<div class="plant-item flex-c">
							<div class="pi-title f14">规模</div>
							<div class="pi-value f14">10亩</div>
						</div>
						<div class="plant-item flex-c">
							<div class="pi-title f14">已收获</div>
							<div class="pi-value f14">100kg</div>
						</div>
					</div>
					<a-space class="mt20">
						<a-button class="w100">删除</a-button>
						<a-button type="primary" class="w100">编辑</a-button>
						<a-button type="primary" class="w100">结束种植</a-button>
					</a-space>
				</a-tab-pane>
				<a-tab-pane key="2" title="农事作业">
					<a-table class="kd-small-table" :pagination="false" row-key="id" :bordered="false" :data="farming.list" :columns="[
					    {title:'ID',dataIndex:'id',width:60},
					    {title:'农事类型',dataIndex:'name',width:120},
					    {title:'农机',dataIndex:'machinery'},
					    {title:'使用物料',dataIndex:'material'},
					    {title:'作业内容',dataIndex:'conent'},
					    {title:'农事拍照',slotName:'img'},
					    {title:'作业人员',dataIndex:'user'},
					    {title:'作业时间',dataIndex:'create_time'},
					    {title:'操作',slotName:'action',width:180},
					]" :loading="state.loading">
					    <template #img="{record}">
					        <a-image width="40" :src="record.img"></a-image>
					    </template>
					    <template #action="{record}">
					        <a-button type="text" size="mini" class="ml-10">设为私密</a-button>
					        <a-popconfirm content="确认删除农事作业吗？" @ok="delData(record.id)">
					            <a-button type="text" size="mini">删除</a-button>
					        </a-popconfirm>
					    </template>
					</a-table>
					<kd-pager :pageData="farming" :event="getList"></kd-pager>
				</a-tab-pane>
				<a-tab-pane key="3" title="采摘记录">
					<a-table :pagination="false" row-key="id" :bordered="false" :data="pickRecord.list" :columns="[
					    {title:'ID',dataIndex:'id',width:100},
					    {title:'采摘人员',dataIndex:'name'},
					    {title:'采摘数量',slotName:'pickingCount'},
					    {title:'备注',dataIndex:'remark'},
					    {title:'采摘时间',dataIndex:'create_time'},
					]" :loading="state.loading">
					    <template #pickingCount="{record}">
					        <div>{{ record.pickingCount }}kg</div>
					    </template>
					    <template #expirationDate="{record}">
					        <div>{{ record.expirationDate || '-' }}</div>
					    </template>
					</a-table>
					<kd-pager :pageData="pickRecord" :event="getPickList"></kd-pager>
				</a-tab-pane>
			</a-tabs>
		</div>
	</a-modal>
</div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';

const state = reactive({
	show:false,
	active:'1'
})

//农事记录
const farming = reactive({
	list:[],
	page:1,
	limit:10,
	count:0
})

//采摘记录
const pickRecord = reactive({
	list:[],
	page:1,
	limit:10,
	count:0
})

onMounted(()=>{
})

function show(){
	state.show = true
	getFarmingList(1,10)
	getPickList(1,10)
}

function getFarmingList(page,limit){
	farming.page = page || farming.page
	farming.limit = limit || farming.limit
	let param = {
		page:farming.page,
		limit:farming.limit,
	}
	farming.list = [
		{id:1,name:'除草',material:'有机肥(2kg)',conent:'施肥操作施肥操作',user:'李四',machinery:'施肥机',plant:'番茄,土豆',status:1,create_time:'2025/10/10 12:00',img:'https://kundian.cqkundian.com/uploads/farm/2024/9/27/24a1407a77e443d91f2edd15ecbf7068278bfa154e15cce38e786b5824aa7985e23ced191600240383.jpg',expand:''},
		{id:2,name:'施肥',material:'有机肥(2kg)',conent:'施肥操作施肥操作',user:'李四',machinery:'施肥机',plant:'番茄,土豆',status:0,create_time:'2025/10/10 12:00',img:'https://kundian.cqkundian.com/uploads/farm/2024/9/27/24a1407a77e443d91f2edd15ecbf7068278bfa154e15cce38e786b5824aa7985e23ced191600240383.jpg',expand:''},
		{id:3,name:'浇水',material:'有机肥(2kg)',conent:'施肥操作施肥操作',user:'李四',machinery:'施肥机',plant:'番茄,土豆',status:1,create_time:'2025/10/10 12:00',img:'https://kundian.cqkundian.com/uploads/farm/2024/9/27/24a1407a77e443d91f2edd15ecbf7068278bfa154e15cce38e786b5824aa7985e23ced191600240383.jpg',expand:''}
	]
}
function getPickList(page,limit){
	pickRecord.page = page || pickRecord.page
	pickRecord.limit = limit || pickRecord.limit
	let param = {
		page:pickRecord.page,
		limit:pickRecord.limit,
	}
	pickRecord.list = [
		{id:1,name:'张三',cropName:'苹果',pickingCount:'100',expirationDate:'12',create_time:'2025/10/10'},
		{id:2,name:'张三',cropName:'苹果',pickingCount:'100',expirationDate:'34',create_time:'2025/10/10'},
		{id:3,name:'张三',cropName:'苹果',pickingCount:'100',expirationDate:'25',create_time:'2025/10/10'},
	]
}
defineExpose({
	show
})
</script>

<style lang="scss" scoped>
.plant-detail{
	width: 100%;
	height: 500px;
	.plant-item{
		width: 100%;
		padding: 5px 0;
		.pi-title{
			width: 100px;
			color: #666;
			font-weight: 300;
		}
	}
}
</style>