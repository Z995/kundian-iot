import { createApp } from 'vue'
import * as Pinia from 'pinia';
import App from './App.vue'
import './index.css';
// UI组件引入
import ArcoVue from '@arco-design/web-vue';
import ArcoVueIcon from '@arco-design/web-vue/es/icon';
import '@arco-design/web-vue/dist/arco.css';
import 'animate.css';

// 滑块验证
import sliderVerify from 'vue3-slider-verify'
import 'vue3-slider-verify/lib/style.css';

// 路由文件引入
import router from './router/index'
import request from '@/api/request'

// 加载进度样式
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

//自定义组件引入
import KdPageBox from '@/components/KdPageBox/index.vue'
import KdPager from '@/components/KdPager/index.vue';
import KdBack from '@/components/KdBack/index.vue'
import KdEmpty from '@/components/KdEmpty/index.vue'
import KdCopy from '@/components/KdCopy/index.vue'

//第三方工具引入
import Clipboard from 'v-clipboard3'

import {useConfigStore}  from '@/store/config'

NProgress.configure({
    easing: 'ease',  	// 动画方式
    speed: 500,  		// 递增进度条的速度
    showSpinner: false, // 是否显示加载ico
    trickleSpeed: 200, 	// 自动递增间隔
    minimum: 0.3 		// 初始化时的最小百分比
})


const app = createApp(App);
app.use(ArcoVue);
app.use(ArcoVueIcon);
app.use(router)
app.use(Clipboard)
app.use(sliderVerify)
app.component('kd-page-box',KdPageBox)
app.component('kd-pager',KdPager)
app.component('kd-back',KdBack)
app.component('kd-empty',KdEmpty)
app.component('kd-copy',KdCopy)

//路由监听
router.beforeEach((to,next)=>{
	NProgress.start();
	
	//判断当前是否登录，没有登录则跳转登录页面
	let token = localStorage.getItem('_IOT_TOKEN_')
	if( !token && to.path !=='/login' && to.path !== '/live/wxlive'){
		return '/login'
	}
	
	//判断当前页面是否缓存
	if( to.meta.keepAlive ){
		const configState = useConfigStore()
		configState.setKeepLive(to.meta.name,true)
	}
	
	if( to.fullPath ==='/'){
		return '/index'
	}
	
})
router.afterEach(()=>{
    NProgress.done()
})
app.use(Pinia.createPinia());
app.mount("#app");
