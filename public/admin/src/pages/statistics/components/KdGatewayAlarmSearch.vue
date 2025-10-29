<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/statistics/components/KdGatewayAlarmSearch.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 17:08:38  -->
<template>
<div class="search-box">
	<a-space>
		<a-select placeholder="请选择网关" class="w160" allow-clear>
			<a-option>网关1</a-option>
			<a-option>网关2</a-option>
			<a-option>网关3</a-option>
		</a-select>
		<a-select placeholder="报警状态" class="w120" allow-clear>
			<a-option>全部</a-option>
			<a-option>正常</a-option>
			<a-option>报警</a-option>
		</a-select>
		<a-select placeholder="处理状态" class="w120" allow-clear>
			<a-option>全部</a-option>
			<a-option>未处理</a-option>
			<a-option>已处理</a-option>
		</a-select>
		<a-range-picker
		    class="w340"
		    show-time
		    format="YYYY-MM-DD HH:mm"
		/>
		<a-button type="primary" @click="searchData">查询</a-button>
		<a-button>重置</a-button>
		<!-- 时间范围默认 [两小时前 ,当前时间]-->
	</a-space>
</div>
</template>

<script setup>
import { reactive } from 'vue';
const emits = defineEmits(['search'])
const state = reactive({
	search:{
		
	}
})
function searchData(){
	emits('search',JSON.parse(JSON.stringify(state.search)))
}
</script>

<style>
</style>