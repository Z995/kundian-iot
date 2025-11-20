const isEnv = import.meta.env.MODE === 'development'

const menuData = [
	{id:'total',name:'概览',path:'/index',show:true,icon:"icon-18jiankonggailan"},
	{
		id:'account',name:'用户',path:'',show:true,icon:"icon-yonghu",
		children:[
			{id:'accountList',name:'用户列表',path:'/account/list',show:true},
		],
	},
	{
		id:'device',name:'设备',path:'',show:true,icon:"icon-wangguanshebei",
		children:[
			{id:'deviceList',name:'设备列表',path:'/device/list',show:true},
			{id:'deviceTemp',name:'设备模板',path:'/device/mould',show:true},
			{id:'deviceProduct',name:'产品库',path:'/device/product',show:true},
		],
	},
	{
		id:'net',name:'网关',path:'',show:true,icon:"icon-wangguanshebei1",
		children:[
			{id:'netList',name:'网关列表',path:'/gateway/list',show:true},
			{id:'netMould',name:'网关型号',path:'/gateway/mould',show:true},
		],
	},
	{
		id:'live',name:'监控',path:'',show:true,icon:"icon-jiankongshexiangtou",
		children:[
			{id:'liveList',name:'监控列表',path:'/live/list',show:true},
			{id:'livePlatform',name:'监控配置',path:'/live/platform',show:true},
		],
	},
	{
		id:'deviceData',name:'数据',path:'',show:true,icon:"icon-tongji",
		children:[
			{id:'deviceDataTotal',name:'数据统计',path:'/statistics/index',show:true},
			{id:'deviceDataHistory',name:'历史记录',path:'/statistics/history',show:true},
			{id:'deviceDataAlarm',name:'变量报警记录',path:'/statistics/dataAlarmRecord',show:true},
			{id:'deviceDataLine',name:'设备上下线',path:'/statistics/deviceOnlineRecord',show:true},
			{id:'deviceDatalinkRecord',name:'联动记录',path:'/statistics/linkRecord',show:true},
			// {id:'gatewayDataAlarm',name:'网关报警记录',path:'/statistics/gatewayAlarmRecord',show:true},
			{id:'gatewayDataLine',name:'网关上下线',path:'/statistics/gatewayOnlineRecord',show:true},
		],
	},
	{
		id:'alarm',name:'联动',path:'',show:true,icon:"icon-jingbao",
		children:[
			{id:'alarmTempTrigger',name:'模板触发器',path:'/alarm/mouldTrigger',show:true},
			{id:'alarmAloneTrigger',name:'独立触发器',path:'/alarm/independenceTrigger',show:true},
			// {id:'alarmSet',name:'报警配置',path:'/alarm/alarmSet',show:true},
			// {id:'alarmUser',name:'报警联系人',path:'/alarm/contacts',show:true},
		],
	},
	{
		id:'produce',name:'生产',path:'',show:isEnv,icon:"icon--shengchanzuoye",
		children:[
			{id:'greenhouse',name:'大棚',path:'/greenhouse/index',show:true,icon:"icon-dapengfenjian"},
			{id:'producePlantManagement',name:'种植管理',path:'/produce/plantManagement',show:true},
			// {id:'producePark',name:'园区管理',path:'/produce/park',show:true},
			{id:'produceGrowthStandards',name:'作物生长标准',path:'/produce/growthStandards',show:true},
			{id:'produceFarmingType',name:'农事类型',path:'/produce/farmingType',show:true},
			{id:'produceFarmingList',name:'农事作业',path:'/produce/farmingList',show:true},
			{id:'produceMachineryManagement',name:'农机管理',path:'/produce/machineryManagement',show:true},
			{id:'produceWarehouseList',name:'仓库管理',path:'/produce/warehouseList',show:true},
			{id:'produceMaterialList',name:'物料管理',path:'/produce/materialList',show:true},
			{id:'produceSupplierList',name:'供应商管理',path:'/produce/supplierList',show:true},
			{id:'producePickingWarehousing',name:'采摘入库',path:'/produce/pickingWarehousing',show:true},
		],
	},
	{
		id:'staff',name:'员工',path:'',show:isEnv,icon:"icon-yonghu",
		children:[
			{id:'staffList',name:'员工列表',path:'/staff/list',show:true}
		],
	}
]

export default menuData