<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/components/KdCopy/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-20 09:52:34  -->
<template>
<div class="copy-box">
	<span class="kd-link" 
		v-clipboard:copy="props.value"
		v-clipboard:success="copySuccess"
	><icon-copy />点击复制</span>
</div>
</template>

<script setup>
import { Message } from '@arco-design/web-vue';
const props = defineProps({
	value:{
		type:[Number,String],
		default:""
	}
})
function copySuccess(){
	Message.success("复制成功")
}

</script>

<style lang="scss" scoped>
.copy-box{
	display: inline-block;
	.kd-link{
		font-size: 12px;
	}
}
</style>