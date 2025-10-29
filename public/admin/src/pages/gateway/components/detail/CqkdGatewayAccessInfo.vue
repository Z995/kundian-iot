<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/components/detail/CqkdGatewayAccessInfo.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-30 10:01:44  -->
<template>
<div class="access-info">
	<div class="f15 fb">接入信息</div>
	<div class="access-info-content mt10">
		<div class="aic-box flex">
			<div class="aic-item flex-c">
				<div class="aic-item-label">接入地址：</div>
				<div class="aic-item-val">
					<span>-</span>
					<span class="copy-btn ml20 f12"><icon-copy />点击复制</span>
				</div>
			</div>
			<div class="aic-item flex-c">
				<div class="aic-item-label">端 口 号：</div>
				<div class="aic-item-val">
					<span>-</span>
				</div>
			</div>
		</div>
		<div class="aic-box flex">
			<div class="aic-item flex-c">
				<div class="aic-item-label">注 册 包：</div>
				<div class="aic-item-val">
					<span>{{props.info.mac}}</span>
					<span class="copy-btn ml20 f12">
						<kd-copy :value="props.info.mac"></kd-copy>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
</template>

<script setup>
const emits = defineEmits(['refresh'])
const props = defineProps({
	info:{
		type:Object,
		default:null
	}
})
</script>

<style lang="scss" scoped>
.access-info{
	width: 100%;
	padding-top: 20px;
	.access-info-content{
		width: 100%;
		height: 100px;
		background: #f9fafb;
		padding: 10px;
		border: 1px solid #f4f4f4;
		.aic-box{
			width: 100%;
			.aic-item{
				width: 500px;
				padding: 8px 0;
				font-size: 13px;
				.aic-item-label{
					width: 80px;
					font-weight: bold;
					font-size: 12px;
				}
				.copy-btn{
					display: inline-block;
					padding: 4px 10px;
					background: rgba(#0066FF, .1);
					border-radius: 3px;
					cursor: pointer;
					color: #0066FF;
				}
			}
		}
	}
}
</style>