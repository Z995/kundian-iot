import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'
// import { pxtovw } from "./util/pxtovw";


export default defineConfig({
	plugins: [vue()],
	server: {
		// 是否自动在浏览器打开
		open: false,
		// 是否开启 https
		https: false,
		port:5555,
		cors: true,
		host:'0.0.0.0'
	},
	resolve: {
        alias: {
            "@": path.resolve(__dirname, "./src"),
            "public": path.resolve(__dirname, "./public"),
        },
    },
	// css: {
	// 	postcss: {
	// 		plugins: [pxtovw()],
	// 	},
  	// },
})
