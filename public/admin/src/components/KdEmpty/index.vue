<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/components/KdEmpty/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-03 15:53:36  -->
<template>
<div class="none-data flex-cc" :style="{height:props.height+'px'}">
	<a-empty>
	    <template #image>
			<img src="@/assets/kd/none1.png" alt="" :style="imgStyle" class="none-img" />
	    </template>
	    <span class="title">{{props.title}}</span>
	</a-empty>
</div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
	title:{
		type:String,
		default:'暂无数据'
	},
	height:{
		type:Number,
		default:200
	}
})
const imgStyle = computed(()=>{
	return `
		width:${props.height/1.5}px;
		height:${props.height/1.5}px;
	`
})
</script>

<style lang="scss" scoped>
.none-data{
	width: 100%;
	.title{
		top: -30px;
		position: relative;
	}
}
</style>