/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

import App from './components/App.vue'
import router from './router'
import axios from 'axios'
import Notifications from '@kyvg/vue3-notification'

/**
 * Store data
 */
import User from './store/User';
/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp(App);
app.config.globalProperties.$axios = axios;
app.use(router);
app.use(User);
app.use(Notifications);
app.mount('#app');
