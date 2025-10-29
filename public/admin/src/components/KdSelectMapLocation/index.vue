<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/components/KdSelectMapLocation/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-18 10:20:48  -->
<template>
<div style="width: 100%;">
	<div class="add-input">
		<a-input placeholder="请选择地址" disabled v-model="state.address">
			<template #append>
				<span class="kd-link" @click="showModal">选择地址</span>
			</template>
		</a-input>
	</div>
	<div v-if="state.show">
		<a-modal title="选择位置" v-model:visible="state.show" width="1000px" :on-before-ok="sureSelect">
			<div class="map-box">
				<div class="search-box">
					<div class="input-box flex">
						<a-input v-model="state.keyword" placeholder="请输入位置进行搜索" 
							@press-enter="doSearch(state.keyword)"
							@focus="getFocus"
						></a-input>
						<a-button type="primary" class="ml10" @click="doSearch(state.keyword)">搜索</a-button>
					</div>
					<a-spin :loading="tianState.search.loading" tip="加载中...">
						<div class="searchResult" ref="resultRef" v-if="tianState.search.loading || tianState.search.show">
							<div class="address-item" v-for="(item,index) in tianState.search.list" :key="index"
								@click="selectSearchAddress(item)"
							>
								<i class="ri-search-2-line"></i>
								<span class="address-name">{{ item.name}}</span>
								<span class="address-area">{{ item.address}}</span>
							</div>
							<kd-empty v-if="!tianState.search.list.length"></kd-empty>
						</div>
					</a-spin>
				</div>
				<div class="map-box-container" id="selectMapBox"></div>
				<div class="flex-c mt10">
					<div>详情地址</div>
					<div class="w600 ml10">
						<a-input placeholder="请选择地址" v-model="tianState.selectLocation.address"></a-input>
					</div>
				</div>
				<div class="flex-c mt10">
					<div>经 纬 度：</div>
					<div class="w600 ml10">
						<span>{{tianState.selectLocation.location}}</span>
					</div>
				</div>
			</div>
		</a-modal>
	</div>
</div>
</template>

<script setup>
import { onClickOutside } from '@vueuse/core'
import { nextTick, onMounted, reactive, ref, watch } from 'vue';
import { useTianDiMap } from '@/util/common/TianDiMap'
import { Message } from '@arco-design/web-vue';
const { tianState,initMap,createSearchLocation ,doSearch,setMapCenter,closeSearchResultBox ,addMarker,getCurrentLocation} = useTianDiMap()
const resultRef = ref()
const props = defineProps({
	lngLat:{
		type:Array,
		default:[]
	},
	address:{
		type:String,
		default:''
	}
})
const emits = defineEmits(['success'])
const state = reactive({
	show:false,
	keyword:"",
	address:"",	//当前位置详细地址
	location:[],	//当前位置经纬度
})

watch(()=>props.lngLat ,()=>{
	state.address = props.address || ''
	state.location = props.lngLat && props.lngLat.length ?props.lngLat : [116.40769, 39.89945]
	if( !state.address ){
		state.address = props.lngLat.join(',')
	}
},{deep:true,immediate:true})

//关闭搜索结果框
onClickOutside(resultRef, event => {
	tianState.search.show = false
})

//显示选择地址弹框
function showModal(){
	state.show = true
	state.address = props.address || ''
	state.location = props.lngLat && props.lngLat.length ?props.lngLat : [116.40769, 39.89945]
	nextTick(()=>{
		let center = state.location || []
		initMap({el:"selectMapBox",center,isClick:true },()=>{
			getCurrentLocation()
			createSearchLocation()
			if( center.length ){
				addMarker(center[0], center[1],{draggable:true,isResolution:true})
			}
		})
	})
}

//选择搜索结果的位置
function selectSearchAddress(data){
	tianState.search.show = false
	if( data.lonlat ){
		let arr = data.lonlat.split(',')
		setMapCenter(arr[0],arr[1])
		addMarker(arr[0],arr[1],{draggable:true,isResolution:true})
	}
}

//输入框获取焦点 ,如果有搜索结果，展示搜索结果
function getFocus(){
	if( tianState.search.list.length ){
		tianState.search.show = true
	}
}

async function sureSelect(){
	let { location,address } = tianState.selectLocation
	if( !location.length || !address ){
		Message.warning('请选择地址')
		return false
	}
	state.address = address
	state.location = location
	emits("success",{location,address})
}
</script>

<style lang="scss" scoped>
.map-box{
	width: 100%;
	height: 500px;
	position: relative;
	.map-box-container{
		width: 100%;
		height: 440px;
	}
	.search-box{
		position: absolute;
		z-index: 999999;
		padding: 10px;
		.input-box{
			width: 400px;
			box-shadow: 1px 1px 10px #acacac;
			overflow: hidden;
		}
	}
	
	.searchResult{
		width: 400px;
		background: #fff;
		margin-top: 5px;
		box-shadow: 1px 1px 10px #acacac;
		border-radius: 4px;
		min-height: 300px;
		
		.address-item{
			width: 100%;
			padding: 0 10px;
			font-size: 12px;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			height: 32px;
			line-height: 32px;
			cursor: pointer;
			font-family: microsoft yahei,sans-serif;
			.address-name{
				color: #333;
				margin-right: 10px;
			}
			&:hover{
				background: rgba(#000, .05);
				.address-name{
					color: #0066FF;
				}
			}
			.ri-search-2-line{
				color: #999;
				margin-right: 8px;
			}
			.address-area{
				font-weight: 300;
				margin-right: 10px;
				color: #b8b8b8;
			}
		}
	}
}
</style>