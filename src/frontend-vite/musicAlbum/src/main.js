import { createApp } from 'vue'
import App from './App.vue'
import router from './router/index'
import axios from 'axios';


axios.defaults.baseURL = "https://yjikwe.lafyun.com";

const app = createApp(App)
app.use(router)
app.mount('#app')
