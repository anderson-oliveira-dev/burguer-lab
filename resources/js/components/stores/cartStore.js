import { defineStore } from 'pinia';
import api from '../services/api';
import { notifyError, notifySuccess } from '../services/notify';

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
                notifyError('Erro ao buscar carrinho.');
            } finally {
                this.loading = false;
            }
        },

        async addItem(productId, quantity, extras = []) {
            this.loading = true;
            try {
                await api.post('/cart', { product_id: productId, quantity, extras });
                await this.fetchCart();
                notifySuccess('Produto adicionado ao carrinho!');
            } catch (error) {
                console.error('Erro ao adicionar item:', error);
                notifyError('Erro ao adicionar ao carrinho. Tente novamente.');
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
                notifyError('Erro ao atualizar item.');
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async removeItem(itemId) {
            this.loading = true;
            try {
                await api.delete(`/cart/${itemId}`);
                notifySuccess('Item removido!');
                await this.fetchCart();
            } catch (error) {
                console.error('Erro ao remover item:', error);
                notifyError('Erro ao remover item.');
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async clearCart() {
            this.loading = true;
            try {
                await api.delete('/cart/clear');
                notifySuccess('Carrinho limpo!');
                this.items = [];
                this.total = 0;
            } catch (error) {
                console.error('Erro ao limpar carrinho:', error);
                notifyError('Erro ao limpar carrinho.');
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
                notifyError('Erro ao sincronizar carrinho.');
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});