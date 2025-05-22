import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import axios from 'axios';

// Set up axios
axios.defaults.withCredentials = true;
axios.defaults.baseURL = '/api';

// Add token to all requests
axios.interceptors.request.use(config => {
    const token = store.state.auth.token;
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

const app = createApp(App);

// Use plugins
app.use(router);
app.use(store);

// Mount app
app.mount('#app');
