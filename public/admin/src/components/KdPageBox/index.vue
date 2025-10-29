<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/components/KdPageBox/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-28 14:29:34  -->
<template>
<div class="kd-page-box">
	<a-spin class="span-box" :loading="props.loading" tip="数据加载中...">
		<slot></slot>
	</a-spin>
	<div class="kd-version">
		<div class="kd-img">
			<img class="logo-icon" src="/static/img/kd-logo.png" alt="" />
		</div>
		<div class="version-txt">Copyright ©2017-2025 坤典智慧农场-专注于数字农业管理系统</div>
	</div>
</div>
</template>

<script setup>
const props = defineProps({
	loading:{
		type:Boolean,
		default:false,
	}
})
</script>

<style lang="scss" scoped>
.kd-page-box{
	width: 100%;
	height: 100%;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	.page-box-content{
		width: 100%;
	}
	.span-box{
		width: 100%;
		flex: 1;
		overflow: hidden;
		overflow-y: auto;
		&::-webkit-scrollbar{
			display: none;
		}
	}
	.kd-version{
		width: 100%;
		height: 40px;
		text-align: center;
		.logo-icon{
			height: 30px;
		}
		.version-txt{
			color: #999;
			font-size: 11px;
			letter-spacing: 1px;
		}
	}
}
</style>