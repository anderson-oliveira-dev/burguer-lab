import { defineStore } from 'pinia';
import api from '../services/api';
import { notifyError, notifySuccess } from '../services/notify';

export const useExtraStore = defineStore('extra', {
    state: () => ({
        extras: [],
        loading: false,
    }),
    actions: {
        async fetchExtras() {
            this.loading = true;
            try {
                const response = await api.get('/extras');
                this.extras = response.data.data;
            } catch (error) {
                console.error('Erro ao buscar extras:', error);
                notifyError('Erro ao buscar extras.');
            } finally {
                this.loading = false;
            }
        },
    },
});