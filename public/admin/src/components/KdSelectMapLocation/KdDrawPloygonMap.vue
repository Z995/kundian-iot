<!-- 坤典智慧农场V6 -->
<!-- @link https://www.cqkundian.com -->
<!-- @description 软件开发团队为 重庆坤典科技有限公司 -->
<!-- @description The software development team is Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549 -->
<!-- @description 软件版权归 重庆坤典科技有限公司 所有 -->
<!-- @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd. -->
<!-- @description 文件路径与名称:  widsomFarmV6_admin/src/components/map/DrawPlotLeafletMap.vue -->
<!-- @description File path and name: widsomFarmV6_admin/src/components/map/DrawPlotLeafletMap.vue-->
<!-- @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用 -->
<!-- @description This file is licensed to 重庆坤典科技-www.cqkundian.com -->
<!-- @warning 这不是一个免费的软件，使用前请先获取正式商业授权 -->
<!-- @warning This is not a free software, please get the license before use. -->
<!-- @warning 未经授权许可禁止转载分发，违者将追究其法律责任 -->
<!-- @warning It is prohibited to reprint and distribute without authorization, and violators will be investigated for legal responsibility -->
<!-- @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任 -->
<!-- @warning It is prohibited to delete this comment without license, and violators will be held legally responsible -->
<template>
    <div v-if="state.show">
        <a-modal title="绘制区域" v-model:visible="state.show" width="1100px" :footer="!props.showColor ?true : null" @ok="saveData">
            <div class="plot-map-box">
                <div class="map-box" id="landV2Main">
                    <div class="change-icon">
                        <a-tooltip content="切换底图">
                            <!-- <img :src="$util.getStaticSrc('admin/map/change.png')"  @click="changeLayer" alt="" class="icon-img"> -->
                        </a-tooltip>
                    </div>
                    <div class="search-box">
                        <div class="input-box flex">
                            <a-input v-model="state.keyword" placeholder="请输入位置进行搜索"
                                        @press-enter="doSearch(1)"
                                        @focus="getFocus"
                            ></a-input>
                            <a-button type="primary" class="ml10" @click="doSearch(1)">搜索</a-button>
                        </div>
                        <div class="draw-box">
                            <div class="draw-box-li" @click="beginDraw">
                                <a-tooltip content="绘制多边形">
                                    <img src="/static/img/duobianxing.png" alt="">
                                </a-tooltip>
                            </div>
                            <div class="draw-box-li" @click="launchFullscreen">
                                <i class="ri-fullscreen-line" v-if="!state.is_fullscreen" style="color:#5c5c5c;position: relative;top: -5px;"></i>
                                <i class="ri-fullscreen-exit-line" v-else style="color:#5c5c5c;position: relative;top: -5px;"></i>
                            </div>
                        </div>
                        <div style="width: 400px;height: 100%">
                            <a-spin :loading="state.loading" tip="加载中..." >
                                <div class="searchResult" ref="resultRef" v-if="state.loading || state.showResult">
                                    <div class="address-item" v-for="(item,index) in state.addressList" :key="index"
                                         @click="selectSearchAddress(item)"
                                    >
                                        <i class="ri-search-2-line"></i>
                                        <span class="address-name">{{ item.name}}</span>
                                        <span class="address-area">{{ item.address}}</span>
                                    </div>
                                    <a-empty v-if="!state.addressList.length"></a-empty>
                                </div>
                            </a-spin>
                        </div>
                    </div>
                    <div class="map-box-container" id="leafletSelectMapBox"></div>
                    <div class="select-color" v-if="state.colorInfo.show && props.showColor">
                        <div class="sc-title">选择填充颜色</div>
                        <div class="sc-color flex mt10 mb24">
                            <div class="color-item" v-for="(item,index) in state.colorInfo.list"
                                 @click="changeColor(item)"
                            >
                                <div class="color-dot" :style="{background:item}"></div>
                            </div>
                        </div>
						<div class="mt20 w-260">
							<a-button class="w200 mb16" @click="clearCurrentEdit">重新绘制</a-button>
						</div>
                        <a-button type="primary" class="w200 mt20" @click="saveData">保存并退出</a-button>
                    </div>
                </div>
            </div>
        </a-modal>
    </div>
</template>
<script setup>
import { onClickOutside } from '@vueuse/core'
import {nextTick, reactive, ref} from "vue";
import axios from "axios";
import {Message} from "@arco-design/web-vue";
import {useKdLeafletMap} from "@/util/common/kdLeafletMap";
import { useConfigStore } from '@/store/config'
const { leafletState,initMap,setMapCenter,drawPolygon,polygonEditOrView,clearCurrentEdit,addSatelliteLayer } = useKdLeafletMap()
const props = defineProps({
    showColor:{
        type:Boolean,
        default:true
    }
})
const configStore = useConfigStore()
const resultRef = ref()
const emits= defineEmits(['change'])
const state = reactive({
    show:false,
    loading:false,
    showResult:false,
    keyword:"",
    location:[],
    addressList:[],     //搜索记录
    search_url:  process.env.NODE_ENV === 'development' ?"http://api.tianditu.gov.cn/v2/search?postStr=":"https://api.tianditu.gov.cn/v2/search?postStr=",
    is_fullscreen:false,        //是否全屏展示
    colorInfo:{         //颜色信息选择
        list:[
            '#00CC00','#00CC33','#00CC66','#00CC99','#00CCCC','#00CCFF',
            '#3366FF','#6633FF','#9933FF','#CC33FF','#FF33FF','#FF6600',
            '#FF0033','#FF0099','#FF6699','#FF9933','#FF3366','#CC0066',
            '#3399FF','#3366FF','#0033CC','#3399CC','#9900CC','#CC33CC',
            '#FFFF33','#CCFF33','#99FF33','#FFCC66','#66CC66','#333333'
        ],
        show:false,
    },
    color:"#3366FF",
    areaPointList:[],   //json格式数据
    area_index:null,
    isEdit:true,        //是否允许编辑 json数据解析时不允许编辑
    satellite:true,     //是否显示卫星影像
})

function showMap(point,color="#3366FF"){
    state.show = true
    state.color = color || '#3366FF'
    nextTick(()=>{
        initMap({el:"leafletSelectMapBox",satellite:true },()=>{
            if( point && point.length ){
                state.colorInfo.show = true
                polygonEditOrView({
                    point,color,edit:point.length <=200
                },()=>{
                    state.colorInfo.show = true
                })
            }else{
                //定位到当前城市
            }
        })
    })
}
//关闭搜索结果框
onClickOutside(resultRef, event => {
    state.showResult = false
})

//普通搜索
function doSearch(searchType){
    state.loading = true
    state.showResult = true
    let url = `${state.search_url}{"keyWord":"${state.keyword}","level":15,"mapBound":"-180,-90,180,90","queryType":${searchType},"start":0,"count":10}&type=query&tk=${configStore.state.tkToken}`
    axios.get(url).then(res=>{
        let result = res.data
        if( result.resultType.toString() === searchType.toString() ){
            state.loading = false
            state.addressList = res.data?.pois || []
        }else{
            searchTypeByThree()
        }
    }).catch(()=>{
        state.loading = false
    })
}

function searchTypeByThree(){
    let url = `${state.search_url}{"keyWord":"${state.keyword}","level":15,"mapBound":"-180,-90,180,90","queryType":4,"start":0,"count":10}&type=query&tk=${configStore.state.tkToken}`
    axios.get(url).then(res=>{
        state.loading = false
        state.addressList = res.data?.suggests || []
    }).catch(()=>{
        state.loading = false
    })
}

//输入框获取焦点 ,如果有搜索结果，展示搜索结果
function getFocus(){
    if( state.addressList.length ){
        state.showResult = true
    }
}
//设置地图中心点位置
function selectSearchAddress(data){
    if( data.lonlat ){
        let center = data.lonlat.split(',')
        setMapCenter([center[1],center[0]])
        state.showResult = false
    }
}

//开始绘制
function beginDraw(){
    state.isEdit = true
    drawPolygon(()=>{
        state.colorInfo.show = true
    })
}
//设置绘制区域颜色
function changeColor(color){
    state.color = color
    let point = JSON.parse(JSON.stringify(leafletState.polygonForm.point))
    if( point.length ){
        polygonEditOrView({point,color,edit:state.isEdit,isCenter:false},()=>{
            state.colorInfo.show = true
        })
    }
}

//保存绘制数据
function saveData(){
    let { point,area } = leafletState.polygonForm
    if( !point.length){
        Message.warning("请绘制区域")
        return false
    }
    let data = {
        point:JSON.parse(JSON.stringify(point)),
        acreage: parseFloat((area/666).toFixed(2)), //单位亩
        color:state.color
    }
    state.show = false
	console.log('111',data);
    emits("change",data)
}

function beforeUpload(evt){
    let file = evt.fileItem.file
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const jsonData = JSON.parse(e.target.result); // 解析JSON数据
            let list = []
            if( jsonData.features && jsonData.features.length > 0 ){
                jsonData.features.forEach(item=>{
                    if( item.properties && item.geometry && item.geometry.coordinates?.length){
                        let temp = {
                            name:item.properties.name,
                            point:item.geometry.coordinates?.[0]
                        }
                        list.push(temp)
                    }
                })
            }

            state.areaPointList = list
            console.log('list',list)
            // 处理jsonData
        } catch (error) {
            console.error("Error parsing JSON:", error);
        }
    };
    reader.readAsText(file); // 以文本格式读取文件
}

//绘制json数据
function drawSelectArea(){
    let data = state.areaPointList[state.area_index]
    if( data.point && data.point.length ){
        state.isEdit = false
        polygonEditOrView({
            point:data.point,color:state.color,edit:false
        },()=>{
            state.colorInfo.show = true
        })
    }
}
function changeLayer(){
    state.satellite = !state.satellite
    addSatelliteLayer(state.satellite)
}

//地块标记全屏/退出全屏操作
function launchFullscreen() {
    let el = document.getElementById('landV2Main')
    if( !state.is_fullscreen ){
        state.is_fullscreen = true
        if(el.requestFullscreen) {
            el.requestFullscreen();
        } else if(el.mozRequestFullScreen) {
            el.mozRequestFullScreen();
        } else if(el.msRequestFullscreen){
            el.msRequestFullscreen();
        } else if(el.webkitRequestFullscreen) {
            el.webkitRequestFullScreen();
        }

    }else{
        state.is_fullscreen = false
        if (document.exitFullscreen) {
            document.exitFullscreen()
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen()
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen()
        } else if (document.oRequestFullscreen) {
            document.oCancelFullScreen()
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen()
        }
    }
}
defineExpose({
    showMap
})
</script>
<style scoped lang="scss">
.plot-map-box{
    width: 100%;
    height: 590px;
    .map-box{
        width: 100%;
        height: 100%;
        position: relative;
        .map-box-container{
            width: 100%;
            height: 100%;
        }
        .search-box{
            position: absolute;
            z-index: 999999;
            padding: 10px;
            width: 100%;
            .input-box{
                width: 400px;
                box-shadow: 1px 1px 10px #acacac;
                overflow: hidden;
            }
        }

        .searchResult{
            width: 400px;
            background: #fff;
            margin-top: 5px;
            box-shadow: 1px 1px 10px #acacac;
            border-radius: 4px;
            min-height: 300px;

            .address-item{
                width: 100%;
                padding: 0 10px;
                font-size: 12px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                height: 32px;
                line-height: 32px;
                cursor: pointer;
                font-family: microsoft yahei,sans-serif;
                .address-name{
                    color: #333;
                    margin-right: 10px;
                }
                &:hover{
                    background: rgba(#000, .05);
                    .address-name{
                        color: #0066FF;
                    }
                }
                .ri-search-2-line{
                    color: #999;
                    margin-right: 8px;
                }
                .address-area{
                    font-weight: 300;
                    margin-right: 10px;
                    color: #b8b8b8;
                }
            }
        }
    }

    .draw-box{
        position: absolute;
        left: 440px;
        top: -6px;
        height:36px;
        background: #fff;
        padding:6px;
        box-sizing: border-box;
        margin-top: 12px;
        border-radius: 6px;
        display: flex;
        z-index: 999;
        &-li{
            width: 36px;
            height: 100%;
            border-right: 1px solid #f4f4f4;
            cursor: pointer;
            text-align: center;

            i{
                font-size: 22px;
                position: relative;
                top: -3px;
                color: #7d7d7d;
            }
        }

        &-li:last-child{
            border-right: none;
        }
        img{
            width: 24px;
            height: 24px;
        }
    }

    .select-color{
        position: absolute;
        width: 300px;
        background: rgba(#000,.5);
        height: 100%;
        right: 0;
        top: 0;
        z-index: 9999;
        padding: 20px;
        text-align: center;
        .sc-title{
            width: 100%;
            color: #fff;
            font-size: 20px;
            text-align: center;
        }
        .sc-color{
            width: 100%;
            padding: 10px;
            flex-wrap: wrap;
            gap: 8px;
            background: rgba(#fff,.4);
            border-radius: 6px;
            .color-item{
                width: 40px;
                height: 40px;
                padding: 5px;
                cursor: pointer;
                border-radius: 4px;
                &:hover{
                    background: rgba(#fff,.4);
                }
                .color-dot{
                    width: 100%;
                    height: 100%;
                    border-radius: 4px;
                }
            }
        }
    }
    .change-icon{
        position: absolute;
        z-index: 999;
        left: 10px;
        bottom: 10px;
        width: 40px;
        height: 40px;
        background: #fff;
        border-radius: 4px;
        padding: 5px;
        box-shadow: 0px 0px 16px #9a9a9a;
        .icon-img{
            width: 30px;
            height: 30px;
            cursor: pointer;
        }
    }
}
</style>

