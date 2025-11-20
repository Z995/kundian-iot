<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/components/KdPager/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-28 15:02:01  -->
<template>
<div class="pager">
    <div v-if="showLeft">
        <slot></slot>
    </div>
    <a-pagination
        :current="pageData.page"
        :size="size"
        :page-size="pageData.limit"
        :show-jumper="showJumper"
        :show-page-size="showSize"
        :default-current="pageData.page"
        :total="pageData.count"
        :simple="props.simple"
        show-total
        @page-size-change="sizeChange"
        @change="pageChange"
    />
</div>
</template>

<script setup>
const props = defineProps({
	pageData:{
		type:Object
	},
	event:{
		type:Function
	},
	showSize:{
		type:Boolean,
		default:true
	},
	showJumper:{
		type:Boolean,
		default:false
	},
	size:{
		type:String,
		default:''
	},
	showLeft:{
		type:Boolean,
		default:true
	},
	simple:{
		type:Boolean,
		default:false
	}
})

function sizeChange(limit){
	if( typeof props.event == 'function' ){
		props.event(1,limit)
	}
}

function pageChange(page){
	if( page === props.pageData.page ) return
	if( typeof props.event == 'function' ){
		props.event(page,props.pageData.limit)
	}
}
</script>

<style lang="scss" scoped>
.pager{
	width: 100%;
	display: flex;
	justify-content: space-between;
	margin-top: 10px;
}
</style>