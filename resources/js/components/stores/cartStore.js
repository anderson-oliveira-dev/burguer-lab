import { defineStore } from 'pinia';
import api from '../services/api';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        total: 0,
        loading: false,
    }),
    getters: {
        itemCount: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    },
    actions: {
        async fetchCart() {
            this.loading = true;
            try {
                const response = await api.get('/cart');
                this.items = response.data.items || [];
                this.total = response.data.total || 0;
            } catch (error) {
                console.error('Erro ao buscar carrinho:', error);
            } finally {
                this.loading = false;
            }
        },

        async addItem(productId, quantity, extras = []) {
            this.loading = true;
            try {
                await api.post('/cart', { product_id: productId, quantity, extras });
                await this.fetchCart(); // recarrega o carrinho atualizado
            } catch (error) {
                console.error('Erro ao adicionar item:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateItem(itemId, quantity) {
            this.loading = true;
            try {
                await api.put(`/cart/${itemId}`, { quantity });
                await this.fetchCart();
            } catch (error) {
                console.error('Erro ao atualizar item:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async removeItem(itemId) {
            this.loading = true;
            try {
                await api.delete(`/cart/${itemId}`);
                await this.fetchCart();
            } catch (error) {
                console.error('Erro ao remover item:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async clearCart() {
            this.loading = true;
            try {
                await api.delete('/cart/clear');
                this.items = [];
                this.total = 0;
            } catch (error) {
                console.error('Erro ao limpar carrinho:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async syncCart() {
            this.loading = true;
            try {
                const itemsToSync = this.items.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    extras: item.extras || [],
                }));
                await api.post('/cart/sync', { items: itemsToSync });
                await this.fetchCart();
            } catch (error) {
                console.error('Erro ao sincronizar carrinho:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});