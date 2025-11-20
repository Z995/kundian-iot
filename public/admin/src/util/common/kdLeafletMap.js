/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称: widsomFarmV6_admin/src/kdLeafletMap.js
 * @description File path and name: widsomFarmV6_admin/src/util/kdLeafletMap.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date:2025/7/9 09:25
 */
import {reactive} from "vue";
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet.pm';
import 'leaflet.pm/dist/leaflet.pm.css';
import * as turf from '@turf/turf'
import { useConfigStore } from "@/store/config"
export function useKdLeafletMap(){
    const baseConfig = useConfigStore()
    let map = null
    let polygonEdit = null
    let tdLayer = null
    let tdLayer1 = null
    let layers = []        //图层
    const leafletState = reactive({
        polygonForm:{
            area:0,             //多边形面积
            point:[]            //多边形点
        }
    })

    function initMap(option,callback){
        map = L.map(option.el,{
            zoomControl:false
        }).setView([39.89945,116.40769], 13);
        //添加天地图底图
        addSatelliteLayer(true)
        map.pm.setLang('zh');
        if( typeof callback ==='function'){
            callback()
        }
    }

    //显示卫星影像图层
    function addSatelliteLayer(isSatellite){
        removerSatelliteLayer()
        let tianDiUrl = '' , imageURL1 = ''
        if( isSatellite ){
            //天地图底图
            tianDiUrl = "https://t0.tianditu.gov.cn/img_w/wmts?" +
                "SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&LAYER=img&STYLE=default&TILEMATRIXSET=w&FORMAT=tiles" +
                "&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}&tk="+baseConfig.state.tkToken;
            //标注图层
            imageURL1 = "https://t{s}.tianditu.gov.cn/cva_w/wmts?SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&LAYER=cva&STYLE=default&TILEMATRIXSET=w&FORMAT=tiles&tk="+baseConfig.state.tkToken+"&TILECOL={x}&TILEROW={y}&TILEMATRIX={z}";
        }else{
            //天地图底图
            tianDiUrl = `http://t0.tianditu.gov.cn/vec_w/wmts?SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&LAYER=vec&STYLE=default&TILEMATRIXSET=w&FORMAT=tiles&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}&tk=${baseConfig.state.tkToken}`
            imageURL1 = `http://t0.tianditu.gov.cn/cia_w/wmts?SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&LAYER=cia&STYLE=default&TILEMATRIXSET=w&FORMAT=tiles&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}&tk=${baseConfig.state.tkToken}`
        }
        tdLayer = L.tileLayer(tianDiUrl, {
            maxNativeZoom:18,
            maxZoom:21,
        });
        tdLayer1 = L.tileLayer(imageURL1, {
            subdomains: '01234567', // 天地图的子域名
            maxNativeZoom:18,
            maxZoom:21,
        });
        tdLayer.addTo(map)
        tdLayer1.addTo(map)
    }

    //移除卫星影像图层
    function removerSatelliteLayer(){
        if( tdLayer )tdLayer.remove()
        if( tdLayer1 )tdLayer.remove()
    }

    /**
     * 设置地图中心点位置
     * @param center // [lat,lng]
     * @returns {boolean}
     */
    function setMapCenter(center){
        if( !center || !center.length ) return false
        map.panTo(center)
    }
    /**
     * 开始绘制多边形
     * @param callback
     */
    function drawPolygon(callback){
        if( polygonEdit ) clearCurrentEdit()
        map.pm.enableDraw('Polygon',{
            snappable:true,
            sanpDistance:20,
        })
        map.on('pm:create', function(e) {
            polygonEdit = e.layer      //保存编辑对象
            getPointByGeo(e.layer)
            e.layer.pm.enable({
                allowSelfIntersection:false,
            })
            //多边形编辑
            e.layer.on('pm:edit', (result) => {
                getPointByGeo(result.sourceTarget)
            })
            if( typeof callback === "function"){
                callback()
            }
        });
    }
    /**
     * 编辑或预览多边形
     * @param config { point ,color ,edit }
     * @param callback
     */
    /**
     *
     * @param point     区域
     * @param color     颜色
     * @param edit      是否允许编辑
     * @param isCenter  是否重新定位
     * @param callback
     */
    function polygonEditOrView({point,color,edit=true,isCenter=true },callback){
        if( polygonEdit ) clearCurrentEdit()
        if( !point || !point.length) return false;
        let options = {
            color: color,
            fillColor:color,
        }
        if( point[0].join(',') !== point[point.length-1].join(',')){
            point.push(point[0])
        }
        let arr = []
        point.forEach((item,index)=>{
            arr.push([item[1],item[0]])     //经纬度变换顺序
        })
        let polygon = L.polygon([arr], options).addTo(map);
        polygonEdit = polygon
        if( edit ){
            getAreaByPoint(point)
            polygon.pm.enable({
                allowSelfIntersection: true,
                preventMarkerRemoval: false,  // 禁止右键删除点
            })
            // 监听编辑事件
            polygon.on('pm:edit', e => {
                // 拖动后的坐标
                getPointByGeo(e.target)
                if( typeof callback ==='function'){
                    callback()
                }
            })
        }else{
            getAreaByPoint(point)
        }

        if( isCenter){
            //获取多边形中心区域位置
            let center = getAreaCenter(point)
            setMapCenter(center)
            let zoom = 13
            let { area } = leafletState.polygonForm
            if( area <= 10000) zoom = 18
            else if( area> 10000 && area <=100000 ) zoom = 16
            else if( area> 100000 && area <=1000000 ) zoom = 15
            else if( area> 1000000 && area <=10000000 ) zoom = 13
            else if ( area >10000000 ) zoom = 11
            setMapZoom(zoom)
        }
        layers.push(polygon)
    }

    //获取多边形面积信息
    function getPointByGeo(data){
        let v = data.toGeoJSON() ,point = []
        if( v && v.geometry && v.geometry.coordinates.length > 0 ){
            point = v.geometry.coordinates[0]
        }
        let polygon = turf.polygon([point]);
        leafletState.polygonForm.area = parseFloat( turf.area(polygon) )
        leafletState.polygonForm.point = point
        console.log('最后的坐标数据',point)
    }
    //获取多边形面积信息
    function getAreaByPoint(point){
        let polygon = turf.polygon([point]);
        leafletState.polygonForm.area = turf.area(polygon)  //单位平方米
        leafletState.polygonForm.point =point
    }

    //清除当前编辑的图形
    function clearCurrentEdit() {
        if( !polygonEdit ) return;
        map.removeLayer(polygonEdit)
        polygonEdit = null
    }

    //获取当前定位
    function getCurrentLocation(){
        // map.locate({watch: false})
        // map.on("locationerror",(event)=>{
        //     console.log('定位失败',event)
        // })
        // map.on("locationfound",(event)=>{
        //     console.log('定位成功',event)
        //     if( event.latlng && typeof event.latlng ==='string'){
        //         let arr = event.latlng.split(',')
        //         setMapCenter([arr[0],arr[1]])
        //     }
        // })
        navigator.geolocation.getCurrentPosition(function(position) {
            console.log('获取定位成功',position)
            // 成功获取位置
            let lat = position.coords.latitude;
            let lon = position.coords.longitude;
            setMapCenter([lat,lon])
        }, function(error) {
            // 处理错误情况
            console.error("获取位置失败:", error.message);
            // alert("无法获取您的位置，请检查您的浏览器设置或网络连接。");
        }, {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        });
    }

    /**
     * 获取多边形区域中心位置
     * @param pointArray
     * @returns {string[]}
     */
    function getAreaCenter(pointArray) {
        let sortedLongitudeArray = pointArray.map(item => item[1]).sort();//首先对经度进行排序，红色部分是array中经度的名称
        let sortedLatitudeArray = pointArray.map(item => item[0]).sort();//对纬度进行排序，红色部分是array中纬度的名称
        let centerLongitude = ((parseFloat(sortedLongitudeArray[0]) + parseFloat(sortedLongitudeArray[sortedLongitudeArray.length - 1])) / 2).toFixed(4);
        const centerLatitude = ((parseFloat(sortedLatitudeArray[0]) + parseFloat(sortedLatitudeArray[sortedLatitudeArray.length - 1])) / 2).toFixed(4);
        return [centerLongitude,centerLatitude];
    }

    /**
     * 设置地图缩放大小
     * @param zoom
     */
    function setMapZoom(zoom){
        map.setZoom(zoom)
    }
    return {
        leafletState,
        initMap,
        setMapCenter,
        drawPolygon,
        polygonEditOrView,
        clearCurrentEdit,
        getCurrentLocation,
        addSatelliteLayer
    }
}
