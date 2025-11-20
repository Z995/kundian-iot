<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/components/grow/CqkdGrowAdd.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-10 15:20:43  -->
<template>
    <div v-if="state.show && state.form">
        <a-drawer :title="state.form.id?'编辑作物生长标准':'添加作物生长标准'" v-model:visible="state.show" width="60%">
            <template #footer>
                <a-button type="primary" @click="changeData(0)"><i class="ri-add-line"></i>添加阶段</a-button>
                <a-button type="primary" @click="saveDate">确认</a-button>
                <a-button @click="state.show = false">取消</a-button>
            </template>
            <a-spin :loading="state.loading" style="width: 100%;">
                <a-form ref="formRef" :model="state.form" :label-col-props="{flex:'170px'}" :wrapper-col-props="{flex:1}">
                    <a-form-item label="作物生长标准名称" field="growthName" :rules="[{required:true,message:'请输入作物生长标准名称'}]">
                        <a-input v-model="state.form.growthName" placeholder="请输入园区名称"></a-input>
                    </a-form-item>
                    <a-form-item>
                        <template #label>
                            <a-tooltip content="提示：设置阶段类型为“循环阶段”时，循环顺序按阶段顺序进行">
                                生长阶段<i class="ri-question-line ri-top2"></i>
                            </a-tooltip>
                        </template>
                        <div style="width: 100%;">
                            <div class="table" v-for="(item,index) in state.form.growthStageList" :key="index">
                                <div>
                                    <div class="table-nav flex">
                                        <div>序号</div>
                                        <div>阶段名称</div>
                                        <div>持续时间(天)</div>
                                        <div>此阶段作物图片</div>
                                        <div>备注</div>
                                        <div>阶段类型</div>
                                        <div>操作</div>
                                    </div>
                                    <div class="table-data flex">
                                        <div class="table-data-item">
                                            <div>{{ index+1 }}</div>
                                        </div>
                                        <div class="table-data-item">
                                            <a-input v-model="item.stageName" placeholder="阶段名称"></a-input>
                                        </div>
                                        <div class="table-data-item">
                                            <a-input v-model="item.durationTime" placeholder="持续时间"></a-input>
                                        </div>
                                        <div class="table-data-item">
                                            <a-upload
                                                list-type="picture-card"
                                                action="/"
                                                :default-file-list="state.fileList"
                                                image-preview
                                            />
                                        </div>
                                        <div class="table-data-item">
                                            <a-input v-model="item.remarks" placeholder="备注"></a-input>
                                        </div>
                                        <div class="table-data-item">
                                            <a-select v-model="item.stageType" placeholder="请选择">
                                                <a-option :value="0">一次性阶段</a-option>
                                                <a-option :value="1">循环阶段</a-option>
                                            </a-select>
                                        </div>
                                        <div class="table-data-item">
                                            <a-popconfirm content="确认删除该生长阶段吗？" @ok="changeData(item.id,index)">
                                                <a-button type="text" status="danger" size="mini">删除</a-button>
                                            </a-popconfirm>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-information">
                                    <div class="flex-c ">
                                        <a-button status="danger" size="mini" @click="seleceSensor">选择数据类型</a-button>
                                        <div class="tips ml5">最多选择8中数据类型！</div>
                                    </div>
                                    <div class="table-information-list flex">
                                        <template v-for="(val,ind) in item.growthStageDataTypeList" :key="ind">
                                            <div class="item">
                                                <div class="item-title">{{ val.dataType }}</div>
                                                <div class="item-input flex-c">
                                                    <a-input class="input" v-model="val.loweLimit"></a-input>
                                                    ~
                                                    <a-input class="input" v-model="val.upperLimit"></a-input>
                                                </div>
                                                <icon-close-circle class="icon-close" @click="delInformation(index,ind)" />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a-form-item>
                </a-form>
            </a-spin>
        </a-drawer>
        <cqkd-sensor-type ref="sensorRef"></cqkd-sensor-type>
    </div>
</template>
<script setup>
    import { reactive,ref } from 'vue';
    import CqkdSensorType from './CqkdSensorType.vue';
    const formRef = ref()
    const sensorRef = ref()
    const state = reactive({
        loading:false,
        show:false,
        form:null,
        fileList:[]
    })

    function show(data){
        console.log(data)
        setTimeout(() => {
            state.form = data?data:{
                standardId: "",
                groupId: "",
                growthName: "",
                growthCycle: 0,
                growthCycleTime: 0,
                growthStageList: [{
                    stageId: "",
                    standardId: "",
                    stageName: "",
                    durationTime: 0,
                    stageImg: "",
                    remarks: "",
                    stageType: 0,
                    createTime: null,
                    stageSort: 1,
                    growthStageDataTypeList: []
                }]
            }
        },1000)
        
        state.show = true
    }

    function changeData(id,index){
        if(id == 0){
            state.form.growthStageList.push({
                stageId: "",
                standardId: "",
                stageName: "",
                durationTime: 0,
                stageImg: "",
                remarks: "",
                stageType: 0,
                createTime: null,
                stageSort: 1,
                growthStageDataTypeList: []
            })
            return
        }
         state.form.growthStageList.splice(index,1)
    }

    function delInformation(index,ind){
        state.form.growthStageList[index].growthStageDataTypeList.splice(ind,1)
    }

    // 选择传感器
    function seleceSensor(){
        sensorRef.value.show()
    }

    async function saveDate(){

    }

    defineExpose({
        show
    })
</script>
<style lang="scss" scoped>
    .table{
        margin-bottom: 20px;
        .table-nav{
            width: 100%;
            justify-content: space-between;
            background: #F6F9FF;
            div{
                width: 16%;
                padding: 8px 20px;
                text-align: center;
                border: 1px solid #dcdee2;
                border-collapse: collapse;
            }
            div{
                border-right: none;
            }
            >div:last-child{
                width: 11%;
                border-right: 1px solid #dcdee2;
            }
            >div:first-child{
                width: 11%;
            }
        }
        .table-data{
            width: 100%;
            background: #fff;
            border: 1px solid #dcdee2;
            border-top: none;
            &-item{
                width: 16.3%;
                padding: 8px 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-right: 1px solid #dcdee2;
            }
            &-item:last-child{
                width: 11%;
                border-right: none;
            }
            &-item:first-child{
                width: 11.2%;
            }
        }
        .table-information{
            width: 100%;
            background: #fff;
            border: 1px solid #dcdee2;
            border-top: none;
            padding: 18px 10px;
            &-list{
                flex-wrap: wrap;
                .item{
                    padding: 10px;
                    background: #f7f6f6;
                    border: 1px solid #dcdee2;
                    border-radius: 6px;
                    margin: 10px 10px 0 0;
                    position: relative;
                    &-title{
                        margin-bottom: 10px;
                    }
                    .input{
                        width: 80px;
                        background: #fff;
                    }
                    .icon-close{
                        color: #ccc;
                        cursor: pointer;
                        position: absolute;
                        right: 5px;
                        top: 5px;
                        font-size: 20px;
                    }
                }
            }
        }
    }
</style>