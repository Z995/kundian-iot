<!-- 坤典智慧农场V6-商户助手 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/gateway/components/detail/CqkdNetworkDebug.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-30 10:20:49  -->
<template>
<div class="debug-box">
	<div class="dialogue-box mt10">
		<div class="dialogue-content" id="dialogBox">
			<template v-for="(item,index) in deviceStore.state.dialogList" :key="index">
				<div class="dialog-device" v-if="item.type ==='device'">
					<div class="dialog-item flex">
						<img src="@/assets/kd/device.png" class="avatar"/>
						<div class="dialog-item-right">
							<div class="user-info flex-c">
								<span class="code">{{ item.code }}</span>
								<span class="time">{{ item.time }}</span>
							</div>
							<div class="dialog-item-content" v-if="item.message?.type ==='ModbusRTUS'">
								<span >{{ item.message?.data?.value }}</span>
							</div>
							<div class="dialog-item-content" v-else-if="item.message?.type ==='Customize'">
								<span >{{ item.message?.data?.val }}</span>
							</div>
							<div class="dialog-item-content" v-else>
								{{ item.message }}
							</div>
						</div>
					</div>
				</div>
				<div class="dialog-device dialog-user" v-if="item.type ==='user'">
					<div class="dialog-item flex ">
						<div class="dialog-item-right">
							<div class="user-info flex-c">
								<span class="time">{{ item.time }}</span>
								<span class="code">{{ item.code }}</span>
							</div>
							<div class="dialog-item-content">{{ item.message }}</div>
						</div>
						<img src="@/assets/kd/avatar.png" class="avatar"/>
					</div>
				</div>
			</template>
		</div>
	</div>
	
	<div class="input-box mt10">
		<div class="input-area">
			<a-textarea v-model="state.form.message" placeholder="请输入" style="height: 100px;"></a-textarea>
		</div>
		<div class="dialogue-footer">
			<a-button type="primary" class="w100" @click="sendMsg">发送</a-button>
		</div>
	</div>
	<div class="flex mt10">
		<a-radio-group v-model="state.form.type">
			<a-radio value="0">ASCII</a-radio>
			<a-radio value="1">HEX</a-radio>
			<a-radio value="2">GB2312</a-radio>
		</a-radio-group>
		<a-checkbox v-model="state.form.eol">末尾加换行</a-checkbox>
	</div>
</div>
</template>

<script setup>
import { onMounted, reactive, watch } from 'vue'
import { useDeviceStore } from '@/store/device'
import { Message } from '@arco-design/web-vue'
const deviceStore = useDeviceStore()
const props = defineProps({
	deviceCode:{				//设备号
		type:[String,Number],
		default:""
	}
})
const state = reactive({
	form:{
		type:"0",		//0:ASCII 1:HEX 2:GB2312
		eol:false,		//末尾加换行
		message:"",		//发送内容
	}
})

watch(()=>deviceStore.state.dialogList,()=>{
	
	scorllToBottom()
},{deep:true})

onMounted(()=>{
	deviceStore.state.dialogList = []	//	清空之前的数据
	//重新连接socket
	deviceStore.connect(null,props.deviceCode)
	scorllToBottom()
})

// 对话框滚动到最底部
function scorllToBottom(){
	requestAnimationFrame(() => {
	    dialogBox.scrollTop = dialogBox.scrollHeight;
	});
}

//发送消息
function sendMsg(){
	let { type,eol,message } = state.form
	if( !message ) {
		Message.warning("请输入发送内容")
		return false;
	}
	
	let msg = {
		to: props.code,
		type: type,
		eol: eol ? 1 : 0,
		val: message.replace(/\s+/g, ''),
		to:props.deviceCode
	}
	deviceStore.sendDebugMsg(msg)
	// scorllToBottom()
	state.form.message = ''
}

</script>

<style lang="scss" scoped>
.debug-box{
	width: 100%;
	height: 100%;
	.dialogue-box{
		width: 800px;
		height: 420px;
		border: 1px solid #f4f4f4;
		.dialogue-content{
			width: 100%;
			height: 100%;
			padding: 20px;
			overflow: hidden;
			overflow-y: auto;
			.dialog-device{
				width: 100%;
				display: flex;
				margin-bottom: 20px;
			}
			.dialog-item{
				width: 80%;
				.avatar{
					width: 48px;
					height: 48px;
					margin-right: 20px;
					border-radius: 5px;
				}
				.dialog-item-right{
					flex: 1;
				}
				.user-info{
					width: 100%;
					font-size: 12px;
					color: #777;
					.time{
						margin-left: 20px;
						font-weight: 300;
					}
				}
				.dialog-item-content{
					// flex: 1;
					max-width: 100%;
					margin-top: 10px;
					display: inline-block;
					background: #f7f7f7;
					padding: 10px;
					border-radius: 4px;
					position:relative;
					word-wrap: break-word;
					white-space: pre-wrap;
					word-break: break-all;
					font-size: 12px;
					line-height: 18px;
					letter-spacing: 1px;
					
					&:before{
						content: '';
						position: absolute;
						left: -10px;
						top: 13px;
						width: 0;
						height: 0;
						border-style: solid dashed dashed;
						border-color: #f7f7f7 transparent transparent;
						border-width: 10px;
						overflow: hidden;
					}
				}
			}
			
			.dialog-user{
				justify-content: flex-end;
				.avatar{
					margin-right: 0;
					margin-left: 20px;
				}
				.user-info{
					justify-content: flex-end;
					.time{
						margin-right: 20px;
					}
				}
				.dialog-item-right{
					text-align: right;
				}
				.dialog-item-content{
					text-align: right;
					&:before{
						border-width:0;
					}
					&:after{
						content: '';
						position: absolute;
						right: -10px;
						top: 13px;
						width: 0;
						height: 0;
						border-style: solid dashed dashed;
						border-color: #f7f7f7 transparent transparent;
						border-width: 10px;
						overflow: hidden;
					}
				}
			}
		}
	}
	
	.input-box{
		width: 800px;
		height: 170px;
		border: 1px solid #f4f4f4;
		padding: 20px;
	}
	.input-area{
		width: 100%;
		height: 110px;
	}
	.dialogue-footer{
		width: 100%;
		display: flex;
		justify-content: flex-end;
	}
}
</style>