<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/farming/farmingList.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-10 10:34:23  -->
<template>
    <kd-page-box>
		<div class="kd-content">
			<a-form :model="state.search" layout="inline">
				<a-form-item label="农事类型">
					<a-select v-model="state.search.type" placeholder="全部" allow-clear>
						<a-option value="1">施肥</a-option>
						<a-option value="2">除草</a-option>
						<a-option value="3">巡检</a-option>
					</a-select>
				</a-form-item>
				<a-form-item label="作物名称">
					<a-input v-model="state.search.name" placeholder="操作作物名称" class="w300"></a-input>
					<a-button type="primary" class="ml10">搜索</a-button>
					<a-button class="ml10">重置</a-button>
				</a-form-item>
			</a-form>
		</div>
        <div class="kd-content mt10">
            <a-card :bordered="false">
                <a-button type="primary" @click="showEdit(null)" class="mb10">
                    <i class="ri-add-line"></i>添加农事
                </a-button>
                <a-table class="kd-small-table" :pagination="false" row-key="id" :bordered="false" :data="state.info.list" :columns="[
                    {title:'ID',dataIndex:'id',width:80},
                    {title:'农事类型',dataIndex:'name',width:120},
                    {title:'目标作物',dataIndex:'plant'},
                    {title:'农机',dataIndex:'machinery'},
                    {title:'使用物料',dataIndex:'material'},
                    {title:'作业内容',dataIndex:'conent'},
                    {title:'农事拍照',slotName:'img'},
                    {title:'作业人员',dataIndex:'user'},
                    {title:'创建时间',dataIndex:'create_time'},
                    {title:'操作',slotName:'action',width:180},
                ]" :loading="state.loading">
                    <template #img="{record}">
                        <a-image width="40" :src="record.img"></a-image>
                    </template>
                    <template #action="{record}">
                        <a-button type="text" size="mini" class="ml-10">设为私密</a-button>
                        <a-popconfirm content="确认删除农事类型吗？" @ok="delData(record.id)">
                            <a-button type="text" size="mini">删除</a-button>
                        </a-popconfirm>
                    </template>
                </a-table>
                <kd-pager :pageData="state.info" :event="getList"></kd-pager>
            </a-card>
        </div>
        <cqkd-add-farming-list ref="addRef"></cqkd-add-farming-list>
    </kd-page-box>
</template>

<script setup>
    import { onMounted, reactive, ref } from 'vue';
    import CqkdAddFarmingList from './components/CqkdAddFarmingList.vue';
    const addRef = ref()
    const state = reactive({
        loading:false,
        info:{
            page:1,
            limit:10,
            count:0,
            list:[],
        },
		search:{
			type:'',
			name:''
		}
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
        state.info.list = [
            {id:1,name:'除草',material:'有机肥(2kg)',conent:'施肥操作施肥操作',user:'李四',machinery:'施肥机',plant:'番茄,土豆',status:1,create_time:'2025/10/10 12:00',img:'https://kundian.cqkundian.com/uploads/farm/2024/9/27/24a1407a77e443d91f2edd15ecbf7068278bfa154e15cce38e786b5824aa7985e23ced191600240383.jpg',expand:''},
            {id:2,name:'施肥',material:'有机肥(2kg)',conent:'施肥操作施肥操作',user:'李四',machinery:'施肥机',plant:'番茄,土豆',status:0,create_time:'2025/10/10 12:00',img:'https://kundian.cqkundian.com/uploads/farm/2024/9/27/24a1407a77e443d91f2edd15ecbf7068278bfa154e15cce38e786b5824aa7985e23ced191600240383.jpg',expand:''},
            {id:3,name:'浇水',material:'有机肥(2kg)',conent:'施肥操作施肥操作',user:'李四',machinery:'施肥机',plant:'番茄,土豆',status:1,create_time:'2025/10/10 12:00',img:'https://kundian.cqkundian.com/uploads/farm/2024/9/27/24a1407a77e443d91f2edd15ecbf7068278bfa154e15cce38e786b5824aa7985e23ced191600240383.jpg',expand:''}
        ]
    }
    //新增编辑
    function showEdit(){
        addRef.value.show()
    }

    //删除数据
    function delData(id){
        
    }
</script>

<style lang="scss" scoped>
</style>