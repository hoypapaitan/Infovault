/*
=========================================================
Muse - Vue Ant Design Dashboard - v1.0.0
=========================================================

Product Page: https://www.creative-tim.com/product/vue-ant-design-dashboard
Copyright 2021 Creative Tim (https://www.creative-tim.com)
Coded by Creative Tim

=========================================================
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. 
*/

import Vue from 'vue'
import axios from 'axios'
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import App from './App.vue'
import DefaultLayout from './layouts/Default.vue'
import DashboardLayout from './layouts/Dashboard.vue'
import DashboardRTLLayout from './layouts/DashboardRTL.vue'
import AuthLayout from './layouts/Auth.vue'
import router from './router'
// import './plugins/click-away'

import './scss/app.scss';

const baseURL = process.env.NODE_ENV === 'development' ? 
'http://localhost:8080/infovault/api/v1' : 
'/infovault/api/v1'

const api = axios.create({ baseURL })

api.interceptors.request.use(
  config => {
      // let appData = localStorage.getItem('userToken');
      // appData = JSON.parse(appData);
      // const token = appData.value;
      // if (token) {
      //     config.headers['Authorization'] = 'Bearer ' + token;
      // }
      return config;
  },
  error => {
      Promise.reject(error)
  }
);

// Add response interceptor to handle API errors
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 500) {
      console.error('Server Error:', error.response.data);
      // Don't throw 500 errors to prevent app crashes
      return Promise.resolve({ data: null, error: true });
    }
    return Promise.reject(error);
  }
);

Vue.use(Antd);
Vue.prototype.$api = api
Vue.config.productionTip = false

// Adding template layouts to the vue components.
Vue.component("layout-default", DefaultLayout);
Vue.component("layout-dashboard", DashboardLayout);
Vue.component("layout-dashboard-rtl", DashboardRTLLayout);
Vue.component("layout-auth", AuthLayout);


new Vue({
  router,
  render: h => h(App)
}).$mount('#app')