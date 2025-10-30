import { defineStore  } from 'pinia'
import {reactive, ref} from 'vue'
import { Notification } from '@arco-design/web-vue';
export const useDeviceStore = defineStore('device', () => {
	const ws = ref()			//socket链接对象
	const state = reactive({
		url:"",					//socket连接地址
		attempt:0, 				// 增加尝试次数
		maxAttempts:5,			// 最大尝试次数
		reconnectInterval:null, 
		keepHeartInterval:null,	//保持心跳
		
		//设备详情对话调试内容
		dialogList:[],
		device_code:"",		//当前调试的设备号
		collectInfo:null,	//当前数据采集信息
	})
	
	function connect(url,device_code=""){
		if( url ){
			state.url = url
		}else{
			let local_url = 'wss://'+window.location.hostname+'/wss'
			if( window.location.port !=80 || window.location.port !=443){
				local_url = `${local_url}:${window.location.port}`
			}
			state.url = local_url
		}
		console.log('SOCKET_URL',state.url);
		state.device_code = device_code || ""	//保存当前操作的设备号
		//判断当前连接是否正常或者是否已连接
		if( ws.value && ws.value.readyState === WebSocket.OPEN ){
			if( state.device_code ){
				pushDebugLog('device',{msg:'connet success',code:state.device_code})
			}
		}else{
			ws.value = new WebSocket(state.url);
			ws.value.addEventListener('open', () => {
				//连接成功
				if( ws.value.readyState === WebSocket.OPEN ){
					console.log('WebSocket connected...');
					let token = `Bearer ${localStorage.getItem('_IOT_TOKEN_')}`
					ws.value.send(token)
					// ws.value.send("YS8GSOzeFXd9")
					//保持心跳
					setTimeout(()=>{
						sendHeartbeat()
					},3000)
				}
			});
			ws.value.addEventListener('message', (event) => {
				//接收到消息，重连次数清0
				state.attempt = 0 
				if( state.reconnectInterval ){
					clearInterval(state.reconnectInterval)
				}
				console.log("Received Message: " + event.data);
				if (event.data != '') {
				    //忽略success
				    if (event.data == 'connet success') {
						if( state.device_code ){
							pushDebugLog('device',{msg:event.data,code:state.device_code})
						}
				        return;
				    }
				    try {
				        var data = eval('(' + event.data + ')');
						
						// 用户主动采集数据
						if( data.k === state.device_code && data.v?.type ==='WebSocketEcho'){
							pushDebugLog('user',{msg:data.v?.data?.command,code:state.device_code})
						}
						
						if( data.k === state.device_code && ( !data.v?.type || data.v?.type !=='WebSocketEcho')){
							pushDebugLog('device',{msg:data.v,time:data.t,code:data.k})
						}
						if( typeof data.v ==='object' ){
							if( ['Online'].includes(data.v.type)){
								showMessageNotification({
									code:data.k,
									time:data.t,
									type:data.v.type,
									data:data.v.data
								})
							}
						}
						// 当前数据采集
						if( data.v?.type ==='ModbusRTUS'){
							state.collectInfo = {
								device_code:data.k,
								data:data.v.data		//采集返回的数据
							}
						}
				    } catch (e) {
				        return;
				    }
				}
			});
			ws.value.addEventListener('close', (event) => {
				console.log('websocket 已断开链接',event.wasClean);
				//非正常断开,重连
				if( !event.wasClean ){
					reconnect()
				}
			});
		}
	}
	
	//保持心跳
	function sendHeartbeat(){
		if( ws.value.readyState === WebSocket.OPEN ){
			state.keepHeartInterval = setInterval(()=>{
				ws.value.send("0000")
			},3000)
		}
	}
	
	//关闭连接
	function close(){
		ws.value.close()
	}
	//重连
	function reconnect(){
		state.attempt++; // 增加尝试次数
		console.log(`正在尝试重连${state.attempt}次...`);
		if (state.attempt <= state.maxAttempts) {
			state.reconnectInterval = setTimeout(() => {
				connect(state.url,state.device_code); 	// 重新连接
			}, 1000 * Math.pow(2, state.attempt)); 		// 使用指数退避策略增加重连间隔时间，例如：1s, 2s, 4s, 8s, 16s...
		} else {
			console.error('websocket连接已超最大尝试次数');
		}
	}
	
	//显示消息弹框
	function showMessageNotification(data){
		console.log('data',data);
		if( data.type === 'Online'){
			Notification.info({
				title:`${data.code}设备${data.data.online == 1 ?'上线':'离线'}通知`,
				content:`${data.time}`,
				duration:10000
			})
		}
	}
	
	//发送调试消息
	function sendDebugMsg(data){
		ws.value.send(JSON.stringify(data))
		pushDebugLog('user',{msg:data.val,code:state.device_code})
	}
	
	//追加调试数据
	function pushDebugLog(type,data){
		state.dialogList.push({
			type: type,
			code: type ==='user'?localStorage.getItem("_IOT_USER_"):data.code,
			time: data.time ? data.time : getTime(),
			message:data.msg
		})
		
	}
	
	function getTime(){
		var time = new Date();
		var m = time.getMonth() + 1;
		var t = time.getFullYear() + "-" + m + "-"
		    + time.getDate() + " " + time.getHours() + ":"
		    + time.getMinutes() + ":" + time.getSeconds();
		return t;
	}
	
	return {
		state,ws,connect,sendDebugMsg
	}
})