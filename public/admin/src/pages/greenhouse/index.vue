<!-- 坤典物联 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description File path and name:   kundian_iot_admin/pages/greenhouse/index.vue -->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<!-- @time:2025-11-06 16:13:43  -->
<template>
<kd-page-box>
	<div class="kd-content">
		<a-card :bordered="false">
			<template #title>
				<div class="flex-c">
					<span>{{ state.name }}管理</span>
					<icon-edit class="greenhouse-title" @click="changeEditTitle"></icon-edit>
				</div>
			</template>
			<template #extra>
				<a-space>
					<a-button :type="state.show?'primary':'dashed'" @click="showEdit">
						<template v-if="state.show">编辑{{ state.name }}</template>
						<template v-else>取消编辑</template>
					</a-button>
					<router-link to="/greenhouse/houseAdd">
						<a-button type="primary">
							<i class="ri-add-line"></i>新建{{ state.name }}
						</a-button>
					</router-link>
				</a-space>
			</template>
			<div class="house-list">
				<div class="house-item" v-for="(item,index) in 12" :key="index">
					<div class="hi-name f16 fb">一号大棚<span class="ml5 f14">(100平方米)</span></div>
					<div class="mt10 fw">种植面积：10平方米</div>
					<div class="mt10 fw flex-c">种植品种：<span class="link flex-c fb">5种</span></div>
					<div class="mt10 fw">传感器：5</div>
					<a-image class="cover" width="70px" src="https://kundian.cqkundian.com/uploads/farm/2024/10/30/5f16e06c0b94e1e440c6cf050050217cb906516855abf02cefbfe8b829fd2a0558a25d68.jpeg"></a-image>
					<a-space class="mt10">
						<a-button size="mini">删除</a-button>
						<router-link to="/greenhouse/houseAdd">
							<a-button size="mini">编辑</a-button>
						</router-link>
						<a-button size="mini">新增种植</a-button>
						<router-link to="/greenhouse/houseDetail">
							<a-button size="mini">详情</a-button>
						</router-link>
					</a-space>
				</div>
			</div>
		</a-card>
	</div>
	<cqkd-edit-title ref="titleRef" @success="data=>state.name = data"></cqkd-edit-title>
	<cqkd-edit ref="editRef"></cqkd-edit>
	<cqkd-batch-migration ref="migrationRef"></cqkd-batch-migration>
</kd-page-box>
</template>
<script setup>
    import { onMounted,reactive,ref } from 'vue';
    import CqkdEditTitle from './components/CqkdEditTitle.vue';
    import CqkdEdit from './components/CqkdEdit.vue';
    import CqkdBatchMigration from './components/CqkdBatchMigration.vue';
    const titleRef = ref()
    const editRef = ref()
    const migrationRef = ref()
    const state = reactive({
        list:[],
        page:1,
        limit:20,
        loading:false,
        name:'大棚',
        show:false
    })

    onMounted(() =>{
        getList(state.page,state.limit)
    })

    function getList(page,limit){
        state.page = page || state.info.page
        state.limit = limit || state.info.limit
        let param = {
            page:state.page,
            limit:state.limit,
        }
        state.loading = true
        // 模拟数据
        state.list = [
            {id:1,image:'https://kundian.cqkundian.com/uploads/farm/2024/10/30/5f16e06c0b94e1e440c6cf050050217cb906516855abf02cefbfe8b829fd2a0558a25d68.jpeg',name:'1号大棚',
             controller:[{name:'卷帘开',id:'40377282_15000',type:1},{name:'继电器1',id:'40377282_15000',type:0},{name:'继电器2',id:'40377282_15000',type:2}]},
             {id:1,image:'https://kundian.cqkundian.com/uploads/farm/2024/10/30/5f16e06c0b94e1e440c6cf050050217cb906516855abf02cefbfe8b829fd2a0558a25d68.jpeg',name:'2号大棚',
             controller:[{name:'卷帘开',id:'40377282_15000',type:1},{name:'卷帘开',id:'40377282_15000',type:1}]},
             {id:1,image:'https://kundian.cqkundian.com/uploads/farm/2024/10/30/5f16e06c0b94e1e440c6cf050050217cb906516855abf02cefbfe8b829fd2a0558a25d68.jpeg',name:'3号大棚',
             controller:[]},
             {id:1,image:'https://kundian.cqkundian.com/uploads/farm/2024/10/30/5f16e06c0b94e1e440c6cf050050217cb906516855abf02cefbfe8b829fd2a0558a25d68.jpeg',name:'4号大棚',
             controller:[{name:'卷帘开2',id:'40377282_15000',type:1}]}
        ]
    }

    function changeEditTitle(){
        titleRef.value.show(state.name)
    }

    function showAdd(data){
        editRef.value.show(data)
    }

    function showEdit(){
        state.show = !state.show
    }

    // 删除
    function delGreenhouse(id){
        console.log('删除')
    }

    // 批量移入
    function batchMigration(data){
        migrationRef.value.show(data)
    }


</script>
<style lang="scss" scoped>
.greenhouse-title{
	margin-left: 8px;
	font-size: 18px;
	color: #165DFF;
	cursor: pointer;
}
.house-list{
	width: 100%;
	display: flex;
	flex-wrap: wrap;
	gap:20px;
	.house-item{
		width: 350px;
		height: 155px;
		border:2px dashed #ececec;
		padding: 10px;
		position: relative;
		cursor: pointer;
		border-radius: 10px;
		&:hover{
			border:2px dashed var(--primary-color);
		}
		.cover{
			position: absolute;
			right: 10px;
			top: 10px;
			border-radius: 10px;
		}
		.hi-name{
			color: #000;
		}
		.link{
			color: var(--primary-color);
		}
	}
}
.greenhouse-list{
	flex-flow: wrap;
	justify-content: space-between;
	&-item{
		position: relative;
		width: calc(32% - 10px);
		margin-right: 30px;
		margin-bottom: 35px;
		box-shadow: 1px 1px 10px #acacac;
		.item-btn{
			position: absolute;
			top: 0;
			right: 0;
			display: flex;
			align-items: center;
			z-index: 1;
			.ml1{
				margin-left: 1px;
			}
		}
		.item-img{
			position: relative;
			width: 100%;
			height: 210px;
			img{
				width: 100%;
				height: 100%;
			}
			.item-name{
				position: absolute;
				bottom: 0px;
				left: 0px;
				width: 100%;
				height: 40px;
				line-height: 40px;
				text-align: center;
				font-size: 18px;
				background: rgba($color: #ffffff, $alpha: .5);
				font-weight: bold;
			}
		}
		.item-number{
			height: 40px;
			line-height: 40px;
			background: #f2f2f2;
			text-align: center;
			font-size: 16px;
			font-weight: 400;
		}
		.item-controller{
			overflow: auto;
			height: 100px;
			&-item{
				position: relative;
				width: 100%;
				justify-content: space-between;
				padding: 5px 10px;
				white-space: nowrap;
				text-overflow: ellipsis;
				cursor: pointer;
				font-size: 14px;
				line-height: 30px;
				.name{
					margin-left: 24px;
				}
				.name::after{
					content: '';
					position: absolute;
					left: 10px;
					top: 11px;
					width: 17px;
					height: 17px;
					border-radius: 50%;
					background: #00CC66;
				}
				.actve::after{
					background: #999B99 !important;
				}
			}
		}
	}
	&-item:nth-child(3n){
		margin-right: 0;
	}
}
</style>