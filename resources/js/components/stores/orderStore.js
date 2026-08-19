import { defineStore } from 'pinia';
import api from '../services/api';

export const useOrderStore = defineStore('order', {
    state: () => ({
        orders: [],
        currentOrder: null,
        loading: false,
        error: null,
    }),
    getters: {
        getByStatus: (state) => (status) => {
            return state.orders.filter(order => order.status === status);
        },

        groupedByStatus: (state) => {
            const groups = {};
            for (const order of state.orders) {
                if (!groups[order.status]) groups[order.status] = [];
                groups[order.status].push(order);
            }
            return groups;
        },
    },
    actions: {
        async fetchOrders() {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get('/orders');
                this.orders = response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erro ao buscar pedidos';
                console.error('Erro fetchOrders:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchOrder(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get(`/orders/${id}`);
                this.currentOrder = response.data;
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erro ao buscar pedido';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createOrder(orderData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/orders', orderData);
                this.orders.unshift(response.data.order);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erro ao criar pedido';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateStatus(orderId, newStatus) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.put(`/orders/${orderId}/status`, { status: newStatus });
                const updatedOrder = response.data.order;

                const index = this.orders.findIndex(o => o.id === orderId);
                if (index !== -1) {
                    this.orders[index] = updatedOrder;
                }
                if (this.currentOrder?.id === orderId) {
                    this.currentOrder = updatedOrder;
                }
                return updatedOrder;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erro ao atualizar status';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async cancelOrder(orderId) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post(`/orders/${orderId}/cancel`);
                const updatedOrder = response.data.order;
                const index = this.orders.findIndex(o => o.id === orderId);
                if (index !== -1) {
                    this.orders[index] = updatedOrder;
                }
                if (this.currentOrder?.id === orderId) {
                    this.currentOrder = updatedOrder;
                }
                return updatedOrder;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erro ao cancelar pedido';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        clearOrders() {
            this.orders = [];
            this.currentOrder = null;
            this.error = null;
        },
    },
});