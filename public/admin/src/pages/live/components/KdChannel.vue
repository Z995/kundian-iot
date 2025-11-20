<template>
    <div>
        <div class="fb" :style="{fontSize:`${rem(14)}px`,marginLeft:`${rem(20)}px`,marginTop:`${rem(20)}px`}">设备通道</div>
        <div class="flex-cb channel-box mt10">
            <div class="channel-item" :style="{
                height: `${rem(86)}px`,
                marginBottom: `${rem(12)}px`,
                padding: `${rem(10)}px`,
                borderRadius: `${rem(10)}px`
            }" :class="{active:gbControl.state.channel_id===item.channelId}" v-for="(item,index) in gbControl.state.channelList" :key="index" @click="selectPlay(item)">
                <div class="name" :style="{fontSize:`${rem(14)}px`}">{{item.name}}</div>
                <div class="desc mt5" :style="{fontSize:`${rem(12)}px`}">{{item.channelId}}</div>
                <div class="tag online" :style="{fontSize: `${rem(10)}px`,bottom: `${rem(10)}px`,left: `${rem(10)}px`}" v-if="item.state == 'online'">在线</div>
                <div class="tag" :style="{fontSize: `${rem(10)}px`,bottom: `${rem(10)}px`,left: `${rem(10)}px`}" v-else>离线</div>
            </div>
        </div>
    </div>
</template>

<script setup>
	import { defineEmits } from 'vue';
	import { useGbControlState } from '@/store/gbControl';
    import {rem} from '@/util/pxtovw'
    import { getLiveChanngeList } from '@/api/kdLive'
	const gbControl = useGbControlState()
	const emits = defineEmits(['change'])

	getDeviceChannels()

    function getDeviceChannels(){
        getLiveChanngeList({id:gbControl.state.device.id}).then(res =>{
            if(res.data?.items.length){
				gbControl.state.channelList = res.data.items
				gbControl.state.channel_id = gbControl.state.channelList[0].channelId
			}
        })
	}

	function selectPlay(data){
		gbControl.state.channel_id = data.channelId
		emits('change')
	}
	
</script>

<style lang="scss" scoped>
.channel-box{
	flex-wrap: wrap;
	.channel-item{
		width: 48%;
		background: #f5f5f5;
		position: relative;
	}
	.active{
		border: 1px solid #2979ff;
		color: #2979ff;
	}
	.desc{
		color: #999;
	}
	.tag{
		position: absolute;
		color: #999;
	}
	.online{
		color: #45df88;
	}
	.name{
		width: 100%;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	
}
</style>