<template>
    <div class="container py-4">
        <div v-if="orderStore.loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

        <div v-else-if="!order" class="text-center py-5">
            <h3>Pedido não encontrado</h3>
            <router-link to="/orders" class="btn btn-primary">Voltar aos pedidos</router-link>
        </div>

        <div v-else>
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h1 class="mb-1">Pedido #{{ order.id }}</h1>
                    <p class="text-muted">
                        {{ new Date(order.created_at).toLocaleString() }}
                    </p>
                </div>
                <span class="badge" :class="statusBadge(order.status)" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                    {{ statusLabel(order.status) }}
                </span>
            </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informações do pedido</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold">Tipo:</td>
                                <td>{{ order.type === 'delivery' ? 'Entrega' : 'Retirada' }}</td>
                            </tr>
                            <tr v-if="order.type === 'delivery'">
                                <td class="fw-bold">Endereço:</td>
                                <td>{{ order.address || 'Não informado' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Telefone:</td>
                                <td>{{ order.phone || 'Não informado' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Pagamento:</td>
                                <td>{{ paymentMethodLabel(order.payment_method) }}</td>
                            </tr>
                            <tr v-if="order.observations">
                                <td class="fw-bold">Observações:</td>
                                <td>{{ order.observations }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    <span class="badge" :class="statusBadge(order.status)">
                                        {{ statusLabel(order.status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div v-if="canAct" class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Ações</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <div v-if="isAdmin">
                                <button
                                    v-if="order.status === 'awaiting_confirmation'"
                                    class="btn btn-success"
                                    @click="changeStatus('preparing')"
                                >
                                    Confirmar Pedido
                                </button>
                                <button
                                    v-if="order.status === 'preparing'"
                                    class="btn btn-primary"
                                    @click="changeStatus('ready_for_delivery')"
                                >
                                    Pronto / Sair para entrega
                                </button>
                                <button
                                    v-if="order.status === 'ready_for_delivery'"
                                    class="btn btn-info"
                                    @click="changeStatus('delivered')"
                                >
                                    Entregar / Retirado
                                </button>
                            </div>
                            <button
                                v-if="['awaiting_confirmation', 'preparing'].includes(order.status)"
                                class="btn btn-danger"
                                @click="cancelOrder"
                            >
                                Cancelar Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Itens do pedido</h5>
                    </div>
                    <div class="card-body">
                        <div v-if="order.items && order.items.length">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="border-bottom pb-2 mb-2"
                            >
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ item.product?.name || 'Produto #' + item.product_id }}</strong>
                                        <span class="text-muted small"> ({{ item.quantity }}x)</span>
                                        <div v-if="item.extras && item.extras.length" class="mt-1">
                                            <span
                                                v-for="extra in item.extras"
                                                :key="extra.id"
                                                class="badge bg-secondary me-1"
                                            >
                                                {{ extra.name }} (+ R$ {{ Number(extra.price).toFixed(2) }})
                                            </span>
                                        </div>
                                    </div>
                                    <span class="fw-bold">R$ {{ Number(item.subtotal).toFixed(2) }}</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between">
                                    <span>Subtotal</span>
                                    <span>R$ {{ subtotal.toFixed(2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between" v-if="order.delivery_fee > 0">
                                    <span>Taxa de entrega</span>
                                    <span>R$ {{ Number(order.delivery_fee).toFixed(2) }}</span>
                                </div>
                                <hr />
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total</span>
                                    <span class="text-success">R$ {{ Number(order.total_price).toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p class="text-muted">Nenhum item encontrado.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class="mt-4">
                <router-link to="/orders" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para pedidos
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import { useOrderStore } from '../stores/orderStore';
import { useAuthStore } from '../stores/auth';

export default {
    computed: {
        orderStore() {
            return useOrderStore();
        },
        authStore() {
            return useAuthStore();
        },
        order() {
            return this.orderStore.currentOrder;
        },
        isAdmin() {
            return this.user && this.user.type === 'admin';
        },
        canAct() {
            if (!this.order) return false;
            const user = this.authStore.user;
            if (!user) return false;
            return user.type === 'admin' || user.id === this.order.user_id;
        },
        subtotal() {
            if (!this.order || !this.order.items) return 0;
            return this.order.items.reduce((sum, item) => sum + Number(item.subtotal), 0);
        },
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
        paymentMethodLabel(method) {
            const map = {
                cash: 'Dinheiro',
                card: 'Cartão',
                pix: 'PIX',
            };
            return map[method] || method;
        },

        async changeStatus(newStatus) {
            const label = this.statusLabel(newStatus);
            if (!confirm(`Deseja alterar o status para "${label}"?`)) return;
            try {
                await this.orderStore.updateStatus(this.order.id, newStatus);
                alert('Status atualizado com sucesso!');
            } catch (error) {
                alert('Erro ao atualizar status: ' + (error.message || ''));
            }
        },

        async cancelOrder() {
            if (!confirm('Tem certeza que deseja cancelar este pedido?')) return;
            try {
                await this.orderStore.cancelOrder(this.order.id);
                alert('Pedido cancelado com sucesso.');
                this.$router.push('/orders');
            } catch (error) {
                alert('Erro ao cancelar pedido: ' + (error.message || ''));
            }
        },
    },

    async created() {
        const id = this.$route.params.id;
        if (id) {
            try {
                await this.orderStore.fetchOrder(id);
            } catch (error) {
                console.error('Erro ao carregar pedido:', error);
                this.$router.push('/orders');
            }
        }
    },

    watch: {
        '$route.params.id': function (newId) {
            if (newId) {
                this.orderStore.fetchOrder(newId);
            }
        },
    },
};
</script>

<style scoped>
.table-sm td {
    padding: 0.3rem 0;
}
</style>