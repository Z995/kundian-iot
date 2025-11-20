<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/greenhouse/components/CqkdEdit.vue-->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-06 17:34:36  -->
<template>
<div v-if="state.show">
	<a-modal :title="`${state.form.name}名称修改`" v-model:visible="state.show" width="550px" :on-before-ok="saveDate">
		<a-form ref="formRef" :model="state.form" :label-col-props="{flex:'110px'}" :wrapper-col-props="{flex:1}">
            <a-form-item label="设备名称" field="name" :rules="[{required:true,message:'设备名称必填'}]">
                <a-input v-model="state.form.name" placeholder="请输入设备名称" class="w600"></a-input>
            </a-form-item>
            <a-form-item label="封面图">
                <a-upload
                    list-type="picture-card"
                    action="/"
                    :default-file-list="state.fileList"
                    image-preview
                />
            </a-form-item>
        </a-form>
	</a-modal>
</div>
</template>
<script setup>
    import { reactive,ref } from 'vue';
    import { Message } from '@arco-design/web-vue';
    const emits = defineEmits(['success'])
    const formRef = ref()
    const state = reactive({
        show:false,
        form:{
            name:'',
            img:''
        },
        fileList:[]
    })

    function show(data){
        state.form = data?data:{
            name:'',
            img:''
        }
        state.show = true
    }

    async function saveDate(){
        let valid = await formRef.value.validate()
	    if( valid ) return false
        console.log('保存数据')
        state.show = false
        return false
    }

    defineExpose({
        show
    })
</script>
<style>
</style>