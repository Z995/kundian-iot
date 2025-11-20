<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/greenhouse/houseAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-20 11:41:53  -->
<template>
<kd-page-box :loading="state.loading">
	<div class="kd-content mould-add">
		<kd-back title="新增/编辑大棚"></kd-back>
		<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'140px'}" :wrapper-col-props="{flex:1}">
			<a-form-item label="大棚名称" field="name" :rules="{required:true,message:'名称必填'}">
				<a-input placeholder="请输入名称" v-model="state.form.name" class="w600"></a-input>
			</a-form-item>
			<a-form-item label="封面图">
			    <a-upload
			        list-type="picture-card"
			        action="/"
			    />
			</a-form-item>
			<a-form-item label="大棚地理位置">
			    <a-button type="text" @click="showMap()">绘制地址位置区域</a-button>
			</a-form-item>
			<a-form-item label="大棚面积" >
				<a-input-number :min="0" placeholder="请输入名称" v-model="state.form.area"  class="w600">
					<template #suffix>平方米</template>
				</a-input-number>
			</a-form-item>
			<a-form-item label="大棚设备" >
				<div class="flex-c">
					<a-tag>温度</a-tag>
					<a-tag class="ml10">湿度</a-tag>
					<a-tag class="ml10">光照度</a-tag>
					<a-button type="text" class="ml10">选择设备</a-button>
				</div>
			</a-form-item>
			<a-form-item label="大棚监控">
				<div>
					<a-table class="w600 kd-small-table" :pagination="false" :data="state.live" :columns="[
						{title:'ID',dataIndex:'id'},
						{title:'监控名称',slotName:'name'},
						{title:'操作',slotName:'action'},
					]">
						<template #name="{record}">
							<div v-if="record.related_type ==='local'">
								<div>{{record.name}}</div>
							</div>
							<div v-else-if="record.related_type ==='kun_dian'">
								<div>{{record.desc || record.name }}</div>
							</div>
							<div v-else>
								<div>{{ record.name }}</div>
							</div>
						</template>
						<template #action="{record,rowIndex}">
							<a-button type="text" size="mini" class="ml-12" @click="delLive(rowIndex)">删除</a-button>
						</template>
					</a-table>
					<a-button type="text" class="mt10" @click="showLive">
						<i class="ri-add-line"></i>添加一个
					</a-button>
				</div>
			</a-form-item>
			<a-form-item >
				<a-button type="primary" class="w120">保存</a-button>
			</a-form-item>
		</a-form>
	</div>
	<kd-draw-ploygon-map ref="drawRef" @change="getPoint"></kd-draw-ploygon-map>
	<cqkd-select-live ref="liveRef" @select="getBindLiveData"></cqkd-select-live>
</kd-page-box>
</template>

<script setup>
import { reactive, ref } from 'vue';
import KdDrawPloygonMap from '@/components/KdSelectMapLocation/KdDrawPloygonMap.vue';
import CqkdSelectLive from '@/components/select/CqkdSelectLive.vue';
const drawRef = ref()
const liveRef = ref()
const state = reactive({
	loading:false,
	form:{
		id:0,
		name:'',
		area:null,
	}
})

function showMap(){
	drawRef.value.showMap([])
}
//显示选择监控
const showLive = ()=>liveRef.value.show()

//获取绘制数据
function getPoint(data){
	console.log('data',data);
	state.form.area = data.acreage || 0
}
</script>

<style lang="scss" scoped>

</style>