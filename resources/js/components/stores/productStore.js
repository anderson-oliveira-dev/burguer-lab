import { defineStore } from 'pinia';
import api from '../services/api';
import { notifyError, notifySuccess } from '../services/notify';

export const useProductStore = defineStore('product', {
    state: () => ({
        products: [],
        loading: false,
    }),
    getters: {
        getByCategory: (state) => (category) => {
            return state.products.filter(product => product.category === category);
        },
    },
    actions: {
        async fetchProducts() {
            this.loading = true;
            try {
                const response = await api.get('/products');
                this.products = response.data.data;
            } catch (error) {
                console.error('Erro ao buscar produtos:', error);
                notifyError('Erro ao buscar produtos.');
            } finally {
                this.loading = false;
            }
        },
        async fetchProductById(id) {
            try {
                const response = await api.get(`/products/${id}`);
                return response.data.data;
            } catch (error) {
                console.error('Erro ao buscar produto:', error);
                notifyError('Erro ao buscar produto.');
            }
        },
        async createProduct(data) {
            try{
                const response = await api.post('/products', data);
                this.products.push(response.data.data);
                return response.data.data;
            } catch (error) {
                console.error('Erro ao registrar produto:', error);
                notifyError('Erro ao registrar produto.');
            }
        },
        async updateProduct(id, data) {
            try{
                const response = await api.put(`/products/${id}`, data);
                const index = this.products.findIndex(p => p.id === id);
                if (index !== -1) this.products[index] = response.data.data;
                return response.data.data;
            } catch (error) {
                console.error('Erro ao atualizar produto:', error);
                notifyError('Erro ao atualizar produto.');
            }
        },
    },
});