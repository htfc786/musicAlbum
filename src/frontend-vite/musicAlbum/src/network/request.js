// https://blog.csdn.net/saienenen/article/details/115030205
import axios from 'axios'

import CONF from '@/config'

const service = axios.create({
  baseURL: CONF.API_BASE_URL, // api的base_url
  timeout: 15000 // 请求超时时间
  // .... 其他信息
})

//响应拦截器
service.interceptors.response.use(
	function(response) {
		//....
		return response
	},
	function(error) {
		return Promise.reject(error)
	}
)

//请求拦截器 
service.interceptors.request.use(
	function(config) {
		//...
		return config
	},
	function(error) {
		return Promise.reject(error)
	}
)



// 封装请求方法
export function request(query) {
	return service
		.request(query)
		.then((res) => {
			return Promise.resolve(res)
		})
		//对错误进行处理
		.catch((e) => {
			return Promise.reject(e)
		})
}

//post请求  ----> json格式的post请求 
export function post(url, params) {
	return request({
		url: url,
		method: 'post',
		data: params,
	})
}

//Get请求
export function get(url, params) {
	return request({
		url: url,
		method: 'get',
		params: params,
	})
}

//post请求
export function form(url, params, onUploadProgress) {
	return request({
		url: url,
		method: 'post',
		data: params,
		headers: {
			'Content-Type': 'multipart/form-data',
		},
		onUploadProgress: onUploadProgress,
	})
}
