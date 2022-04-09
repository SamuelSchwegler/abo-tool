import {createApp} from 'vue'

require('./bootstrap')
import App from './App.vue'
import axios from 'axios'
import router from './router'
import Notifications from '@kyvg/vue3-notification';

import { createStore } from "vuex";
import createPersistedState from "vuex-persistedstate";

const store = createStore({
    // ...
    plugins: [createPersistedState()],
});

const can = (can) => {
    if (window.Laravel.permissions.length > 0) {
        return window.Laravel.permissions.some(r => can.includes(r))
    }

    return false;
};

const app = createApp(App);
app.config.globalProperties.can = can;
app.config.globalProperties.$axios = axios;
app.use(router)
app.use(Notifications);



app.mount('#app');
