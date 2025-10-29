<!-- 坤典物联-->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/device/components/mould/KdSelectDeviceMould.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 11:59:32  -->
<template>
<div v-if="state.show">
	<a-modal title="选择模板" v-model:visible="state.show" width="1000px" :on-before-ok="saveData">
		<div class="mould-box flex">
			<a-list :bordered="false" class="mould-list" :max-height="400"
				@reach-bottom="getList(state.page+1)"
			>
			    <a-list-item v-for="(item,index) in state.mouldList" :key="index" 
					:class="{active:state.template_id===item.id}"
					@click="selectTemplate(item)"
				>
					<a-radio :value="item.id" v-model="state.template_id">
						<span class="f12">{{item.name}}</span>
					</a-radio>
				</a-list-item>
			</a-list>
			<div class="mould-data">
				<div class="flex-c mb10 f12">
					<span class="count">从机数量：{{ state.info.slave }}</span>
					<span class="count">变量数量：{{ state.info.variable_count }}</span>
					<span class="count">采集方式：{{ state.info.collect ===1 ?'云端轮询':'边缘计算'}}</span>
				</div>
				<a-table class="kd-small-table" row-key="id" :bordered="false" :data="state.info.data" :columns="state.columns"
					:loading="state.loading"
				>
					<template #data_format="{record}">
						{{constantData.dataFormat[record.data_format]}}
					</template>
				</a-table>
			</div>
		</div>
	</a-modal>
</div>
</template>

<script setup>
import { reactive } from 'vue';
import { getDeviceMouldList,getDeviceMouldDetail } from '@/api/kdDevice'
import constantData from '@/util/common/constantData';
import { Message } from '@arco-design/web-vue';
const emits = defineEmits(['success'])
const state = reactive({
	loading:false,
	show:false,
	mouldList:[],		//模板列表
	page:0,				//模板当前页
	columns:[
		{title:'从机名称',dataIndex:'slave_name'},
		{title:'变量名称',dataIndex:'name'},
		{title:'数值类型',slotName:'data_format'},
	],
	template_id:null,	//选中的模板id
	info:{				//选择的模板数据
		name:"",	
		variable_count:0,
		collect:1,
		slave:0,		//从机数量
		subordinate:[],	//从机列表
		data:[],		//变量列表
	},			
})
function open(data){
	state.show = true
	if( data){
		selectTemplate(data)
	}
}
//获取设备模板数据
function getList(page){
	state.page = page
	let param = {
		page:state.page,
		limit:30,
	}
	getDeviceMouldList(param).then(res=>{
		if( res.code === 200 && res.data?.list?.length){
			state.mouldList = page===1 ?res.data.list : state.mouldList.concat(res.data.list)
		}
	}).catch(()=>{
	})
}

//选择设备模板
function selectTemplate(data){
	state.template_id = data.id
	state.loading = true
	let info = {
		name:data.name,
		variable_count:data.variable_count,
		collect:data.collect,
		slave:0,		//从机数量
		subordinate:[],	//从机列表
		data:[],		//变量列表
	}
	getDeviceMouldDetail({id:data.id}).then(res=>{
		state.loading = false
		if( res.data?.id){
			info.slave = res.data.subordinate.length
			info.subordinate = res.data.subordinate
			
			//将从机变量放在一起
			let dataList = []
			info.subordinate.forEach(item=>{
				item.variable.forEach(val=>{
					dataList.push({
						...val,
						slave_name:item.name
					})
				})
			})
			info.data = dataList
		}
		state.info = info
	}).catch(()=>{
		state.loading = false
	})
}

async function saveData(){
	let { template_id,info } = state
	if( !template_id ){
		Message.warning("请选择设备模板")
		return false
	}
	
	let slaveData = []
	info.subordinate.forEach(item=>{
		slaveData.push({
			subordinate_id:item.id,
			name:item.name,
			protocol:item.protocol,
		})
	})
	
	emits("success",{
		template_id,
		template_name:info.name,
		data:slaveData
	})
}

defineExpose({
	open
})
</script>

<style lang="scss" scoped>
.mould-box{
	width: 100%;
	height: 400px;
	border: 1px solid #f4f4f4;
	.mould-list{
		width: 260px;
		border-right: 1px solid #f4f4f4;
		height: 100%;
		overflow: hidden;
		overflow-y: auto;
		&::-webkit-scrollbar{
			display: none;
		}
		&:hover{
			&::-webkit-scrollbar{
				display: block!important;
			}
		}
		.mould-item{
			width: 100%;
			height: 40px;
			padding-left: 20px;
			font-size: 12px;
			border-bottom: 1px solid #f4f4f4;
			line-height: 40px;
			cursor: pointer;
			span{
				color: #555;
			}
			&:last-child{
				border-bottom: none;
			}
		}
		.active{
			background: rgba(#0066FF, .1);
		}
	}
	.mould-data{
		flex: 1;
		padding: 10px 20px;
		
		.count{
			display: inline-block;
			padding-left: 10px;
			margin-right: 30px;
			position: relative;
			font-weight: bold;
			&:before{
				position: absolute;
				content: '';
				width: 4px;
				height: 14px;
				background: #0066FF;
				left: 0;
				top: 4px;
				border-radius: 2px;
			}
		}
	}
}
</style>