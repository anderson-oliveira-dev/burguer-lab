<template>
    <div class="container py-4">
        <h1 class="mb-4">Cardápio</h1>

        <div v-if="loading" class="row g-4">
            <div v-for="(item, index) in [1,2,3,4]" :key="index" class="col-3">
                <ProductPlaceholder />
            </div>
        </div>

        <div v-else class="row g-3">
            <div v-if="products.length" v-for="product in products" :key="product.id" class="col-3">
                <ProductCard
                    :product="product"
                    @open-modal="openProductModal"
                />
            </div>
            <div v-else>
                <div class="alert alert-warning" role="alert">
                    Sem produtos disponíveis
                </div>
            </div>
        </div>

        <ProductModal
            :product="selectedProduct"
            :isOpen="modalOpen"
            @close="modalOpen = false"
        />
    </div>
</template>

<script>
import ProductCard from './ProductCard.vue';
import ProductPlaceholder from './placeholders/ProductPlaceholder.vue';
import ProductModal from './ProductModal.vue'; // ← importe o modal

import { useProductStore } from '../stores/productStore.js';
import { mapState, mapActions } from 'pinia';
import { ref } from 'vue';

export default {
    components: {
        ProductPlaceholder,
        ProductCard,
        ProductModal,
    },
    setup() {
        const modalOpen = ref(false);
        const selectedProduct = ref(null);

        function openProductModal(product) {
            selectedProduct.value = product;
            modalOpen.value = true;
        }

        return {
            modalOpen,
            selectedProduct,
            openProductModal,
        };
    },
    computed: {
        ...mapState(useProductStore, ['products', 'loading']),
    },
    mounted() {
        this.fetchProducts();
    },
    methods: {
        ...mapActions(useProductStore, ['fetchProducts']),
    },
};
</script>