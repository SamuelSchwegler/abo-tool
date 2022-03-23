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

const app = createApp(App)
app.config.globalProperties.$axios = axios;
app.use(router)
app.use(Notifications);
//app.use(store);
app.mount('#app')
