<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/components/select/CqkdSelectLabel.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-05-29 10:42:16  -->
<template>
<div v-if="state.show">
	<a-modal title="选择标签" v-model:visible="state.show" width="800px" :on-before-ok="sureSelect" :mask-closable="false">
		<div class="label-box">
			<div class="label-item" v-for="(item,index) in state.list" :key="index">
				<div class="flex-cb">
					<span class="label-name f17">{{ item.name }}</span>
					<a-popconfirm content="确认删除类别吗？" @ok="delType(item.id)">
						<a-button type="text" status="danger">
							<i class="ri-delete-bin-2-line mr5"></i>删除类别
						</a-button>
					</a-popconfirm>
				</div>
				<div class="label-ul flex">
					<div class="label-ul-li" v-for="(val,ind) in item.label" :class="{active:val.is_checked}"
						@click.stop="val.is_checked =!val.is_checked"
					>
						{{val.name}}
						<div class="del-icon" @click.stop="delData(val.id)"><i class="ri-close-line"></i></div>
						<div class="check-icon"><i class="ri-check-line"></i></div>
					</div>
					
					<template v-if="item.show_add">
						<a-input placeholder="请输入标签名称" v-model="state.form.label_name" class="w160"></a-input>
						<a-button @click="item.show_add = false">取消</a-button>
						<a-button type="primary" @click="saveData(item.id)">添加</a-button>
					</template>
					<div class="add-label" v-else @click="item.show_add = true">
						<i class="ri-add-line"></i>添加标签
					</div>
				</div>
			</div>
		</div>
		<div class="footer-box mt20 flex-c">
			<template v-if="state.show_add_type">
				<a-input placeholder="请输入类别名称" v-model="state.form.type_name" class="w160"></a-input>
				<a-button class="ml10" @click="state.show_add_type = false">取消</a-button>
				<a-button type="primary" class="ml10" @click="saveTypeData">添加</a-button>
			</template>
			<div class="add-label" v-else @click="state.show_add_type = true">
				<i class="ri-add-line"></i>新建类别
			</div>
		</div>
	</a-modal>
</div>
</template>

<script setup>
import { Message,Modal } from '@arco-design/web-vue';
import { reactive } from 'vue';
import { 
	getLabelCateList,saveLabelCateData,deleteLabelCateData,
	saveLabelData,deleteLabelData
 } from '@/api/kdLabel'
const emits = defineEmits(['success'])
const state = reactive({
	show:false,
	list:[],
	show_add_type:false,
	form:{
		type_name:"",
		label_name:""
	},
	checkedList:[],
})
function show(data){
	state.show = true
	if( data && data.length ){
		let checkArr = []
		data.forEach(val=>checkArr.push(parseInt(val)))
		state.checkedList =checkArr
	}
	getList()
}

async function getList(){
	let res = await getLabelCateList()
	state.list = res.data?.list || []
	//回显选中的数据
	state.list.forEach(item=>{
		if( item.label && item.label.length){
			item.label.forEach(val=>{
				val.is_checked = state.checkedList.includes(val.id)
			})
		}
	})
}

//保存标签分类
function saveTypeData(){
	let { type_name } = state.form
	if( !type_name ){
		Message.warning("请输入类别名称")
		return false
	}
	saveLabelCateData({name:type_name}).then(res=>{
		if( res.code === 200 ){
			Message.success("添加成功")
			state.form.type_name = ''
			state.show_add_type = false
			getList()
			return false
		}
		Message.error(res.msg)
	})
}

//删除类别
function delType(id){
	deleteLabelCateData({id}).then((res)=>{
		if( res.code === 200 ){
			Message.success("删除成功")
			getList()
			return
		}
		Message.error(res.msg)
	})
}

//保存标签
function saveData(category_id){
	let { label_name } = state.form
	if( !label_name ){
		Message.warning("请输入标签名称")
		return false
	}
	saveLabelData({name:label_name,category_id}).then(res=>{
		if( res.code === 200 ){
			Message.success("添加成功")
			state.form.label_name = ''
			let index = state.list.findIndex(item=>item.id == category_id)
			state.list[index].show_add = false
			getList()
			return false
		}
		Message.error(res.msg)
	})
}

//删除标签
function delData(id){
	Modal.confirm({
		title:'提示',
		content:"确认删除标签吗？",
		onOk:()=>{
			deleteLabelData({id}).then((res)=>{
				if( res.code === 200 ){
					Message.success("删除成功")
					getList()
					return
				}
				Message.error(res.msg)
			})
		}
	})
}

//确认选择
function sureSelect(){
	let labelData = [] ,labelId = []
	state.list.forEach(item=>{
		if( item.label && item.label.length){
			item.label.forEach(val=>{
				if( val.is_checked ){
					labelData.push(val)
					labelId.push(val.id)
				}
			})
		}
	})
	if( !labelId.length ){
		Message.warning('请选择标签')
		return false
	}
	emits("success",{ id:labelId, list:labelData })
	state.show = false
}

defineExpose({
	show
})
</script>

<style lang="scss" scoped>
.label-box{
	width: 100%;
	border-bottom: 1px solid #f4f4f4;
	.label-item{
		width: 100%;
		margin-bottom: 10px;
	}
	.label-ul{
		width: 100%;
		flex-wrap: wrap;
		gap:20px;
		margin-top: 10px;
		.label-ul-li{
			padding: 6px 10px 6px 15px;
			background: #efefef;
			border-radius: 3px;
			color: #777;
			cursor: pointer;
			position: relative;
			.del-icon{
				display: inline-block;
				width: 16px;
				height: 16px;
				border-radius: 50%;
				text-align: center;
				font-size: 14px;
				line-height: 16px;
				margin-left: 2px;
				color:#999;
				cursor: pointer;
				&:hover{
					background: #cecece;
					color: #555;
				}
			}
		
			.check-icon{
				display: none;
				background: #165dff;
				color: #fff;
				width: 16px;
				height: 16px;
				position: absolute;
				right: 0;
				top: 0;
				text-align: center;
				font-size: 10px;
				padding-left: 3px;
				border-radius: 0 3px 0 15px;
			}
		}
		.active{
			background: rgba(#165dff, .1);
			color: #165dff;
			.check-icon{
				display: inline-block;
			}
		}
	}
}
.add-label{
	padding: 6px 20px;
	background: #efefef;
	border-radius: 3px;
	color: #777;
	cursor: pointer;
	color: #165dff;
	display: inline-block;
}
</style>