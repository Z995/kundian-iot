<template>
<div class="play-control">
	<div :style="{
        fontWeight:'bold',
        fontSize:`${rem(14)}px`,
        marginTop:`${rem(38)}px`,
        marginLeft:`${rem(20)}px`
    }">云台控制</div>
	<div class="control-box flex-cc">
		<div class="cb-content" :style="{
            width: `${rem(200)}px`,
            height: `${rem(200)}px`,
        }">
			<div ref="cbItem" class="cb-item" @mousedown="controlPlay('up')" @mouseup="controlPlay('stop')">
				<div  class="cb-icon arrow-up" :style="{top: `${rem(-6)}px`,left: `${rem(30)}px`}">
					<div class="ri-arrow-up-s-line" style="color: #c1bebe;" :style="{fontSize:`${rem(28)}px`}"></div>
				</div>
			</div>
			<div ref="cbItem1" class="cb-item" @mousedown="controlPlay('right')" @mouseup="controlPlay('stop')">
				<div class="cb-icon" :style="{top: `${rem(-10)}px`,left: `${rem(38)}px`}">
                    <div class="ri-arrow-right-s-line" style="color: #c1bebe;" :style="{fontSize:`${rem(28)}px`}"></div>
				</div>
			</div>
			<div ref="cbItem2" class="cb-item" @mousedown="controlPlay('left')" @mouseup="controlPlay('stop')">
				<div class="cb-icon" :style="{top: `${rem(1)}px`,left: `${rem(29)}px`}">
                    <div class="ri-arrow-left-s-line" style="color: #c1bebe;" :style="{fontSize:`${rem(28)}px`}"></div>
				</div>
			</div>
			<div ref="cbItem3" class="cb-item" @mousedown="controlPlay('down')" @mouseup="controlPlay('stop')">
				<div class="cb-icon" :style="{top: `${rem(6)}px`,left: `${rem(41)}px`}">
                    <div class="ri-arrow-down-s-line" style="color: #c1bebe;" :style="{fontSize:`${rem(28)}px`}"></div>
				</div>
			</div>
			<div class="cb-center" :style="{
				width: `${rem(100)}px`,
				height: `${rem(100)}px`,
				left: `${rem(50)}px`,
				top: `${rem(50)}px`
			}">
				<div class="cbc-item" :style="{width: '100%',height: `${rem(50)}px`,lineHeight: `${rem(50)}px`}" @mousedown="controlPlay('zoomin')" @mouseup="controlPlay('stop')">
					<div class="cb-icon">
                        <div class="ri-add-line" style="color: #c1bebe;" :style="{fontSize:`${rem(18)}px`}"></div>
					</div>
				</div>
				<div class="cbc-item" :style="{width: '100%',height: `${rem(50)}px`,lineHeight: `${rem(50)}px`}" @mousedown="controlPlay('zoomout')" @mouseup="controlPlay('stop')">
					<div class="cb-icon">
                        <div class="ri-subtract-line" style="color: #c1bebe;" :style="{fontSize:`${rem(18)}px`}"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</template>

<script setup>
    import { defineEmits,ref,onMounted } from 'vue';
    const emits = defineEmits(['control'])
    import {rem} from '@/util/pxtovw'

    const cbItem = ref()
	const cbItem1 = ref()
	const cbItem2 = ref()
	const cbItem3 = ref()
    onMounted(() =>{
        cbItem.value.style.width = `${rem(100)}px`
        cbItem.value.style.height = `${rem(100)}px`
        cbItem.value.style.lineHeight = `${rem(100)}px`
        
		cbItem1.value.style.width = `${rem(100)}px`
        cbItem1.value.style.height = `${rem(100)}px`
        cbItem1.value.style.lineHeight = `${rem(100)}px`

		cbItem2.value.style.width = `${rem(100)}px`
        cbItem2.value.style.height = `${rem(100)}px`
        cbItem2.value.style.lineHeight = `${rem(100)}px`

		cbItem3.value.style.width = `${rem(100)}px`
        cbItem3.value.style.height = `${rem(100)}px`
        cbItem3.value.style.lineHeight = `${rem(100)}px`
    })

    function controlPlay(type){
        emits('control',type)
    }
    
</script>

<style lang="scss" scoped>
	.cb-content{
		border-radius: 50%;
		display: flex;
		flex-wrap: wrap;
		transform: rotate(45deg);
		position: relative;
		.cb-item{
			text-align: center;
			position: relative;
			border-right: 1px solid #f4f4f4;
			border-top: 1px solid #f4f4f4;
			background: #e4e4e4;
			&:first-child{
				border-radius: 100% 25% 25% 25%;
			}
			&:nth-child(2){
				border-radius: 25% 100% 25% 25%;
			}
			&:nth-child(3){
				border-radius: 25% 25% 25% 100%;
			}
			&:nth-child(4){
				border-radius: 25% 25% 100% 25%;
			}
			.cb-icon{
				display: inline-block;
				transform: rotate(-45deg);
				position: absolute;
			}
			
			&:active{
				background: rgba($color: #2979ff, $alpha: .5);
			}
		}
		.cb-center{
			border: 1px solid #f4f4f4;
			position: absolute;
			transform: rotate(-45deg);
			border-radius: 50%;
			background: #fff;
			overflow: hidden;
			.cb-icon{
				display: inline-block;
			}
			.cbc-item{
				border-bottom: 1px solid #f4f4f4;
				text-align: center;
				&:active{
					background: rgba($color: #2979ff, $alpha: .5);
				}
				&:last-child{
					border-bottom: none;
				}
			}
		}
	}
</style>