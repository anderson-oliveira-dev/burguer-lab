import { defineStore } from 'pinia';
import api from '../services/api';

export const useProductStore = defineStore('product', {
    state: () => ({
        products: [],
        loading: false,
    }),
    actions: {
        async fetchProducts() {
            this.loading = true;
            try {
                const response = await api.get('/products');
                this.products = response.data.data;
            } catch (error) {
                console.error('Erro ao buscar produtos:', error);
            } finally {
                this.loading = false;
            }
        },
    },
});