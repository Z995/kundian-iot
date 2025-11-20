<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/growthStandards.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-07 16:23:53  -->
<template>
    <kd-page-box>
        <div class="kd-content">
            <a-card title="作物生长标准" :bordered="false">
                <template #extra>
                    <router-link to="/produce/growthAdd">
						<a-button type="primary" >
						    <i class="ri-add-line"></i>添加
						</a-button>
					</router-link>
                </template>
                <div class="growthStandards-list">
                    <div class="growthStandards-list-item" v-for="(item,index) in state.info.list" :key="index">
                        <div class="flex item-nav">
                            <div>{{ item.growthName }}</div>
                            <div class="item-nav-btn flex-c">
								<router-link to="/produce/growthAdd">
									<p style="color:#fff;">修改</p>
								</router-link>
                                <p @click="delGrowthStandards">删除</p>
                                <p class="" v-if="state.expand" @click="state.expand = false"><icon-down /></p>
                                <p v-else @click="state.expand = true"><icon-up /></p>
                            </div>
                        </div>
                        <div class="item-box" v-if="state.expand">
                            <div class="growthStage-list" v-for="(val,ind) in item.growthStageList" :key="ind">
                                <div class="flex growthStage-list-nav">
                                    <img class="img" :src="val.stageImg" alt="">
                                    <div class="">
                                        <div class="f18">{{ val.stageName }}</div>
                                        <div class="flex-c">持续时间：<p class="day">{{ val.durationTime }}</p>天 <p class="type">{{ val.stageType == 0?'一次性阶段':'循环阶段' }}</p></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a-card>
        </div>
        <cqkd-grow-add ref="growRef"></cqkd-grow-add>
    </kd-page-box>
</template>
<script setup>
    import { reactive,onMounted,ref } from 'vue';
    import CqkdGrowAdd from './components/grow/CqkdGrowAdd.vue';
    const growRef = ref()
    const state = reactive({
        expand:true,
        loading:false,
        info:{
            list:[],
            page:1,
            limit:10,
            count:0
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
            {
                "standardId": "xzf2n1745346607047UG64g",
                "groupId": "EBSL71741312480839VPMW6",
                "growthName": "苹果",
                "growthCycle": 120,
                "growthCycleTime": 90,
                "createTime": "2025-04-23 02:30:07",
                "growthStageList": [
                    {
                        "stageId": "Aq68Q1762503015896BfXH0",
                        "standardId": "xzf2n1745346607047UG64g",
                        "stageName": "育苗",
                        "durationTime": 30,
                        "stageImg": "",
                        "remarks": "1",
                        "stageType": 0,
                        "createTime": null,
                        "stageSort": 1,
                        "growthStageDataTypeList": []
                    },
                    {
                        "stageId": "TAgjr1762503015896Q1eqG",
                        "standardId": "xzf2n1745346607047UG64g",
                        "stageName": "开花",
                        "durationTime": 30,
                        "stageImg": "https://kundian.cqkundian.com/uploads/farm/2024/9/27/a1ed9ff67e95eab77617cf0ca333307f46721df32200bd26a3e94ec7cb1670e1f083aa4e1600410311.png",
                        "remarks": "22222222222",
                        "stageType": 1,
                        "createTime": null,
                        "stageSort": 2,
                        "growthStageDataTypeList": [
                            {
                                "id": 1,
                                "stageId": "TAgjr1762503015896Q1eqG",
                                "standardId": null,
                                "dataType": "温度传感器",
                                "factorId": null,
                                "loweLimit": 12,
                                "upperLimit": 45,
                                "unit": null,
                                "factorName": null
                            },
                            {
                                "id": 2,
                                "stageId": "TAgjr1762503015896Q1eqG",
                                "standardId": null,
                                "dataType": "风向传感器",
                                "factorId": null,
                                "loweLimit": 22,
                                "upperLimit": 223,
                                "unit": null,
                                "factorName": null
                            }
                        ]
                    },
                    {
                        "stageId": "oyHZs1762503015897yOT25",
                        "standardId": "xzf2n1745346607047UG64g",
                        "stageName": "挂果",
                        "durationTime": 60,
                        "stageImg": "https://kundian.cqkundian.com/uploads/farm/2024/9/27/a1ed9ff67e95eab77617cf0ca333307f46721df32200bd26a3e94ec7cb1670e1f083aa4e1600410311.png",
                        "remarks": "",
                        "stageType": 1,
                        "createTime": null,
                        "stageSort": 3,
                        "growthStageDataTypeList": []
                    }
                ]
            }
        ]
    }
    
    function showAdd(data){
        growRef.value.show(data)
    }

</script>
<style lang="scss" scoped>
    .growthStandards-list{
        &-item{
            width: 100%;
            .item-nav{
                height: 40px;
                line-height: 40px;
                padding: 0 0px 0 20px;
                justify-content: space-between;
                background: var(--primary-color);
                color: #fff;
                &-btn{
                    p{
                        font-size: 15px;
                        cursor: pointer;
                        padding: 0 20px;
                        border-right: 1px solid #fff;
                    }
                    
                }
            }
            .item-box{
                width: 100%;
                height: auto;
                padding: 20px;
                background: rgba($color: #2979ff, $alpha: .2);
                overflow: auto;
                display: flex;
                flex-wrap: wrap;
                .growthStage-list{
                    width: calc(24% - 5px);
                    margin: 5px;
                    background-color: #fff;
                    border-radius: 6px;
                    overflow: hidden;
                    &-nav{
                        padding: 15px;
                        border-bottom: 1px solid #eee;
                        .img{
                            border-radius: 6px;
                            width: 120px;
                            height: 70px;
                            margin-right: 10px;
                        }
                        .f18{
                            font-weight: bold;
                            margin-bottom: 15px;
                        }
                        .day{
                            font-size: 18px;
                            color: var(--primary-color);
                            font-weight: bold;
                        }
                        .type{
                            background: rgba($color: #2979ff, $alpha: .2);
                            border: 1px solid var(--primary-color);
                            color: var(--primary-color);
                            border-radius: 4px;
                            height: 26px;
                            line-height: 24px;
                            margin-left: 5px;
                            padding: 0 8px;
                        }
                    }
                    &-data{
                        height: auto;
                        padding: 10px 15px;
                        display: flex;
                        flex-wrap: wrap;
                        .item{
                            margin-right: 10px;
                            width: calc(50% - 10px);
                            overflow: hidden;
                            white-space: nowrap;
                            text-overflow: ellipsis;
                            color: #333;
                        }
                    }
                }
            }
        }
    }
</style>