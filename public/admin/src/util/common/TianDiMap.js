/**
 * 坤典物联
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称:  kundian_iot_admin/src/util/common/TianDiMap.js
 * @description File path and name: kundian_iot_admin/src/util/common/TianDiMap.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-06-18 11:00:07
 */

import { reactive } from "vue";
import { useConfigStore } from '@/store/config'
export function useTianDiMap(){
	let satelliteLayer1 = null	//卫星图层
	let satelliteLayer2 = null
	const configStore = useConfigStore()
	const tianState = reactive({
		mapObj:null,			//地图操作对象
		localSearchObj:null,	//搜索对象
		search:{				//搜索结果展示
			list:[],
			show:false,
			loading:false,
		},
		markerList:[],			//标注内容
		selectLocation:{
			marker:null,		//当前选择的定位标记
			location:[],		//当前选择的定位经纬度
			address:"",			//详细地址
		},
		tk:configStore.state.tkToken
	})
	
	function initMap(option,callback){
		const map = new T.Map(option.el);
		let [lng,lat] = option?.center && option.center.length ? option.center : [116.40769, 39.89945]
		map.centerAndZoom(new T.LngLat(lng, lat), 15);
		tianState.mapObj = map
		
		//获取当前定位
		// getCurrentLocation()
		
		//判断当前是否有点击事件监听
		if( option?.isClick){
			map.on('click',(e) =>{
				addMarker(e.lnglat.lng,e.lnglat.lat,{draggable:true,isResolution:true})
				resolutionLnglatToAddress(e.lnglat.lng,e.lnglat.lat)
			})
		}
		
		//判断当前图层是否显示卫星图层
		if( option.isSatellite ){
			addSatelliteLayer(true)
		}
		
		if( typeof callback === 'function'){
			callback(true)
		}
	}
	
	//显示卫星影像图层
	function addSatelliteLayer(isSatellite){
		removerSatelliteLayer()
		let imageURL = "https://t0.tianditu.gov.cn/img_w/wmts?" +
			"SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&LAYER=img&STYLE=default&TILEMATRIXSET=w&FORMAT=tiles" +
			"&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}&tk="+tianState.tk;
		let imageURL1 = `https://t2.tianditu.gov.cn/cva_w/wmts?SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&LAYER=cva&STYLE=default&TILEMATRIXSET=w&FORMAT=tiles&tk=${tianState.tk}&TILECOL={x}&TILEROW={y}&TILEMATRIX={z}`;
		//创建自定义图层对象
		satelliteLayer1 = new T.TileLayer(imageURL, {minZoom: 1, maxZoom: 18,zIndex:1});
		satelliteLayer2 = new T.TileLayer(imageURL1, {minZoom: 1, maxZoom: 18,zIndex:2});
		//将图层增加到地图上
		tianState.mapObj.addLayer(satelliteLayer1);
		tianState.mapObj.addLayer(satelliteLayer2);
	}
	
	//获取当前定位
	function getCurrentLocation(){
		var lo = new T.Geolocation();
		lo.getCurrentPosition(function(e){
			if (this.getStatus() == 0){
				tianState.mapObj.centerAndZoom(e.lnglat, 15)
			}
			if(this.getStatus() == 1){
				tianState.mapObj.centerAndZoom(e.lnglat, e.level)
			}
		});
	}
	
	//创建搜索对象
	function createSearchLocation(){
		try{
			//搜索结果内容处理
			const localSearchResult = function(result){
				tianState.search.loading = false
				tianState.search.show = true
				switch (parseInt(result.getResultType())) {
					case 1:
						// console.log('result.getPois()',result.getPois())
						//解析点数据结果
						tianState.search.list = result.getPois()
						break;
					case 2:
						//解析推荐城市
						// statistics(result.getStatistics());
						break;
					case 3:
						//解析行政区划边界,获取到城市信息
						let areaData = result.getArea()
						if( areaData.name ){
							doSearch(areaData.name,4)
						}
						break;
					case 4:
						//解析建议词信息
						tianState.search.list = result.getSuggests()
						break;
					case 5:
						//解析公交信息
						// lineData(result.getLineData());
						break;
				}
			}
			var config = {
				pageCapacity: 10,	//每页显示的数量
				onSearchComplete: localSearchResult	//接收数据的回调函数
			};
			//创建搜索对象
			tianState.localSearchObj = new T.LocalSearch(tianState.mapObj, config);
		}catch(e){
			console.log('创建搜索对象报错',e);
		}
	}
	
	/**
	 * 搜索操作
	 * @param {String} key	搜索关键词
	 */
	function doSearch(key,searchType=null){
		tianState.search.loading = true
		if( searchType ){
			tianState.localSearchObj.search(key,searchType)
		}else{
			tianState.localSearchObj.search(key)
		}
	}
	
	/**
	 * 设置地图中心位置
	 * @param {Object} lng
	 * @param {Object} lat
	 */
	function setMapCenter(lng,lat){
		tianState.mapObj.centerAndZoom(new T.LngLat(lng, lat), 15);
	}
	
	/**
	 * 添加普通标注
	 * @param {Number} lng
	 * @param {Number} lat
	 * @param {Object} options { draggable:是否可拖动,isResolution:根据坐标点反地址解析, }
	 */
	function addMarker(lng,lat,options){
		//存在标记，先移除标记
		removeMarker()
		// if( tianState.selectLocation.marker ){
		// 	tianState.mapObj.removeOverLay(tianState.selectLocation.marker)
		// 	tianState.selectLocation.marker = null
		// }
		//创建标注对象
		var marker = new T.Marker(new T.LngLat(lng, lat),{ draggable:options.draggable || false });
		//向地图上添加标注
		tianState.mapObj.addOverLay(marker);
		//将标注存起来，方便清除
		tianState.selectLocation.marker = marker
		tianState.selectLocation.location = [lng,lat]
		setMapCenter(lng, lat)
		
		//判断标记是否可拖动，监听拖动事件
		if( options?.draggable ){
			marker.addEventListener("dragend",event=>{
				//位置更新，修改当前标记位置
				tianState.selectLocation.location = [event.lnglat.lng,event.lnglat.lat]
				
				//解析地址
				if( options?.isResolution){
					resolutionLnglatToAddress(event.lnglat.lng,event.lnglat.lat)
				}
			})
		}
		
		//解析地址
		if( options?.isResolution){
			resolutionLnglatToAddress(lng,lat)
		}
	}
	
	//移除标记
	function removeMarker(){
		if( tianState.selectLocation.marker ){
			tianState.mapObj.removeOverLay(tianState.selectLocation.marker)
			tianState.selectLocation.marker = null
		}
	}
	
	//地址逆解析
	function resolutionLnglatToAddress(lng,lat){
		const geocode = new T.Geocoder();
		geocode.getLocation(new T.LngLat(lng, lat),(result)=>{
			tianState.selectLocation.address = result.getAddress()
		});
	}
	
	//移除卫星影像图层
	function removerSatelliteLayer(){
		if( satelliteLayer1 ) tianState.mapObj.removeLayer(satelliteLayer1)
		if( satelliteLayer2 ) tianState.mapObj.removeLayer(satelliteLayer2)
	}
	
	/**
	 * 批量添加标注
	 * @param data      标注数据
	 * @param isClick   是否监听点击事件
	 * @param callback  回调
	 */
	function addBatchMarker(data,{isClick=false},callback){
		//判断是否已存在标注
		if( tianState.markerList.length ){
			for (let i = 0; i < tianState.markerList.length; i++) {
				tianState.mapObj.removeOverLay(tianState.markerList[i])
			}
			tianState.markerList = []
		}
		data.forEach(item=>{
			//创建图片对象
			// let icon = new T.Icon({
			// 	iconUrl: $util.getStaticSrc("admin/map/live-ding-marker.png"),
			// 	iconSize: new T.Point(80, 80),
			// 	iconAnchor: new T.Point(80, 80)
			// });
			//向地图上添加自定义标注
			let marker = new T.Marker(new T.LngLat(item.longitude,item.latitude));
			tianState.mapObj.addOverLay(marker);
			tianState.markerList.push(marker)
			if( isClick) {
				marker.on('click',function (event){
					if( typeof callback === 'function'){
						callback(item)
					}
				})
			}
		})
	}
	
	return { 
		tianState,initMap,createSearchLocation,doSearch,getCurrentLocation,
		setMapCenter ,addMarker,removeMarker,addBatchMarker
	}
}