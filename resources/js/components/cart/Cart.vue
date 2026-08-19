<template>
    <div class="container py-4">
        <h1 class="mb-4">🛒 Carrinho de compras</h1>

        <div v-if="cartStore.loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

        <div v-else-if="!cartStore.items.length" class="text-center py-5">
            <h3>Seu carrinho está vazio</h3>
            <p class="text-muted">Continue comprando e adicione seus produtos favoritos.</p>
            <router-link to="/" class="btn btn-primary">Ver produtos</router-link>
        </div>

        <div v-else>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th class="d-none d-sm-table-cell">Preço unit.</th>
                            <th>Extras</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                <tbody>
                    <tr v-for="item in cartStore.items" :key="item.id">
                        <td>
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center">
                                <img
                                    v-if="item.product?.image"
                                    :src="item.product.image"
                                    alt="Produto"
                                    class="img-thumbnail me-sm-3 mb-2 mb-sm-0"
                                    style="width: 60px; height: 60px; object-fit: cover;"
                                />
                                <div>
                                    <strong>{{ item.product?.name || 'Produto' }}</strong>
                                    <p class="text-muted small mb-0">
                                        Código: #{{ item.product_id }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="d-none d-sm-table-cell">R$ {{ Number(item.unit_price).toFixed(2) }}</td>
                        <td>
                            <span v-if="item.extras && item.extras.length">
                                <span
                                    v-for="(extra, index) in item.extras"
                                    :key="extra.id || index"
                                    class="badge bg-secondary me-1"
                                >
                                    {{ extra.name }} (+ R$ {{ Number(extra.price).toFixed(2) }})
                                </span>
                            </span>
                            <span v-else class="text-muted">Nenhum</span>
                        </td>
                        <td style="min-width: 120px;">
                            <div class="input-group input-group-sm">
                                <button
                                    class="btn btn-outline-secondary"
                                    @click="updateQuantity(item, item.quantity - 1)"
                                    :disabled="item.quantity <= 1"
                                >
                                    -
                                </button>
                                <input
                                    type="number"
                                    class="form-control text-center"
                                    v-model.number="item.quantity"
                                    min="1"
                                    @change="updateQuantity(item, item.quantity)"
                                    @input="validateQuantity(item)"
                                />
                                <button
                                    class="btn btn-outline-secondary"
                                    @click="updateQuantity(item, item.quantity + 1)"
                                >
                                    +
                                </button>
                            </div>
                        </td>
                        <td>R$ {{ calculateSubtotal(item).toFixed(2) }}</td>
                        <td>
                            <button
                            class="btn btn-danger btn-sm"
                            @click="removeItem(item.id)"
                            title="Remover item"
                            >
                                <i class="bi bi-trash"></i> Remover
                            </button>
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <button class="btn btn-outline-danger" @click="clearCart">
                        <i class="bi bi-eraser"></i> Limpar carrinho
                    </button>
                </div>
                <div class="col-md-6 text-md-end">
                    <h4>
                        Total: <span class="text-success">R$ {{ cartStore.total.toFixed(2) }}</span>
                    </h4>
                    <p class="text-muted">
                        <small>{{ cartStore.itemCount }} item(s) no carrinho</small>
                    </p>
                    <button class="btn btn-success btn-lg" @click="checkout">
                        <i class="bi bi-credit-card"></i> Finalizar compra
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { confirm } from '../services/dialog';
import { useCartStore } from '../stores/cartStore';
import { mapState } from 'pinia';

export default {
    data() {
        return {

        };
    },
    computed: {
        ...mapState(useCartStore, ['items', 'total', 'loading']),
        cartStore() {
            return useCartStore();
        },
    },
    methods: {
        calculateSubtotal(item) {
            const extrasTotal = (item.extras || []).reduce((sum, extra) => sum + (extra.price || 0), 0);
            return (Number(item.unit_price) + extrasTotal) * item.quantity;
        },

        async updateQuantity(item, newQuantity) {
            if (newQuantity < 1) return;
            if (newQuantity === item.quantity) return;
            try {
                await this.cartStore.updateItem(item.id, newQuantity);
            } catch (error) {
                this.cartStore.fetchCart();
            }
        },

        validateQuantity(item) {
            if (item.quantity < 1 || isNaN(item.quantity)) {
                item.quantity = 1;
            }
        },

        async removeItem(itemId) {
            await this.cartStore.removeItem(itemId);
        },

        async clearCart() {
            await this.cartStore.clearCart();
        },

        checkout() {
            this.$router.push('/checkout');
        },
    },
};
</script>

<style scoped>
.table td {
    vertical-align: middle;
}
.input-group input[type="number"] {
    max-width: 60px;
}
</style>