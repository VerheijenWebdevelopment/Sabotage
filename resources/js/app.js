
// Load Vue
window.Vue = require('vue');

// Lodash
window._ = require('lodash');

// Axios
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
import VueAxios from 'vue-axios';
Vue.use(VueAxios, window.axios);

// Vuetify
import Vuetify from 'vuetify';
Vue.use(Vuetify);

// Pusher
window.Pusher = require('pusher-js');

// Echo
import Echo from "laravel-echo";
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    disableStats: true,
});

// Automatically load all vue components
const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Create the Vue.js application
const app = new Vue({
    el: '#app',
    vuetify: new Vuetify({
        icons: {
            iconfont: 'mdiSvg', // 'mdi' || 'mdiSvg' || 'md' || 'fa' || 'fa4'
        },
    }),
});