<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/components/KdBack/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-30 10:55:15  -->
<template>
<div class="kd-back flex-cb">
	<div class="back-left flex-c">
		<div class="back-icon" @click="goBack">
			<icon-left/>
		</div>
		<span class="f16 fw">{{props.title}}</span>
	</div>
	<div class="back-right flex-c">
		<slot></slot>
	</div>
</div>
</template>

<script setup>
import router from '../../router';

const props = defineProps({
	title:{
		type:String,
		default:"返回"
	}
})

function goBack(){
	router.back()
}
</script>

<style lang="scss" scoped>
.kd-back{
	width: 100%;
	height: 50px;
	border-bottom: 1px solid #f4f4f4;
	margin-bottom: 20px;
	.back-icon{
		width: 30px;
		height: 30px;
		border-radius: 50%;
		background: #f4f4f4;
		text-align: center;
		line-height: 30px;
		margin-right: 15px;
		font-size: 18px;
		cursor: pointer;
		transition: all linear 0.05s;
		color: #999;
		
		&:hover{
			font-size: 20px;
			color: #555;
		}
	}
}
</style>