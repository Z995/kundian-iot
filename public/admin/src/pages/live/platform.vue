<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/live/plateform.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-07-15 10:43:39  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="监控平台配置" :bordered="false">
			<div class="platform-item flex-cb">
				<div class="flex">
					<div class="aliyun-icon" style="line-height: 90px;">
						<img src="/static/img/live/kundian.png" alt="" />
					</div>
					<div class="platform-item-info">
						<div class="pi-title">坤典云</div>
						<div class="pi-desc">引领传统农业转型升级</div>
					</div>
				</div>
				<a-button type="primary" class="w120" @click="showKundian">配置</a-button>
			</div>
			<div class="platform-item flex-cb">
				<div class="flex">
					<div class="hai-kang-icon">
						<img src="/static/img/live/haikang.png" class="hai-kang" alt="" />
						<img src="/static/img/live/yingshi.png" class="ying-shi" alt="" />
					</div>
					<div class="platform-item-info">
						<div class="pi-title">海康威视 & 萤石云</div>
						<div class="pi-desc">引领智能物联新未来</div>
					</div>
				</div>
				<a-button type="primary" disabled class="w120" @click="showYinShi">开发中</a-button>
			</div>
			<div class="platform-item flex-cb">
				<div class="flex">
					<div class="aliyun-icon">
						<img src="/static/img/live/lecheng.png" alt="" />
					</div>
					<div class="platform-item-info">
						<div class="pi-title">乐橙</div>
						<div class="pi-desc">简单、安全、智能</div>
					</div>
				</div>
				<a-button type="primary" disabled class="w120" @click="showImou">开发中</a-button>
			</div>
		</a-card>
		
		<cqkd-ying-shi-yun ref="yingShiRef"></cqkd-ying-shi-yun>
		<cqkd-qiniu-yun ref="qiniuRef"></cqkd-qiniu-yun>
		<cqkd-tencent ref="tencentRef"></cqkd-tencent>
		<cqkd-aliyun ref="aliyunRef"></cqkd-aliyun>
		<cqkd-kundian ref="kundianRef"></cqkd-kundian>
		<cqkd-imou ref="imouRef"></cqkd-imou>
	</div>
</kd-page-box>
</template>

<script setup>
import CqkdYingShiYun from './components/config/CqkdYingShiYun.vue';
import CqkdQiniuYun from './components/config/CqkdQiniuYun.vue';
import CqkdTencent from './components/config/CqkdTencent.vue';
import CqkdAliyun from './components/config/CqkdAliyun.vue';
import CqkdKundian from './components/config/CqkdKundian.vue';
import CqkdImou from './components/config/CqkdImou.vue';
import { ref } from 'vue';

const yingShiRef = ref()
const qiniuRef = ref()
const tencentRef = ref()
const aliyunRef = ref()
const kundianRef = ref()
const imouRef = ref()

//萤石云配置
const showYinShi = ()=>yingShiRef.value.show()

//七牛云配置
const showQiniu = ()=>qiniuRef.value.show()
//腾讯云配置
// const showTencent = ()=>tencentRef.value.show()
//阿里云配置
// const showAliyun = ()=>aliyunRef.value.show()
//坤典云配置
const showKundian = ()=>kundianRef.value.show()
//乐橙配置
const showImou = ()=>imouRef.value.show()
</script>

<style lang="scss" scoped>
.platform-item{
	width: 100%;
	background: rgba(#0066FF, .05);
	border-radius: 10px;
	padding: 20px;
	margin-bottom: 20px;
	.hai-kang-icon{
		width: 150px;
		height: 80px;
		background: #fff;
		padding: 10px;
		line-height: 100px;
		border-radius: 6px;
		display: flex;
		align-items: center;
		gap:10px;
		.hai-kang{
			width: 70px;
		}
		.ying-shi{
			width: 40px;
		}
	}
	.aliyun-icon{
		width: 150px;
		height: 80px;
		background: #fff;
		padding: 10px;
		line-height: 80px;
		border-radius: 6px;
		img{
			width: 100%;
		}
	}
	.platform-item-info{
		margin-left: 20px;
		padding-top: 10px;
		.pi-title{
			font-size: 24px;
			font-weight: bold;
			color: #000;
			letter-spacing: 4px;
			margin-bottom: 10px;
		}
		.pi-desc{
			letter-spacing: 6px;
			color: #555;
			font-weight: 300;
			font-size: 15px;
		}
	}
}
</style>