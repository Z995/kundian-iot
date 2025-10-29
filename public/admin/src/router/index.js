import { createRouter, createWebHistory } from "vue-router";
import commonView from "@/pages/base.vue"
const routes = [
	{name:'登录',path:'/login',component:()=>import('@/pages/login/index.vue')},
	{name:'小程序监控播放',path:'/live/wxlive',component:()=>import('@/pages/live/wxlive.vue')},
	{
		path:'/',name:'admin',component:commonView,
		children:[
			{name:'总览',path:'/index',component:()=>import('@/pages/index/index.vue')},
			{name:'设备列表',path:'/device/list',component:()=>import('@/pages/device/list.vue'),meta:{keepAlive:true,name:'DeviceList'}},
			{name:'设备列表-新增/编辑',path:'/device/deviceAdd',component:()=>import('@/pages/device/deviceAdd.vue')},
			{name:'设备列表-设备详情',path:'/device/deviceDetail',component:()=>import('@/pages/device/deviceDetail.vue')},
			{name:'设备模板',path:'/device/mould',component:()=>import('@/pages/device/mould.vue'),meta:{keepAlive:true,name:'DeviceMouldList'}},
			{name:'设备模板-新增/编辑',path:'/device/mouldAdd',component:()=>import('@/pages/device/mouldAdd.vue')},
			{name:'产品库',path:'/device/product',component:()=>import('@/pages/device/product.vue'),meta:{keepAlive:true,name:'DeviceProductList'}},
			{name:'产品库-新增/编辑',path:'/device/productAdd',component:()=>import('@/pages/device/productAdd.vue')},
			{name:'网关列表',path:'/gateway/list',component:()=>import('@/pages/gateway/list.vue'),meta:{keepAlive:true,name:'GatewayList'}},
			{name:'网关详情',path:'/gateway/detail',component:()=>import('@/pages/gateway/detail.vue')},
			{name:'网关型号',path:'/gateway/mould',component:()=>import('@/pages/gateway/mould.vue')},
			{name:'网关日志',path:'/gateway/log',component:()=>import('@/pages/gateway/log.vue')},
			{name:'网关型号-新增/编辑',path:'/gateway/mouldAdd',component:()=>import('@/pages/gateway/mouldAdd.vue')},
			{name:'数据中心-数据统计',path:'/statistics/index',component:()=>import('@/pages/statistics/index.vue')},
			{name:'数据中心-历史记录',path:'/statistics/history',component:()=>import('@/pages/statistics/history.vue')},
			{name:'数据中心-变量报警记录',path:'/statistics/dataAlarmRecord',component:()=>import('@/pages/statistics/dataAlarmRecord.vue')},
			{name:'数据中心-设备上下线',path:'/statistics/deviceOnlineRecord',component:()=>import('@/pages/statistics/deviceOnlineRecord.vue')},
			{name:'数据中心-网关报警记录',path:'/statistics/gatewayAlarmRecord',component:()=>import('@/pages/statistics/gatewayAlarmRecord.vue')},
			{name:'数据中心-网关上下线',path:'/statistics/gatewayOnlineRecord',component:()=>import('@/pages/statistics/gatewayOnlineRecord.vue')},
			{name:'数据中心-联动记录',path:'/statistics/linkRecord',component:()=>import('@/pages/statistics/linkRecord.vue')},
			{name:'报警联动-报警联系人',path:'/alarm/contacts',component:()=>import('@/pages/alarm/contacts.vue')},
			{name:'报警联动-报警配置',path:'/alarm/alarmSet',component:()=>import('@/pages/alarm/alarmSet.vue')},
			{name:'报警联动-独立触发器',path:'/alarm/independenceTrigger',component:()=>import('@/pages/alarm/independenceTrigger.vue')},
			{name:'报警联动-模板触发器',path:'/alarm/mouldTrigger',component:()=>import('@/pages/alarm/mouldTrigger.vue')},
			{name:'用户管理-用户列表',path:'/account/list',component:()=>import('@/pages/account/list.vue')},
			{name:'监控管理-监控列表',path:'/live/list',component:()=>import('@/pages/live/list.vue'),meta:{keepAlive:true,name:'LiveList'}},
			{name:'监控管理-监控平台',path:'/live/platform',component:()=>import('@/pages/live/platform.vue')},
			{name:'监控管理-添加监控',path:'/live/addLive',component:()=>import('@/pages/live/addLive.vue')},
			{name:'监控管理-监控设备详情',path:'/live/detail',component:()=>import('@/pages/live/detail.vue')}
		]
	},
]
export default createRouter({
    history: createWebHistory(),
    routes,
});