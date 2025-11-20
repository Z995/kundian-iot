<!-- 坤典智慧农场V6 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 文件路径与名称:  widsomFarmV6_admin/src/components/miniApp/CqkdJessibucaPlay/CqkdPlay.vue -->
<!-- @description File path and name: widsomFarmV6_admin/src/components/miniApp/CqkdJessibucaPlay/CqkdPlay.vue-->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<template>
    <div class="root">
        <div class="container-shell">
            <div class="container" :id="props.id">
                <div class="error-box" v-if="state.errorText">{{state.errorText}}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {reactive, ref,watch } from "vue";
import {onMounted, onUnmounted,defineProps} from "vue";
const props = defineProps({
	//播放容器id
	id:{
		type:String,
		default:"livePlayBox"
	},
	//播放地址
	src:{
		type:String,
		default:"",
	}
})
const errorCode = {
    "fetchError":"http 请求失败",
    "playError":"播放错误",
    "websocketError":"websocket 请求失败",
    "webcodecsH265NotSupport":"webcodecs 解码 h265 失败",
    "mediaSourceH265NotSupport":"mediaSource 解码 h265 失败",
    "wasmDecodeError":"wasm 解码失败",
}
const state = reactive({
    errorText:"",
})
let jessibuca = {};
const buffer = ref(null);
const showBandwidth = ref(true)
const showOperateBtns = ref(true);
const forceNoOffscreen = ref(false);
const playing = ref(false);
const quieting = ref(true);
const loaded = ref(false);
const createJessibuca = () => {
    jessibuca[props.id] = new window.Jessibuca({
        container: document.getElementById(`${props.id}`),
        videoBuffer: 0.2, // 缓存时长
        isResize: false,
        text: "",
        decoder:'/static/js/jessibuca/decoder.js',
        loadingText: "加载中,请稍后",
        debug: import.meta.env.MODE === 'development' ?true:false,
        showBandwidth: showBandwidth.value, // 显示网速
        operateBtns: {
            fullscreen: showOperateBtns.value,
            screenshot: showOperateBtns.value,
            play: false,
            audio: showOperateBtns.value,
        },
        forceNoOffscreen: forceNoOffscreen.value,
        isNotMute: false,
    })
    jessibuca[props.id].on("load", () => {
        if( import.meta.env.MODE === 'development' ){
			console.log('加载完成，开始播放...')
		}
        play()
    })

    jessibuca[props.id].on("log", function (msg) {
        if( import.meta.env.MODE === 'development' ){
			console.log("on log", msg);
		}
    });
    jessibuca[props.id].on("record", function (msg) {
        if( import.meta.env.MODE === 'development' ){
			console.log("on record:", msg);
		}
    });
    jessibuca[props.id].on("pause", function () {
        if( import.meta.env.MODE === 'development' ){
			console.log("on pause");
		}
        playing.value = false;
    });
    jessibuca[props.id].on("play", function () {
        if( import.meta.env.MODE === 'development' ){
			console.log("on play");
		}
        state.errorText = ''
        playing.value = true;
        loaded.value = true;
        quieting.value = jessibuca[props.id].isMute();
    });
    jessibuca[props.id].on("fullscreen", function (msg) {
        if( import.meta.env.MODE === 'development' ){
			console.log("on fullscreen", msg);
		}
    });

    jessibuca[props.id].on("mute", function (msg) {
        if( import.meta.env.MODE === 'development' ){
			console.log("on mute", msg);
		}
        quieting.value = msg;
    });

    jessibuca[props.id].on("mute", function (msg) {
        if( import.meta.env.MODE === 'development' ){
			console.log("on mute2", msg);
		}
    });

    jessibuca[props.id].on("audioInfo", function (msg) {
        if( import.meta.env.MODE === 'development' ){
			console.log("audioInfo", msg);
		}
    });
    jessibuca[props.id].on("videoInfo", function (info) {
        if( import.meta.env.MODE === 'development' ){
			console.log("videoInfo", info);
		}
    });

    jessibuca[props.id].on("error", function (error) {
        if( import.meta.env.MODE === 'development' ){
			console.log("error", error);
		}
        state.errorText = errorCode[error] || '未知错误'
    });

    jessibuca[props.id].on("timeout", function () {
        if( import.meta.env.MODE === 'development' ){
			console.log("timeout");
		}
    });

    jessibuca[props.id].on('start', function () {
        if( import.meta.env.MODE === 'development' ){
			console.log('start');
		}
    })

    jessibuca[props.id].on("performance", function (performance) {
        var show = "卡顿";
        if (performance === 2) {
            show = "非常流畅";
        } else if (performance === 1) {
            show = "流畅";
        }
    });
    jessibuca[props.id].on('buffer', function (buffer) {
        if( import.meta.env.MODE === 'development' ){
			console.log('buffer', buffer);
		}
    })

    jessibuca[props.id].on('stats', function (stats) {
        if( import.meta.env.MODE === 'development' ){
			console.log('stats', stats);
		}
    })

    jessibuca[props.id].on('kBps', function (kBps) {
        if( import.meta.env.MODE === 'development' ){
			console.log('kBps', kBps);
		}
    });

    // 显示时间戳 PTS
    jessibuca[props.id].on('videoFrame', function () {

    })

    //
    jessibuca[props.id].on('metadata', function () {

    });
}
watch(()=>props.src,val=>{
	if( !val ) return ''
	jessibuca && jessibuca[props.id] && jessibuca[props.id].destroy();
	//等待切换播放组件销毁完成后，在初始化创建
	setTimeout(()=>{
		createJessibuca();
	},1000)
},{deep:true})

onMounted(() => {
    createJessibuca();
})
onUnmounted(() => {
    if( import.meta.env.MODE === 'development' ){
		console.log('离开',jessibuca[props.id])
	}
    jessibuca[props.id] && jessibuca[props.id].destroy();
})

const play = () => {
    if (props.src) {
        jessibuca[props.id].play(props.src);
    }
}
const pause = () => {
    jessibuca[props.id].pause();
    playing.value = false;
}

const destroy = ()=>{
    if (jessibuca[props.id]) {
        jessibuca[props.id].destroy();
    }
    createJessibuca();
    playing.value = false;
    loaded.value = false;
}


</script>
<style lang="scss" scoped>
.root {
    width: 100%;
    height: 100%;
}

.container-shell {
    width: 100%;
    height: 100%;
    display: flex;
    position: relative;
    width: auto;
    position: relative;
    border-radius: 5px;
}

.container {
    background: rgba(9, 9, 10, 0.7);
    width: 100%;
    height: 100%;

    .error-box{
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: red;
    }
}
</style>
