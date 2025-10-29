<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/src/pages/account/list.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-06-04 14:57:57  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card title="用户列表" :bordered="false">
			<template #extra>
				<a-button type="primary" @click="toAdd(null)">
					<i class="ri-add-line"></i>添加用户
				</a-button>
			</template>
			<!-- 搜索参数 -->
			<a-space>
				<a-input v-model="state.search.name" placeholder="用户姓名，手机号或登录账户" class="w300"></a-input>
				<a-button type="primary" :loading="state.loading" @click="getList(1,state.info.limit)">查询</a-button>
				<a-button @click="resetData">重置</a-button>
			</a-space>
			<a-table class="kd-small-table mt20" :pagination="false" row-key="id"
				:data="state.info.list"
				:loading="state.loading"
				:bordered="false"
				:columns="[
					{title:'ID',dataIndex:'id',width:80},
					{title:'用户姓名',dataIndex:'name'},
					{title:'登录账号',dataIndex:'phone'},
					{title:'设备数量',dataIndex:'device_count'},
					{title:'网关数量',dataIndex:'gateway_count'},
					{title:'状态',slotName:'status'},
					{title:'注册时间',dataIndex:'create_time'},
					{title:'操作',slotName:'action',width:190},
				]"
			>
				<template #status="{record}">
					<a-tag color="#00CC66" v-if="record.status==1">启用</a-tag>
					<a-tag color="#FF9933" v-else>禁用</a-tag>
				</template>
				<template #action="{record}">
					<a-button type="text" size="mini" class="ml-10" @click="toAdd(record)">编辑</a-button>
					<a-button type="text" size="mini" class="ml-10" @click="updatePwd(record)">修改密码</a-button>
					<a-popconfirm content="确认删除用户吗?" @ok="delData(record.id)">
						<a-button type="text" size="mini">删除</a-button>
					</a-popconfirm>
				</template>
			</a-table>
			<kd-pager :page-data="state.info" :event="getList"></kd-pager>
		</a-card>
	</div>
	<kd-account-add ref="addRef" @success="getList"></kd-account-add>
	<kd-update-login-pwd ref="pwdRef"></kd-update-login-pwd>
</kd-page-box>
</template>

<script setup>
import { reactive,onMounted, ref } from 'vue';
import KdAccountAdd from './components/KdAccountAdd.vue';
import KdUpdateLoginPwd from './components/KdUpdateLoginPwd.vue';
import { getAccountList ,deleteAccountData } from '@/api/kdAccount'
import { Message } from '@arco-design/web-vue';
const addRef = ref()
const pwdRef = ref()
const state = reactive({
	loading:false,
	search:{
		name:'',
	},
	info:{
		list:[],
		page:1,
		limit:10,
		count:0,
	},
})

onMounted(()=>{
	getList(1,10)
})

function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	let param = { 
		page:state.info.page,
		limit:state.info.limit,
	}
	if( state.search.name ) param.key = state.search.name 
	state.loading = true
	getAccountList(param).then(res=>{
		state.loading = false
		state.info.list = res.data.list
		state.info.count = res.data.count
	}).catch(()=>{
		state.loading = false
	})
}

//添加，编辑
function toAdd(data){
	addRef.value.show(data)
}
//修改密码
const updatePwd = (data)=>pwdRef.value.show(data)
//重置搜索
function resetData(){
	state.search = {}
	getList(1,state.info.limit)
}
function delData(id){
	deleteAccountData({id}).then(res=>{
		if( res.code === 200 ){
			Message.success("删除成功")
			getList()
			return;
		}
		Message.error(res.msg)
	})
}
</script>

<style lang="scss" scoped>

</style>