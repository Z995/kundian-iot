<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/components/KdBase/kdHeaderUser.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-07 10:07:24  -->
<template>
<div class="user-box flex-c" :style="getColor">
	<a-avatar :size="22" :style="{ backgroundColor: 'rgba(0,0,0,.2)' }">
		<IconUser />
	</a-avatar>
	<a-dropdown>
		<span class="ml5">{{state.loginUser}} <icon-down /></span>
		<template #content>
			<a-doption>修改密码</a-doption>
			<a-doption><span @click="logout">退出登录</span></a-doption>
		</template>
	</a-dropdown>
	<icon-settings class="ml10" size="16" @click="showThemeSet()"/>
	
	<kd-theme-set ref="setRef"></kd-theme-set>
</div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import router from '@/router'
import { Modal } from '@arco-design/web-vue';
import KdThemeSet from './KdThemeSet.vue'
import { useMenuStore } from '@/store/menu';
const menuStore = useMenuStore()
const setRef = ref()
const state = reactive({
	loginUser:localStorage.getItem('_IOT_USER_'),
})

//当前布局为上下结构时，考虑到顶部导航底色是白色情况下，修改文字颜色兼容文字颜色和底色不同色
const getColor = computed(()=>{
	if( menuStore.state.layoutStyle ==='top-bottom' ){
		return `color:var(--ment-text-color)`
	}
})

function logout(){
	Modal.confirm({
		title: '提示',
		content: '确认要退出登录吗？',
		titleAlign:"start",
		onOk:()=>{
			localStorage.removeItem("_IOT_TOKEN_")
			localStorage.removeItem("_IOT_USER_")
			router.push("/login")
		},
	});
}

function showThemeSet(){
	setRef.value.show()
}

</script>

<style lang="scss" scoped>
.user-box{
	cursor: pointer;
	color:#000;
}
</style>