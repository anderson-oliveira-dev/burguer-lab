import { defineStore } from 'pinia';
import axios from 'axios';

export const useExtraStore = defineStore('extra', {
    state: () => ({
        extras: [],
        loading: false,
    }),
    actions: {
        async fetchExtras() {
            this.loading = true;
            try {
                const response = await axios.get('/api/extras');
                this.extras = response.data.data;
            } catch (error) {
                console.error('Erro ao buscar extras:', error);
            } finally {
                this.loading = false;
            }
        },
    },
});