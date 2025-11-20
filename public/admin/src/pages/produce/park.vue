<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/produce/park.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-07 10:31:43  -->
<template>
    <kd-page-box>
        <div class="kd-content">
            <a-card :bordered="false">
                <template #title>
                    <div class="flex-c">
                        <span>{{ state.name }}管理</span>
                        <icon-edit class="park-title" @click="changeEditPark"></icon-edit>
                    </div>
                </template>
                <div class="park-data">
                    <img class="park-data-img" :src="state.form.img" alt="">
                    <div class="park-data-item">园区名称：{{ state.form.name }}</div>
                    <div class="park-data-item">园区面积：{{ state.form.area }}</div>
                    <div class="park-data-item">责任人：{{ state.form.responsiblePerson }}</div>
                    <div class="park-data-item">联系方式：{{ state.form.phone }}</div>
                    <div class="park-data-item">地址：{{ state.form.address }}</div>
                    <div class="park-data-item">园区大屏可视化标题名称：{{ state.form.title }}</div>
                    <div class="park-data-item">备注：{{ state.form.remarks }}</div>
                </div>
            </a-card>
        </div>
        <cqkd-park-manage ref="parkmanageRef"></cqkd-park-manage>
    </kd-page-box>
</template>
<script setup>
    import { reactive,ref } from 'vue';
    import CqkdParkManage from './components/CqkdParkManage.vue';
    const parkmanageRef = ref()
    const state = reactive({
        name:'大棚',
        form:{
            img:'https://kundian.cqkundian.com/uploads/farm/2024/10/30/5f16e06c0b94e1e440c6cf050050217cb906516855abf02cefbfe8b829fd2a0558a25d68.jpeg',
            name:'坤典园区',
            area:'47108.7',
            responsiblePerson:'',
            phone:'',
            address:'',
            title:'',
            remarks:'',
        }
    })

    function changeEditPark(){
        parkmanageRef.value.show(state.form)
    }

</script>
<style lang="scss" scoped>
    .park-title{
        margin-left: 8px;
        font-size: 18px;
        color: #165DFF;
        cursor: pointer;
    }
    .park-data{
        padding: 10px 30px;
        &-img{
            width: 300px;
            height: 300px;
        }
        &-item{
            padding: 10px 0;
            font-weight: bold;
        }
    }
</style>