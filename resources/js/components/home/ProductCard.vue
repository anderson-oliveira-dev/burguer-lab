<template>
    <div class="card h-100">
        <div v-if="product.image" class="card-img-top" :style="{ backgroundImage: `url(${product.image})`, backgroundSize: 'cover', height: '200px' }"></div>
        <div v-else class="card-img-top bg-secondary" style="height: 200px;"></div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ product.name }}</h5>
            <p class="card-text flex-grow-1">{{ product.description }}</p>
            <p class="card-text"><strong>R$ {{ product.price.toFixed(2) }}</strong></p>
            <button class="btn btn-primary mt-auto" @click="openModal">Comprar</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        product: {
            type: Object,
            required: true,
        },
    },
    emits: ['open-modal'],
    methods: {
        openModal() {
            this.$emit('open-modal', this.product);
        },
    },
};
</script>