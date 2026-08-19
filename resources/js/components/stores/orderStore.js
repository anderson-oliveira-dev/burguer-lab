import { defineStore } from 'pinia';
import api from '../services/api';
import { notifyError, notifySuccess } from '../services/notify';
import { confirm } from '../services/dialog';

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

        statusLabel: (state) => (status) => {
            const map = {
                awaiting_confirmation: 'Aguardando confirmação',
                preparing: 'Preparando',
                ready_for_delivery: 'Pronto / Saiu',
                delivered: 'Entregue / Retirado',
                canceled: 'Cancelado',
            };
            return map[status] || status;
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
                console.error('Erro ao buscar pedidos:', error);
                notifyError('Erro ao buscar pedidos.');
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
                notifyError('Erro ao buscar pedido.');
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
                notifyError('Erro ao finalizar pedido. Tente novamente');
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateStatus(orderId, newStatus) {
            const label = this.statusLabel(newStatus);
            const confirmed = await confirm(
                'Atualizar pedido',
                `Deseja alterar o pedido #${orderId} para "${label}"?`,
                'warning',
                'Sim, Atualizar',
                'Cancelar'
            );

            if(confirmed){
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
                    notifySuccess('Status atualizado com sucesso!');
                    return updatedOrder;
                } catch (error) {
                    this.error = error.response?.data?.message || 'Erro ao atualizar status';
                    notifyError('Erro ao atualizar status.');
                    throw error;
                } finally {
                    this.loading = false;
                }
            }
        },

        async cancelOrder(orderId) {
            const confirmed = await confirm(
                'Cancelar pedido',
                `Tem certeza que deseja cancelar o pedido #${orderId}?`,
                'warning',
                'Sim, Cancelar',
                'Não'
            );

            if(confirmed){
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
                    notifySuccess('Pedido cancelado com sucesso!');
                    return updatedOrder;
                } catch (error) {
                    this.error = error.response?.data?.message || 'Erro ao cancelar pedido';
                    notifyError('Erro ao cancelar pedido.');
                    throw error;
                } finally {
                    this.loading = false;
                }
            }
        },

        clearOrders() {
            this.orders = [];
            this.currentOrder = null;
            this.error = null;
        },
    },
});