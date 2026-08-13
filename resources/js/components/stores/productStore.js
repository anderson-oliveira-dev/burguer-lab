import { defineStore } from 'pinia';
import axios from 'axios';

export const useProductStore = defineStore('product', {
    state: () => ({
        products: [],
        loading: false,
    }),
    actions: {
        async fetchProducts() {
            this.loading = true;
            try {
                const response = await axios.get('/api/products');
                this.products = response.data.data;
            } catch (error) {
                console.error('Erro ao buscar produtos:', error);
            } finally {
                this.loading = false;
            }
        },
    },
});