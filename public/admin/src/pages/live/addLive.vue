<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/addList.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-15 15:59:30  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<kd-back :title="state.title"></kd-back>
		<!-- 普通直播流 -->
		<cqkd-general-live v-if="option.type==='local'" :id="option.id"></cqkd-general-live>
		<!-- 海康威视 -->
		<cqkd-hai-kang v-if="option.type==='3'"></cqkd-hai-kang>
		<!-- 七牛云 & 坤典云 -->
		<cqkd-qiniu v-if="option.type ==='kun_dian'"></cqkd-qiniu>
		<!-- 阿里云 -->
		<!-- <cqkd-aliyun v-if="option.type ==='5'"></cqkd-aliyun> -->
		<!-- 乐橙 & 大华 -->
		<cqkd-imou v-if="option.type ==='6'"></cqkd-imou>
	</div>
</kd-page-box>
</template>

<script setup>
import CqkdGeneralLive from './components/list/CqkdGeneralLive.vue';
import CqkdHaiKang from './components/list/CqkdHaiKang.vue';
import CqkdQiniu from './components/list/CqkdQiniu.vue';
import CqkdAliyun from './components/list/CqkdAliyun.vue';
import CqkdImou from './components/list/CqkdImou.vue';
import { onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
const option = useRoute().query
const state = reactive({
	title:"新增监控"
})
onMounted(()=>{
	state.title = option.id ? '编辑监控':'新增监控'
})
</script>

<style>
</style>