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


/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faUser, faPen, faCalendarDays, faCircleExclamation } from '@fortawesome/free-solid-svg-icons'

/* add icons to the library */
library.add(faUser, faPen, faCalendarDays, faCircleExclamation)

/**
 * Store data
 */
import Storage from './Storage';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp(App);
app.config.globalProperties.$axios = axios;

app.component('font-awesome-icon', FontAwesomeIcon);

app.use(router);
app.use(Storage);
app.use(Notifications);

app.mount('#app');
