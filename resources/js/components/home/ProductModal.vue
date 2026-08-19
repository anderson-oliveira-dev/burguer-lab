<template>
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 v-if="product" class="modal-title" id="productModalLabel">{{ product.name }}</h5>
                    <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="product">
                        <img v-if="product.image" :src="product.image" class="img-fluid mb-3" />
                        <p>{{ product.description }}</p>
                        <p><strong>Preço base:</strong> R$ {{ product.price.toFixed(2) }}</p>

                        <div v-if="allExtras.length" class="mb-3">
                            <h6>Escolha os extras:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <button
                                    v-for="extra in allExtras"
                                    :key="extra.id"
                                    class="btn"
                                    :class="isExtraSelected(extra.id) ? 'btn-primary' : 'btn-outline-secondary'"
                                    @click="toggleExtra(extra)"
                                >
                                    {{ extra.name }} (+ R$ {{ extra.price.toFixed(2) }})
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantidade</label>
                            <div class="input-group" style="max-width: 150px;">
                                <button class="btn btn-outline-secondary" @click="decrementQuantity">-</button>
                                <input
                                    type="number"
                                    class="form-control text-center"
                                    v-model.number="quantity"
                                    min="1"
                                    @input="validateQuantity"
                                />
                                <button class="btn btn-outline-secondary" @click="incrementQuantity">+</button>
                            </div>
                        </div>

                        <p><strong>Total:</strong> R$ {{ total.toFixed(2) }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="closeModal">Cancelar</button>
                    <button class="btn btn-primary" @click="addToCart">Adicionar ao carrinho</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from 'bootstrap';
import { useExtraStore } from '../stores/extraStore';
import { mapState } from 'pinia';
import { useCartStore } from '../stores/cartStore';

export default {
    props: {
        product: {
            type: Object,
            required: true,
        },
        isOpen: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['close'],
    data() {
        return {
            selectedExtras: [],
            quantity: 1,
            modalInstance: null,
            unwatch: null,
            onShown: null,
            onHidden: null,
        };
    },
    computed: {
        ...mapState(useExtraStore, ['extras']),
        allExtras() {
            return this.extras;
        },
        total() {
            const extrasTotal = this.selectedExtras.reduce((sum, extra) => sum + extra.price, 0);
            return (this.product.price + extrasTotal) * this.quantity;
        },
    },
    mounted() {
        const modalEl = document.getElementById('productModal');
        if (modalEl) {
            this.modalInstance = new Modal(modalEl);

            this.onShown = () => {};
            this.onHidden = () => {
                this.$emit('close');
            };

            modalEl.addEventListener('shown.bs.modal', this.onShown);
            modalEl.addEventListener('hidden.bs.modal', this.onHidden);
        }

        this.unwatch = this.$watch(
            () => this.isOpen,
            (newVal) => {
                if (newVal) {
                    const extraStore = useExtraStore();
                    if (extraStore.extras.length === 0) {
                        extraStore.fetchExtras();
                    }
                    this.selectedExtras = [];
                    this.quantity = 1;
                    this.modalInstance?.show();
                } else {
                    this.modalInstance?.hide();
                }
            },
            { immediate: true }
        );
    },
    beforeUnmount() {
        if (this.unwatch) this.unwatch();
        const modalEl = document.getElementById('productModal');
        if (modalEl) {
            modalEl.removeEventListener('shown.bs.modal', this.onShown);
            modalEl.removeEventListener('hidden.bs.modal', this.onHidden);
        }
        this.modalInstance?.dispose();
    },
    methods: {
        toggleExtra(extra) {
            const index = this.selectedExtras.findIndex(e => e.id === extra.id);
            if (index > -1) {
                this.selectedExtras.splice(index, 1);
            } else {
                this.selectedExtras.push(extra);
            }
        },
        isExtraSelected(extraId) {
            return this.selectedExtras.some(e => e.id === extraId);
        },
        incrementQuantity() {
            this.quantity++;
        },
        decrementQuantity() {
            if (this.quantity > 1) this.quantity--;
        },
        validateQuantity() {
            if (this.quantity < 1) this.quantity = 1;
        },
        async addToCart() {
            const cartStore = useCartStore();
            await cartStore.addItem(
                this.product.id,
                this.quantity,
                this.selectedExtras
            );
            this.closeModal();
        },
        closeModal() {
            this.modalInstance?.hide();
        },
    },
};
</script>