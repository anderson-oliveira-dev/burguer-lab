import { defineStore } from 'pinia';
import axios from 'axios';

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
                const response = await axios.post('/api/login', { login, password });
                this.user = response.data.user;
                this.token = response.data.token;

                localStorage.setItem('auth_token', this.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                return true;
            } catch (error) {
                console.error('Erro no login:', error);
                throw error;
            }
        },
        async logout() {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Erro no logout:', error);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem('auth_token');
                delete axios.defaults.headers.common['Authorization'];
            }
        },
        async register() {
            try {
                await axios.post('/api/register');
            } catch (error) {
                console.error('Erro no logout:', error);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem('auth_token');
                delete axios.defaults.headers.common['Authorization'];
            }
        },
        loadToken() {
            const token = localStorage.getItem('auth_token');
            if (token) {
                this.token = token;
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

                this.fetchUser();
            }
        },
        async fetchUser() {
            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
            } catch {
                this.logout();
            }
        },
    },
});