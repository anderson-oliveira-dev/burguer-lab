import axios from 'axios';
import { getGuestId } from './guest';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        const guestId = getGuestId();
        config.headers['X-Guest-Id'] = guestId;
        return config;
    },
    (error) => Promise.reject(error)
);

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            // Opcional: tratar logout automático
        }
        return Promise.reject(error);
    }
);

export default api;