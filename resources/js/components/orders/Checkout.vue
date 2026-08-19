<template>
    <div class="container py-4">
        <h1 class="mb-4">Finalizar pedido</h1>

        <div v-if="cartStore.loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

        <div v-else-if="!cartStore.items.length" class="text-center py-5">
            <h3>Seu carrinho está vazio</h3>
            <router-link to="/" class="btn btn-primary">Voltar às compras</router-link>
        </div>

        <div v-else>
            <div class="row">
                <div class="col-lg-6">
                    <h4 class="mb-3">Itens do pedido</h4>
                    <div class="list-group">
                        <div v-for="item in cartStore.items" :key="item.id" class="list-group-item">
                            <div class="d-flex align-items-start gap-3">
                                <img
                                    v-if="item.product?.image"
                                    :src="item.product.image"
                                    alt="Produto"
                                    class="img-thumbnail"
                                    style="width: 70px; height: 70px; object-fit: cover;"
                                />
                                <div v-else class="bg-light img-thumbnail d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                    <span class="text-muted small">Sem foto</span>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ item.product?.name || 'Produto' }}</strong>
                                        <span class="fw-bold text-success">
                                            R$ {{ calculateItemTotal(item).toFixed(2) }}
                                        </span>
                                    </div>

                                    <div v-if="item.extras && item.extras.length" class="mt-1">
                                        <span
                                            v-for="extra in item.extras"
                                            :key="extra.id"
                                            class="badge bg-secondary me-1"
                                        >
                                            {{ extra.name }} (+ R$ {{ Number(extra.price).toFixed(2) }})
                                        </span>
                                    </div>
                                    <span v-else class="text-muted small">Sem extras</span>

                                    <div class="text-muted small mt-1">
                                        Quantidade: {{ item.quantity }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <h5>
                            Subtotal: <span class="text-success">R$ {{ cartStore.total.toFixed(2) }}</span>
                        </h5>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h4>Dados do pedido</h4>
                    <form @submit.prevent="submitOrder">
                        <div class="mb-3">
                            <label class="form-label">Tipo de pedido</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" id="delivery" value="delivery" v-model="form.type" autocomplete="off">
                                <label class="btn btn-outline-primary" for="delivery">Entrega</label>

                                <input type="radio" class="btn-check" id="pickup" value="pickup" v-model="form.type" autocomplete="off">
                                <label class="btn btn-outline-primary" for="pickup">Retirada</label>
                            </div>
                        </div>

                        <div class="mb-3" v-if="form.type === 'delivery'">
                            <label for="address" class="form-label">Endereço de entrega</label>
                            <input type="text" class="form-control" id="address" v-model="form.address" placeholder="Rua, número, bairro" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefone para contato</label>
                            <input type="text" class="form-control" id="phone" v-model="form.phone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Forma de pagamento</label>
                            <select class="form-select" v-model="form.payment_method" required>
                                <option value="cash">Dinheiro</option>
                                <option value="card">Cartão</option>
                                <option value="pix">PIX</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="observations" class="form-label">Observações (opcional)</label>
                            <textarea class="form-control" id="observations" rows="3" v-model="form.observations" placeholder="Ex: tirar cebola, ponto da carne"></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <router-link to="/cart" class="btn btn-outline-secondary">Voltar ao carrinho</router-link>
                            <button type="submit" class="btn btn-success btn-lg" :disabled="submitting">
                                <span v-if="submitting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Finalizar pedido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useCartStore } from '../stores/cartStore';
import { useAuthStore } from '../stores/auth';
import { useOrderStore } from '../stores/orderStore';

export default {
    data() {
        return {
            submitting: false,
            form: {
                type: 'delivery',
                address: '',
                phone: '',
                payment_method: 'cash',
                observations: '',
                delivery_fee: 0,
            },
        };
    },
    computed: {
        cartStore() {
            return useCartStore();
        },
        authStore() {
            return useAuthStore();
        },
    },
    created() {
        const user = this.authStore.user;
        if (user) {
            this.form.phone = user.phone || '';
            this.form.address = user.address || '';
        }
    },
    methods: {
        calculateItemTotal(item) {
            const extrasTotal = (item.extras || []).reduce((sum, extra) => sum + (extra.price || 0), 0);
            return (Number(item.unit_price) + extrasTotal) * item.quantity;
        },

        async submitOrder() {
            this.submitting = true;
            try {
                const payload = {
                    type: this.form.type,
                    address: this.form.type === 'delivery' ? this.form.address : null,
                    phone: this.form.phone,
                    payment_method: this.form.payment_method,
                    observations: this.form.observations,
                    delivery_fee: this.form.delivery_fee,
                };

                const orderStore = useOrderStore();
                await orderStore.createOrder(payload);

                this.cartStore.items = [];
                this.cartStore.total = 0;

                this.$router.push({ name: 'orders' });
            } catch (error) {
                console.error('Erro ao finalizar pedido:', error);
                alert(error.response?.data?.message || 'Erro ao finalizar pedido. Tente novamente.');
            } finally {
                this.submitting = false;
            }
        },
    },
};
</script>