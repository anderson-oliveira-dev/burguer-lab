<template>
    <div class="card" aria-hidden="true">
        <div v-if="image" class="card-img-top" :style="{ backgroundImage: `url(${image})`, backgroundSize: 'cover', height: '200px' }"></div>
        <div v-else class="card-img-top bg-secondary" style="height: 200px;"></div>

        <div class="card-body">
            <h5 class="card-title">{{ title }}</h5>
            <p class="card-text flex-grow-1">{{ description }}</p>
            <p class="card-text"><strong>R$ {{ price.toFixed(2) }}</strong></p>
            <a href="#" class="btn btn-primary mt-auto" @click.prevent="addToCart">Comprar</a>
        </div>
    </div>
</template>
<script>
export default{
    props: {
        image: {
            type: String,
            default: ''
        },
        title: {
            type: String,
            required: true
        },
        description: {
            type: String,
            default: 'Sem descrição'
        },
        price: {
            type: Number,
            required: true
        }
    },
    methods: {
        addToCart() {
            alert(`Adicionado ao carrinho: ${this.title}`);
        }
    }
}
</script>
<style scoped>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>