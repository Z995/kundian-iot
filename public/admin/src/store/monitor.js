/**
 * 坤典智慧农场-
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: 
 * @description File path and name: 
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-08-18 10:51:10
 */

import { defineStore } from "pinia";
import { reactive } from "vue";
import { getLiveDetail ,getLiveChanngeList } from '@/api/kdLive'
export const useMonitorStore = defineStore("monitor",()=>{
	const state = reactive({
		live_id:0,			//监控id
		info:null,			//设备信息
		loading:false,
		detail:null,		//坤典云设备详情
		qnDetail:null,		//七牛云设备详情
		gateway:null,		//网关信息
		
		channelList:[],		//通道
		channelTotal:{		//通道统计
			all:0,			//总数
			online:0,		//在线
			offline:0,		//离线
		},
		channel_id:null,	//选择的通道id
		webrtc:'',			//wertc 播放地址
		flv:'',				//flv 播放地址
		fls:'',				//fls 播放地址
	})
	
	//获取监控详情
	async function getInfo(id){
		state.loading = true
		try{
			state.live_id = id
			let res = await getLiveDetail({id:id})
			state.loading = false
			if( res.code === 200 ){
				state.info = res.data
				state.info = res.data
				state.detail = res.data?.details?.device || null
				state.qnDetail = res.data?.details?.qiniu_device || null
			}
		}catch(e){
			
		}
	}
	
	//获取通道列表数据
	async function getChannel(){
		let res = await getLiveChanngeList({id:state.live_id})
		if( res.code === 200 ){
			state.channelList = res.data?.items || []
			state.channelTotal.all = res.data?.total || 0
			state.channelTotal.online = res.data?.onlineCount || 0
			state.channelTotal.offline = res.data?.offlineCount || 0
			
			//设置初始选择的通道id
			if( state.channelList.length ){
				state.channel_id = state.channelList[0].channelId
			}
		}
	}
	
	//切换播放地址
	function getPlayUrl(){
		let webrtc = state.detail.webrtc
		let flv = state.detail.flv
		let hls = state.detail.hls
		//平台设备用通道的播放地址
		if( state.detail.type === 2 ){
			for (let i = 0; i < state.channelList.length; i++) {
				if( state.channelList[i].channelId === state.channel_id ){
					webrtc = state.channelList[i].webrtc
					flv = state.channelList[i].flv
					hls = state.channelList[i].m3u8
				}
			}
		}
		state.webrtc = webrtc
		state.flv = flv
		state.hls = hls
	}
	return {
		state,getInfo,getChannel,getPlayUrl
	}
})