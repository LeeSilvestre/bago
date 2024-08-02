import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify'
import { loadFonts } from './plugins/webfontloader'
import 'vuetify/dist/vuetify.min.css';
import axios from "axios";

window.axios = axios
axios.defaults.withCredentials = false
// axios.defaults.baseURL = "http://localhost:8081/api"
let backendUrl = "http://localhost:8000/api"

axios.defaults.baseURL = backendUrl

loadFonts()

createApp(App)
  .use(router)
  .use(store)
  .use(vuetify)
  .mount('#app')

