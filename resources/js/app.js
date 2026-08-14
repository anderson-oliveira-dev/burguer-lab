import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useAuthStore } from './components/stores/auth.js';

import App from './components/App.vue';
import router from './router';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

const authStore = useAuthStore();
authStore.loadToken();

app.mount('#app');