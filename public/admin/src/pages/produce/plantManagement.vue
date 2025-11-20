<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/plantManagement.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-07 14:18:43  -->
<template>
    <kd-page-box>
		<div class="kd-content">
			<a-form :model="state.search" layout="inline">
				<a-form-item label="生长标准">
					<a-select v-model="state.search.type" placeholder="请选择" class="w140" allow-clear>
					    <a-option>苹果</a-option>
					    <a-option>桃子</a-option>
					</a-select>
				</a-form-item>
				<a-form-item label="大棚">
					<a-select v-model="state.search.type" placeholder="请选择" class="w140" allow-clear>
					    <a-option>大棚A</a-option>
					    <a-option>大棚B</a-option>
					</a-select>
				</a-form-item>
				<a-form-item label="种植名称">
					<a-input v-model="state.search.name" placeholder="种植名称" class="w300"></a-input>
					<a-button type="primary" class="ml10">搜索</a-button>
					<a-button class="ml10">重置</a-button>
				</a-form-item>
			</a-form>
		</div>
        <div class="kd-content mt10">
           <a-button type="primary" @click="showAdd(null)">
               <i class="ri-add-line"></i>添加种植
           </a-button>
           <div class="plantManagement-list mt10">
               <div class="plantManagement-list-item" v-for="(item,index) in state.info.list" :key="index">
                   <div class="mb10">{{ item.name }} (大棚A)</div>
                   <div class="item-data">
                       <div class="ml6">
                           <div>种植日期：{{ item.time }}</div>
                           <div class="mt6 f12">已种植：{{ item.grow }}天 <a-tag class="ml6" color="#00b42a" size="small">挂果</a-tag></div>
                           <div class="mt6 f12">剩余生长时长：{{ item.residualGrowthTime }}天</div>
                           <div class="mt6 f12">规模：{{ item.scale }}亩</div>
                           <div class="mt6 f12">已采摘：100kg</div>
                       </div>
                       <a-image class="img" width="70" :src="item.image" />
                   </div>
                   <div class="flex-c item-btn">
                       <a-button type="primary" size="mini" class="ml10" @click="showAdd(item)">采摘</a-button>
                       <a-button type="primary" size="mini" class="ml10" @click="showDetail(item)">详情</a-button>
                   </div>
               </div>
           </div>
           <kd-pager :page-data="state.info" :event="getList"></kd-pager>
        </div>
        <cqkd-plant-add ref="plantaddRef"></cqkd-plant-add>
		<cqkd-plant-detail ref="detailRef"></cqkd-plant-detail>
    </kd-page-box>
</template>
<script setup>
import { reactive,onMounted,ref } from 'vue';
import CqkdPlantAdd from './components/CqkdPlantAdd.vue';
import CqkdPlantDetail from './components/plant/CqkdPlantDetail.vue';
const plantaddRef = ref()
const detailRef = ref()
const state = reactive({
	loading:false,
	info:{
		list:[],
		page:1,
		limit:20,
		count:0
	},
	search:{
		type:null,
		plot_id:null,
		name:"",
	}
})

onMounted(() =>{
	getList(state.page,state.limit)
})

function getList(page,limit){
	state.info.page = page || state.info.page
	state.info.limit = limit || state.info.limit
	let param = {
		page:state.info.page,
		limit:state.info.limit,
	}
	state.loading = true
	// 模拟数据
	state.info.list = [
		{id:1,image:'http://60.247.225.87:8081/file/customizeImg/1kXB71741489519843OU0wG.png',name:'苹果',type:'生鲜瓜果',
		 grow:'1',residualGrowthTime:"挂果-55",scale:100,remarks:'',time:'2025-11-07'},
		{id:2,image:'http://60.247.225.87:8081/file/customizeImg/1kXB71741489519843OU0wG.png',name:'桃子',type:'生鲜瓜果',
		 grow:'1',residualGrowthTime:"挂果-55",scale:45,remarks:'',time:'2025-11-07'},  
		{id:3,image:'http://60.247.225.87:8081/file/customizeImg/1kXB71741489519843OU0wG.png',name:'李子',type:'生鲜瓜果',
		 grow:'1',residualGrowthTime:"挂果-55",scale:89,remarks:'',time:'2025-11-07'},  
		{id:4,image:'http://60.247.225.87:8081/file/customizeImg/1kXB71741489519843OU0wG.png',name:'草莓',type:'生鲜瓜果',
		 grow:'1',residualGrowthTime:"挂果-55",scale:114,remarks:'',time:'2025-11-07'},  
	]
}

function showAdd(data){
	plantaddRef.value.show(data)
}
function showDetail(data){
	detailRef.value.show(data)
}
</script>
<style lang="scss" scoped>
    .plantManagement-list{
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
		gap:20px;
        &-item{
            max-width: 320px;
            min-width: 220px;
            width: 50%;
            padding: 10px;
			border: 2px dashed #f0f0f0;
			cursor: pointer;
			position: relative;
            .mb10{
                margin-bottom: 10px;
            }
            .item-data{
                display: flex;
				font-weight: 300;
                .ml6{
                    margin-left: 6px;
                }
                .mt6{
                    margin-top: 6px;
                }
            }
			.img{
				position: absolute;
				right:10px;
				top:10px;
			}
            .item-btn{
                justify-content: flex-end;
                margin-top: 6px;
            }
        }
    }
</style>