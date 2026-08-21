<template>
    <div class="container py-4">
        <h1 class="mb-4">📋 Lista de Pedidos</h1>

        <div v-if="!isAuthenticated">
            <LoginPanel />
        </div>

        <div v-else>
            <div v-if="orderStore.loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>

            <div v-else-if="!orderStore.orders.length" class="text-center py-5">
                <h3>Nenhum pedido encontrado</h3>
                <div v-if="isManager">
                    <p class="text-muted">Faça seu primeiro pedido agora mesmo!</p>
                    <router-link to="/" class="btn btn-primary">Ver cardápio</router-link>
                </div>
                <div v-else>
                    <p class="text-muted">Ainda não há pedidos disponíveis!</p>
                </div>
            </div>

            <div v-else>
                <div v-if="isManager" class="row">
                    <div
                        v-for="(ordersGroup, status) in groupedOrders"
                        :key="status"
                        class="col-md-4 mb-4"
                    >
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ statusLabel(status) }}</h5>
                                <span class="badge bg-secondary">{{ ordersGroup.length }}</span>
                            </div>
                            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                <div v-for="order in ordersGroup" :key="order.id" class="card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <strong>#{{ order.id }}</strong>
                                                <span class="text-muted small"> - {{ order.user?.name || 'Cliente' }}</span>
                                                <br>
                                                <small>Total: R$ {{ Number(order.total_price).toFixed(2) }}</small>
                                                <br>
                                                <small class="text-muted">
                                                    {{new Date(order.created_at).toLocaleString()}}
                                                </small>
                                                <br>
                                                <button
                                                    class="btn btn-sm btn-outline-secondary mt-1"
                                                    @click="viewOrder(order.id)"
                                                >
                                                    Ver detalhes
                                                </button>
                                            </div>
                                            <div class="ms-auto d-flex flex-column align-items-end">
                                                <button
                                                    v-if="order.status === 'awaiting_confirmation'"
                                                    class="btn btn-sm btn-success mb-1"
                                                    @click="changeStatus(order.id, 'preparing')"
                                                >
                                                    Confirmar
                                                </button>
                                                <button
                                                    v-if="order.status === 'preparing'"
                                                    class="btn btn-sm btn-primary mb-1"
                                                    @click="changeStatus(order.id, 'ready_for_delivery')"
                                                >
                                                    Pronto / Sair
                                                </button>
                                                <button
                                                    v-if="order.status === 'ready_for_delivery'"
                                                    class="btn btn-sm btn-info mb-1"
                                                    @click="changeStatus(order.id, 'delivered')"
                                                >
                                                    Entregar
                                                </button>
                                                <button
                                                    v-if="['awaiting_confirmation', 'preparing'].includes(order.status)"
                                                    class="btn btn-sm btn-danger"
                                                    @click="cancelOrder(order.id)"
                                                >
                                                    Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="row">
                    <div
                        v-for="order in orderStore.orders"
                        :key="order.id"
                        class="col-md-6 col-lg-4 mb-4"
                    >
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Pedido #{{ order.id }}</span>
                                <span class="badge" :class="statusBadge(order.status)">
                                    {{ statusLabel(order.status) }}
                                </span>
                            </div>
                            <div class="card-body">
                                <p><strong>Total:</strong> R$ {{ Number(order.total_price).toFixed(2) }}</p>
                                <p><strong>Tipo:</strong> {{ order.type === 'delivery' ? 'Entrega' : 'Retirada' }}</p>
                                <p><strong>Pagamento:</strong> {{ order.payment_method }}</p>
                                <p v-if="order.observations" class="text-muted small">
                                    <strong>Obs:</strong> {{ order.observations }}
                                </p>
                                <p class="text-muted small">
                                    {{ new Date(order.created_at).toLocaleString() }}
                                </p>
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="viewOrder(order.id)"
                                >
                                    Ver detalhes
                                </button>
                                <button
                                    v-if="['awaiting_confirmation', 'preparing'].includes(order.status)"
                                    class="btn btn-sm btn-outline-danger ms-2"
                                    @click="cancelOrder(order.id)"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoginPanel from '../common/LoginPanel.vue';
import { useAuthStore } from '../stores/auth';
import { useOrderStore } from '../stores/orderStore';

export default {
    components: {
        LoginPanel
    },

    data() {
        return {
            pollingInterval: null,
        };
    },

    computed: {
        authStore() {
            return useAuthStore();
        },
        isAuthenticated() {
            return this.authStore.isAuthenticated;
        },
        user() {
            return this.authStore.user;
        },
        isManager() {
            return this.userRole === 'admin' || this.userRole === 'worker';
        },

        orderStore() {
            return useOrderStore();
        },

        groupedOrders() {
            if (!this.isManager) return {};
            const groups = {};
            for (const order of this.orderStore.orders) {
                const status = order.status;
                if (!groups[status]) groups[status] = [];
                groups[status].push(order);
            }
            return groups;
        },
    },

    mounted() {
        if (this.isAuthenticated) {
            this.startPolling();
        }
    },

    beforeUnmount() {
        this.stopPolling();
    },

    methods: {
        statusLabel(status) {
            const map = {
                awaiting_confirmation: 'Aguardando confirmação',
                preparing: 'Preparando',
                ready_for_delivery: 'Pronto / Saiu',
                delivered: 'Entregue / Retirado',
                canceled: 'Cancelado',
            };
            return map[status] || status;
        },

        statusBadge(status) {
            const map = {
                awaiting_confirmation: 'bg-warning',
                preparing: 'bg-info',
                ready_for_delivery: 'bg-primary',
                delivered: 'bg-success',
                canceled: 'bg-danger',
            };
            return map[status] || 'bg-secondary';
        },

        async changeStatus(orderId, newStatus) {
            await this.orderStore.updateStatus(orderId, newStatus);
        },

        async cancelOrder(orderId) {
            await this.orderStore.cancelOrder(orderId);
        },

        viewOrder(orderId) {
            this.$router.push(`/orders/${orderId}`);
        },

        startPolling() {
            if (this.pollingInterval) return;

            this.pollingInterval = setInterval(() => {
                if (this.isAuthenticated) {
                this.orderStore.fetchOrders();
                }
            }, 5000);
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },
    },

    async created() {
        if (this.isAuthenticated) {
            await this.orderStore.fetchOrders();
        }
    },

    watch: {
        isAuthenticated(newVal) {
            if (newVal) {
                this.startPolling();
            } else {
                this.stopPolling();
            }
        },
    },
};
</script>