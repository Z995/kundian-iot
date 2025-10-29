<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/history.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 14:24:45  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="设备历史记录" :bordered="false">
			<!-- 搜索参数 -->
			<kd-search 
				@search="getList" 
				@changeDevice="getDeviceInfo" 
				@changeData="getData"
			></kd-search>
			
			<div class="history-box flex mt20">
				<a-spin :loading="state.loading" tip="数据加载中..." class="line-box">
					<div>
						<div class="f14 fb hb-title">曲线趋势图</div>
						<kd-history-data-line ref="dataLineRef"></kd-history-data-line>
					</div>
				</a-spin>
				
				<div class="info-box">
					<div class="info-data f12">
						<div class="f14 fb hb-title">设备信息</div>
						<template v-if="state.deviceInfo">
							<div class="info-item flex mt10">
								<div class="ii-title">设备名称：</div>
								<div class="ii-value">{{ state.deviceInfo.name }}</div>
							</div>
							<div class="info-item flex">
								<div class="ii-title">设备模板：</div>
								<div class="ii-value">{{ state.deviceInfo.template?.name || '-'}}</div>
							</div>
						</template>
						<kd-empty v-else></kd-empty>
					</div>
					<div class="info-data mt20">
						<div class="f14 fb hb-title">设备地图</div>
						<template v-if="state.deviceInfo">
							<div class="map-box">
								<kd-marker-address 
									id="markerMapBox" 
									:latitude="state.deviceInfo.latitude"
									:longitude="state.deviceInfo.longitude"
								></kd-marker-address>
							</div>
						</template>
						<kd-empty v-else></kd-empty>
					</div>
				</div>
			</div>
			
			<!-- 数据列表 -->
			<kd-history-data-list ref="dataListRef"></kd-history-data-list>
		</a-card>
	</div>
</kd-page-box>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import KdHistoryDataLine from './components/history/KdHistoryDataLine.vue';
import KdHistoryDataList from './components/history/KdHistoryDataList.vue';
import KdSearch from './components/KdSearch.vue';
import KdMarkerAddress from '../../components/KdSelectMapLocation/KdMarkerAddress.vue';
import { getDeviceDataHistoryRecord } from '@/api/kdDevice'
import { Message } from '@arco-design/web-vue';
import { useRoute } from 'vue-router';
const options = useRoute().query
const dataLineRef = ref()
const dataListRef = ref()
const state = reactive({
	loading:false,
	search:null,			//搜索参数
	deviceInfo:null,		//设备信息
	recordList:[],			//历史数据
	selectDataList:[],		//选择的变量
})

onMounted(()=>{
	
})

//获取变量历史记录
function getList(search){
	state.search = search
	if( !search?.data_id || !search?.data_id.length ) {
		Message.warning("请选择变量")
		return false
	}
	let param = {
		ids:search?.data_id.join(','),
	}
	if( search?.time && search?.time.length){
		param.start_time = search?.time[0]
		param.end_time = search?.time[1]
	}
	state.loading = true
	getDeviceDataHistoryRecord(param).then(res=>{
		state.loading = false
		state.recordList = res.data
		dataLineRef.value.drawData(res.data)
		dataListRef.value.refreshData(state.selectDataList,res.data)
	}).catch(()=>{
		state.loading = false
	})
}

//获取设备信息
function getDeviceInfo(data){
	state.deviceInfo = data
}
//获取选择的变量数据
function getData(data){
	state.selectDataList = JSON.parse(JSON.stringify(data))
}

</script>

<style lang="scss" scoped>
.history-box{
	width: 100%;
	height: 440px;
	gap:20px;
	.hb-title{
		width: 100%;
		height: 40px;
		line-height: 40px;
		padding-left: 20px;
		font-weight: bold;
		color: #000;
		border-bottom: 1px solid #f4f4f4;
	}
	.line-box{
		flex: 1;
		border: 1px solid #f4f4f4;
		height: 100%;
	}
	.info-box{
		width: 360px;
		.info-data{
			width: 100%;
			height: 210px;
			border: 1px solid #f4f4f4;
		}
		.map-box{
			height: 168px;
		}
		.info-item{
			padding:6px 20px;
			.ii-title{
				color: #777;
				width: 70px;
			}
			.ii-value{
				flex: 1;
				color: #000;
			}
		}
	}
}
</style>