<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/index/components/CqkdUpdateLog.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-23 14:42:06  -->
<template>
<div class="update-log">
	<!-- <div class="ul-item" v-for="(item,index) in 5" :key="index">
		<span class="time">07/01</span>
		<span>1.0.1版本更新日志版本更新日志版本更新日志版本更新日志</span>
	</div> -->
	<kd-empty :height="150"></kd-empty>
</div>
</template>
<script setup>

</script>

<style lang="scss" scoped>
.update-log{
	width: 100%;
	height: 150px;
	background: #fff;
	border-radius: 8px;
	padding: 5px 10px;
	.ul-item{
		padding: 7px 0;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		cursor: pointer;
		font-size: 12px;
		font-weight: 300;
		.time{
			color: #999;
			margin-right: 10px;
			letter-spacing: 2px;
		}
	}
}
</style>