<template>
    <div>
        <h1>Cardápio</h1>
        <div v-if="loading" class="row g-4">
            <div v-for="(item, index) in [1,2,3,4]" :key="index" class="col-3">
                <ProductPlaceholder />
            </div>
        </div>
        <div v-else class="row g-3">
            <div v-if="products.length" v-for="product in products" :key="product.id" class="col-3">
                <ProductCard
                    :image="product.image"
                    :title="product.name"
                    :description="product.description"
                    :price="product.price"
                />
            </div>
            <div v-else>
                <div class="alert alert-warning" role="alert">
                    Sem produtos disponíveis
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ProductCard from './placeholders/ProductCard.vue';
import ProductPlaceholder from './placeholders/ProductPlaceholder.vue';

import { useProductStore } from '../stores/productStore.js';
import { mapState, mapActions } from 'pinia';

export default{
    components: {
        ProductPlaceholder,
        ProductCard
    },
    computed: {
        ...mapState(useProductStore, ['products', 'loading'])
    },
    mounted(){
        this.fetchProducts();
    },
    methods: {
        ...mapActions(useProductStore, ['fetchProducts'])
    },
}
</script>