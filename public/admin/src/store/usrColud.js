import { defineStore  } from 'pinia'
import {reactive,h} from 'vue'
import { Notification } from '@arco-design/web-vue';

export const useUsrCloudState = defineStore('usrCloud',()=>{
	const state = reactive({
        AppKey:'',          //有人云AppKey
        APPSecret:'',       //有人云AppSecret
    })

    //初始化有人云sdk
    function initSdk(){
        window.usrsdk.init({
            appKey: state.AppKey,
            appSecret:state.APPSecret
        }).then(function(){
            //初始化成功后会执行
            console.log("init complete");
            listenNetStatus()
            listenDeviceStatus()
        }).catch(function(e){
            //初始化失败后会执行
            console.log("error:"+e);
        });
    }

    //监听网关上下线
    function listenNetStatus(){
        window.usrsdk.addListener("deviceStatus",{},function(username,data){
            //网关上下线
            if( data.c?.type =='onlineOffline'){
                //上线
                if( data.c.value == 1 ){
                    Notification.success({
                        title:`网关上线通知`,
                        content:()=>[
                            h('p',{class:'mt10'},`状态：上线`),
                            h('p',{class:'mt10'},`网关SN：${data.c?.deviceNo}`)
                        ],
                        duration:10000
                    })
                }else{
                    //下线
                    Notification.error({
                        title:`网关下线通知`,
                        content:()=>[
                            h('p',{class:'mt10'},`状态：下线`),
                            h('p',{class:'mt10'},`网关SN：${data.c?.deviceNo}`)
                        ],
                        duration:10000
                    })
                }
            }
        })
    }

    //监听设备上下线
    function listenDeviceStatus(){
        window.usrsdk.addListener("cusdeviceStatus",{},function(username,data){
            //设备上下线
            if( data.c?.type =='cusdeviceStatus'){
                //上线
                if( data.c.value == 1 ){
                    Notification.success({
                        title:`设备上线通知`,
                        content:()=>[
                            h('p',{class:'mt10'},`状态：上线`),
                            h('p',{class:'mt10'},`设备编号：${data.c?.cusdeviceNo}`)
                        ],
                        duration:10000
                    })
                }else{
                    //下线
                    Notification.error({
                        title:`设备下线通知`,
                        content:()=>[
                            h('p',{class:'mt10'},`状态：下线`),
                            h('p',{class:'mt10'},`设备编号：${data.c?.cusdeviceNo}`)
                        ],
                        duration:10000
                    })
                }
            }
        })
    }

    return {
        state,
        initSdk
    }
})