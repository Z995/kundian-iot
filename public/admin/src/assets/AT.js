//AT指令合集

//name:指令说明 command:指令 type:指令类型[button:按钮类型 select:查询类型 set:设置类]
//setParam.mode.type [select:下拉选择框,input:输入框 ，switch:开关 ]
const AT_LIST = [
	{name:"重启",command:"AT+Z",type:["button"]},
	{name:"保存配置并重启",command:"AT+S",type:["button"]},
	{name:"恢复出厂并重启",command:"AT+CLEAR",type:["button"]},
	{name:"工作模式",command:"AT+WKMOD",type:["select","set"],
		setParam:{
			mode:{
				type:"select",
				label:"设置工作模式",
				option:[
					{value:"CMD",label:"指令模式"},
					{value:"NET",label:"网络透传模式",},
					{value:"HTTPD",label:"HTTP透传模式"},
				],
			}
		},
	},
	{name:"信号强度",command:"AT+CSQ",type:["select"]},
	{name:"连接制式",command:"AT+SYSINFO",type:["select"]},
	{name:"固件版本号",command:"AT+VER",type:["select"]},
	{name:"固件编译时间",command:"AT+BUILD",type:["select"]},
	{name:"查询 SN 码",command:"AT+SN",type:["select"]},
	{name:"查询 IMEI 号",command:"AT+IMEI",type:["select"]},
	{name:"查询 IMSI",command:"AT+IMSI",type:["select"]},
	{name:"查询 ICCID 码",command:"AT+ICCID",type:["select"]},
	{name:"查询 SIM 卡电话号码",command:"AT+CNUM",type:["select"]},
	{name:"小区基站信息",command:"AT+LBS",type:["select"]},
	{name:"邻小区基站信息",command:"AT+LBSN",type:["select"]},
	{name:"查询时间",command:"AT+CCLK",type:["select"]},
	{name:"安全机制使能",command:"AT+SAFEATEN",type:["select","set"]},
	{name:"登录/设置登录密码",command:"AT+SIGNINAT",type:["button","set"],
		setParam:{
			password:{
				type:"input",
				label:"登录密码",
			}
		},
	},
	{name:"串口参数",command:"AT+UART",type:["select","set"],
		setParam:{
			baud:{
				type:"input",
				label:"波特率",
				default_value:"115200"
			},
			data:{
				type:"select",
				label:"数据位",
				option:[
					{value:"8",label:"8 位数据"},
				],
				default_value:"8"
			},
			stop:{
				type:"select",
				label:"停止位",
				option:[
					{value:"1",label:"1 位停止位"},
					{value:"2",label:"2 位停止位"},
				],
				default_value:"1"
			},
			parity:{
				type:"select",
				label:"校验方式",
				option:[
					{value:"NONE",label:"无校验"},
					{value:"ODD",label:"奇校验"},
					{value:"EVEN",label:"偶校验"},
				],
				default_value:"NONE"
			},
			flow:{
				type:"select",
				label:"流控",
				option:[
					{value:"NONE",label:"无流控"},
					{value:"RS485",label:"RS485 流控"},
				],
				default_value:"RS485"
			},
		},
	},
	{name:"串口打包长度",command:"AT+UARTFL",type:["select","set"],
		setParam:{
			len:{
				type:"input",
				label:"打包长度",
				default_value:1024,
				unit:"Bytes",
				min:5,
				max:4096,
			}
		},
	},
	{name:"串口打包时间",command:"AT+UARTFT",type:["select","set"],
		setParam:{
			time:{
				type:"input",
				label:"打包时间",
				default_value:50,
				unit:"毫秒",
				min:0,
				max:500,
			}
		},
	},
]

export default AT_LIST