/**
 * 坤典物联 -工具函数
 * @link https://www.cqkundian.com
 * @description 软件开发团队为 重庆坤典科技有限公司
 * @description The software development team is Chongqing Kundian Network Technology Co., Ltd.
 * @description 软件著作权归 重庆坤典科技有限公司 所有 软著登记号: 2021SR0143549
 * @description 软件版权归 重庆坤典科技有限公司 所有
 * @description The software copyright belongs to Chongqing Kundian Technology Co., Ltd.
 * @description 文件路径与名称:  kundian_iot_admin/src/util/util.js
 * @description File path and name:  kundian_iot_admin/src/util/util.js
 * @description 本文件由重庆坤典科技授权予 重庆坤典科技 使用
 * @description This file is licensed to 重庆坤典科技-cqkundian.com
 * @warning 这不是一个免费的软件，使用前请先获取正式商业授权
 * @warning This is not a free software, please get the license before use.
 * @warning 未经授权许可禁止转载分发，违者将追究其法律责任
 * @warning It is prohibited to reprint and distribute without license, and violators will be investigated for legal responsibility
 * @warning 未经授权许可禁止删除本段注释，违者将追究其法律责任
 * @warning It is prohibited to delete this comment without license, and violators will be held legally responsible
 * @Date: 2025-07-24 09:43:26
 */

import { dayjs } from "@arco-design/web-vue/es/_utils/date"
import { useConfigStore } from '@/store/config'
import axios from "axios"
import { QNRTPlayer } from "qn-rtplayer-web";
/**
 * 获取随机字符串
 * @param {Number} num 随机字符串个数
 */
export function getRandom(num){
	let str = new Date().getTime() +''+Math.floor(Math.random() * 900 + 100)
	return str.substring(str.length - num )
}

/**
 * 时间格式化
 */
export function formatTime(time,format='YYYY-MM-DD HH:mm:ss'){
	if( !time ) return '-'
	return dayjs(time).format(format)
}

/**
 * 经纬度转详细地址
 * @param {String} lon	经度
 * @param {String} lat	纬度
 */
export async function lngLatToAddress(lon,lat){
	if( !lon || !lat ) return ''
	const config = useConfigStore()
	const tk = config.state.tkToken
	let apiUrl = `https://api.tianditu.gov.cn/geocoder?postStr={'lon':${lon},'lat':${lat},'ver':1}&type=geocode&tk=${tk}`
	return await axios({
		url:apiUrl,
		method:'get',
	})
}

/**
 * 国标监控播放
 * @param {String} webrtc		播放地址
 * @param {String} el			播放容器
 * @param {String} lat			纬度
 * @param {Function} callback 	回调函数
 * @param {Function} errorBack 	错误内容返回函数
 */
export function playGbLive({ webrtc,el },callback,errorBack){
	//开发环境下端口必须为1240才能正常播放
	if( import.meta.env.MODE === 'development' ){
		webrtc = webrtc.replace('447','1240')
	}
	console.log('webrtc======',webrtc)
	//七牛播放器
	const player = new QNRTPlayer();
	// 2. 初始化配置信息
	player.init({controls:true,playsinline:false});
	setTimeout(function (){
		// 3. 传入播放地址及容器，开始播放
		player.play( webrtc, document.getElementById(el) )
		
		player.on('error', (error) => {
			console.log('播放出错',error);
			errorBack(error)
		});
		callback(player)
	},1000)
}