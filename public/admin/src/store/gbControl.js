import { defineStore  } from 'pinia'
import {reactive} from 'vue'
export const useGbControlState = defineStore('gbControl',()=>{
	const state = reactive({
		device_id:0,
		active:1,		//1云台控制 2设备通道 3预置位 4巡航组
		channel_id:0,	//当前选中通道id
		preset_id:0,	//当前选中预置位id
		cruse_id:0,		//当前选中巡航组id
		presetList:[],	//设备预置位
		cruseList:[],	//设备巡航组
		channelList:[],	//通道列表
		loading:false,	//数据加载中提示
		playType:null,
		device:null,		//设备详情
		user_device:null,	//设备播放地址
		
		player:null,		//播放对象
		state:"loading",		//loading加载中 001正常播放 其他报错
		
		timer:null,
		time:10,
		
		// m3u8播放
		is_full_screen:false,	//是否为全屏播放
		url:"",
		isLoading:true,
		h5:{
			showLoading:false,
			player:null
		}
		
	})
	
	return {state}
})