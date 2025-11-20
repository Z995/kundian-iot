<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/greenhouse/components/CqkdEditTitle.vue-->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-06 17:14:36  -->
<template>
<div v-if="state.show">
	<a-modal :title="`${state.form.name}名称修改`" v-model:visible="state.show" width="400px" :on-before-ok="saveTitle">
		<a-input placeholder="请输入名称" v-model="state.form.name"></a-input>
        <div class="tips">修改名称后该园区下所有涉及“{{ state.form.name }}”的位置将同步做出修改</div>
	</a-modal>
</div>
</template>

<script setup>
    import { reactive } from 'vue';
    import { Message } from '@arco-design/web-vue';
    const emits = defineEmits(['success'])
    const state = reactive({
        show:false,
        form:{
            name:''
        }
    })

    function show(data){
        state.form.name = data
        state.show = true
    }

    async function saveTitle(){
        emits('success',state.form.name)
        state.show = false
        return false
    }

    defineExpose({
        show
    })
</script>