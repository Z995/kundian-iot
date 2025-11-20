<template>
<a-config-provider>
	<router-view></router-view>
</a-config-provider>
</template>
<script setup>
import { onMounted } from 'vue';
import { useMenuStore } from '@/store/menu';
onMounted(()=>{
	//初始化设置主题样式
	const menuStore = useMenuStore()
	menuStore.setPageTheme()
})
</script>
<style scoped>

</style>
