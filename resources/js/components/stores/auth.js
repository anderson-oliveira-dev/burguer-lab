import { defineStore } from 'pinia';
import api from '../services/api';
import { useCartStore } from './cartStore';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        async login(login, password) {
            try {
                const response = await api.post('/login', { login, password });
                this.user = response.data.user;
                this.token = response.data.token;
                localStorage.setItem('auth_token', this.token);

                const cartStore = useCartStore();
                await cartStore.syncCart();
                return true;
            } catch (error) {
                console.error('Erro no login:', error);
                throw error;
            }
        },
        async logout() {
            try {
                await api.post('/logout');
            } catch (error) {
                console.error('Erro no logout:', error);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem('auth_token');

                const cartStore = useCartStore();
                await cartStore.fetchCart();
            }
        },
        async register(userData) {
            try {
                const response = await api.post('/register', userData);
                this.user = response.data.user;
                this.token = response.data.token;
                localStorage.setItem('auth_token', this.token);
                return true;
            } catch (error) {
                console.error('Erro no registro:', error);
                throw error;
            }
        },
        loadToken() {
            const token = localStorage.getItem('auth_token');
            if (token) {
                this.token = token;
                this.fetchUser();
            }
        },
        async fetchUser() {
            try {
                const response = await api.get('/user');
                this.user = response.data;
            } catch {
                this.logout();
            }
        },
    },
});