<template>
    <div class="container py-4">
        <h1 class="mb-4">🍔 Cardápio</h1>

        <div v-if="loading" class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4">
            <div v-for="(item, index) in [1,2,3,4]" :key="index" class="col">
                <ProductPlaceholder />
            </div>
        </div>

        <div v-else>
            <div v-if="!Object.keys(groupedProducts).length" class="alert alert-warning" role="alert">
                Sem produtos disponíveis
            </div>

            <div v-else v-for="(products, category) in groupedProducts" :key="category">
                <h3 class="mt-4 mb-3">{{ category || 'Sem categoria' }}</h3>
                <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-4">
                    <div v-for="product in products" :key="product.id" class="col">
                        <ProductCard
                            :product="product"
                            @open-modal="openProductModal"
                        />
                    </div>
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
import { ref, computed } from 'vue';
import { mapState, mapActions } from 'pinia';
import { useProductStore } from '../stores/productStore.js';
import ProductCard from './ProductCard.vue';
import ProductPlaceholder from './placeholders/ProductPlaceholder.vue';
import ProductModal from './ProductModal.vue';

export default {
    components: {
        ProductCard,
        ProductPlaceholder,
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

        groupedProducts() {
            return this.products.reduce((acc, product) => {
                const category = product.category || 'Sem categoria';
                if (!acc[category]) {
                    acc[category] = [];
                }
                acc[category].push(product);
                return acc;
            }, {});
        }
    },
    mounted() {
        this.fetchProducts();
    },
    methods: {
        ...mapActions(useProductStore, ['fetchProducts']),
    },
};
</script>