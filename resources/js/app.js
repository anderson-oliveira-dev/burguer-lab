import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useAuthStore } from './components/stores/auth.js';
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import App from './components/App.vue';
import router from './router';

import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap';

const app = createApp(App);
const pinia = createPinia();

const options = {
    maxToasts: 5,
    position: 'bottom-right',
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    showCloseButtonOnHover: false,
    hideProgressBar: true,
    closeButton: "button",
    icon: true,
    rtl: false
}

app.use(pinia);
app.use(router);
app.use(Toast, options)

const authStore = useAuthStore();
authStore.loadToken();

app.mount('#app');