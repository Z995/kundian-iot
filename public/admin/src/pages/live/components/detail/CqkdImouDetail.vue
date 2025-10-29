<!-- 坤典物联 - 乐橙监控详情 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/CqkdImouDetail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-17 14:02:38  -->
<template>
<div>
	<a-tabs default-active-key="1">
		<a-tab-pane key="1" title="基本信息">
			<imou-base-info></imou-base-info>
		</a-tab-pane>
		<a-tab-pane key="2" title="通道列表">
			<imou-channel></imou-channel>
		</a-tab-pane>
		<a-tab-pane key="3" title="云台控制">
			<imou-ptz></imou-ptz>
		</a-tab-pane>
		<a-tab-pane key="4" title="截图管理">
			<imou-screenshot></imou-screenshot>
		</a-tab-pane>
		<a-tab-pane key="5" title="本地录像">
			<imou-recording></imou-recording>
		</a-tab-pane>
	</a-tabs>
</div>
</template>

<script setup>
import { reactive } from 'vue';
import ImouBaseInfo from './imou/ImouBaseInfo.vue';
import ImouChannel from './imou/ImouChannel.vue';
import ImouPtz from './imou/ImouPtz.vue';
import ImouScreenshot from './imou/ImouScreenshot.vue';
import ImouRecording from './imou/ImouRecording.vue';
const state = reactive({
	
})
</script>

<style>
</style>