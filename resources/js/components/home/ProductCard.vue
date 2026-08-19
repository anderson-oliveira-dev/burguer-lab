<template>
    <div class="card h-100 card-hover">
        <div v-if="product.image" class="card-img-top" :style="{ backgroundImage: `url(${product.image})`, backgroundSize: 'cover' }"></div>
        <div v-else class="card-img-top bg-secondary"></div>
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
<style scoped>
.card-img-top {
    height: 250px;
    object-fit: cover;
}
@media (max-width: 576px) {
    .card-img-top {
        height: 280px;
    }
}

.card-hover {
    transition: transform 0.25s ease;
    will-change: transform;
}

.card-hover:hover {
    transform: scale(1.04);
    z-index: 1;
}
</style>