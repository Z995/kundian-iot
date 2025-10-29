<!-- 坤典物联-阿里云设备详情 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/components/detail/CqkdAliyunDetail.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-17 09:58:42  -->
<template>
<div>
	<a-tabs default-active-key="1">
	    <a-tab-pane key="1" title="基本信息">
			<aliyun-base-info></aliyun-base-info>
	    </a-tab-pane>
	    <a-tab-pane key="2" title="通道列表">
			<aliyun-channel></aliyun-channel>
	    </a-tab-pane>
	    <a-tab-pane key="3" title="云台控制">
			<aliyun-ptz></aliyun-ptz>
	    </a-tab-pane>
	    <a-tab-pane key="4" title="截图管理">
			<aliyun-screenshot></aliyun-screenshot>
	    </a-tab-pane>
	    <a-tab-pane key="5" title="本地录像">
			<aliyun-recording></aliyun-recording>
	    </a-tab-pane>
	</a-tabs>
</div>
</template>

<script setup>
import AliyunBaseInfo from './aliyun/AliyunBaseInfo.vue';
import AliyunChannel from './aliyun/AliyunChannel.vue';
import AliyunPtz from './aliyun/AliyunPtz.vue';
import AliyunScreenshot from './aliyun/AliyunScreenshot.vue';
import AliyunRecording from './aliyun/AliyunRecording.vue';
</script>

<style>
</style>