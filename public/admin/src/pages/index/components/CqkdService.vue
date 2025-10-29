<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:    kundian_iot_admin/pages/index/components/CqkdService.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-23 14:26:13  -->
<template>
<div class="service-box">
	<img src="/static/img/kd-logo.png" class="logo" alt="" />
	<div class="wechat-card flex">
		<div class="wc-user mt20">
			<img src="/static/img/create-user.png" class="avatar" alt="" />
			<div class="name fb mt5">涂坤</div>
			<div class="phone mt10 ">18723237733</div>
		</div>
		<div class="wc-qrcode">
			<img src="/static/img/create-wechat.jpg" class="qrcode" alt="">
		</div>
	</div>
</div>
</template>
<script setup>
</script>

<style lang="scss" scoped>
.service-box{
	width: 100%;
	height: 200px;
	background: #fff;
	border-radius: 8px;
	padding: 20px;
	.logo{
		width: 140px;
	}
	.wechat-card{
		width: 100%;
		position: relative;
		padding-left: 20px;
		.wc-user{
			width: 100px;
			text-align: center;
		}
		.avatar{
			width: 48px;
			height: 48px;
			border-radius: 50%;
		}
		.phone{
			color: #999;
		}
		.wc-qrcode{
			width: 110px;
			position: absolute;
			right: 0;
			top: -10px;
			.qrcode{
				width: 100%;
			}
		}
	}
}
</style>