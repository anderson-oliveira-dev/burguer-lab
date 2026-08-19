import { defineStore } from 'pinia';
import api from '../services/api';
import { useCartStore } from './cartStore';
import { notifyError, notifySuccess } from '../services/notify';
import { confirm } from '../services/dialog';

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

                notifySuccess('Login realizado com sucesso!');
                return true;
            } catch (error) {
                console.error('Erro no login:', error);
                notifyError('Credenciais inválidas. Tente novamente.');
                throw error;
            }
        },
        async logout() {
            const confirmed = await confirm(
                'Deseja realmente sair?',
                'Você precisará fazer login novamente para acessar sua conta.',
                'warning',
                'Sim, sair',
                'Cancelar'
            );

            if(confirmed){
                try {
                    await api.post('/logout');
                    notifySuccess('Logout realizado com sucesso!');
                } catch (error) {
                    console.error('Erro no logout:', error);
                    notifyError('Erro ao fazer logout.');
                } finally {
                    this.user = null;
                    this.token = null;
                    localStorage.removeItem('auth_token');

                    const cartStore = useCartStore();
                    await cartStore.fetchCart();
                }
            }
        },
        async register(userData) {
            try {
                const response = await api.post('/register', userData);
                this.user = response.data.user;
                this.token = response.data.token;
                localStorage.setItem('auth_token', this.token);

                notifySuccess('Cadastro realizado com sucesso!');
                return true;
            } catch (error) {
                console.error('Erro no registro:', error);
                notifyError('Falha no cadastro. Tente novamente.');
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
                notifyError('Sessão expirada. Faça login novamente.');
            }
        },
    },
});