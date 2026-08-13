<template>
    <div>
        <h1>Cardápio</h1>
        <div v-if="loading" class="row">
            <div v-for="(item, index) in [1,2,3,4]" :key="index" class="col-3">
                <ProductPlaceholder />
            </div>
        </div>
        <div v-else class="row">
            <div v-for="product in products" :key="product.id" class="col-3">
                <ProductCard
                    :image="product.image"
                    :title="product.title"
                    :description="product.description"
                    :price="product.price"
                />
            </div>
        </div>
    </div>
</template>
<script>
import ProductCard from './placeholders/ProductCard.vue';
import ProductPlaceholder from './placeholders/ProductPlaceholder.vue';

export default{
    components: {
        ProductPlaceholder,
        ProductCard
    },
    data() {
        return {
            loading: true,
            products: [
                {
                    id: 1,
                    image: 'https://via.placeholder.com/300x200/ff7f50/fff?text=Produto+1',
                    title: 'Pizza Margherita',
                    description: 'Molho de tomate, mussarela, manjericão.',
                    price: 39.90
                },
                {
                    id: 2,
                    image: 'https://via.placeholder.com/300x200/4682b4/fff?text=Produto+2',
                    title: 'Hambúrguer Artesanal',
                    description: 'Pão brioche, carne 180g, queijo cheddar.',
                    price: 29.90
                },
                {
                    id: 3,
                    image: 'https://via.placeholder.com/300x200/32cd32/fff?text=Produto+3',
                    title: 'Salada Caesar',
                    description: 'Alface, frango, croutons, parmesão.',
                    price: 24.90
                },
                {
                    id: 4,
                    image: 'https://via.placeholder.com/300x200/ff6347/fff?text=Produto+4',
                    title: 'Suco Natural',
                    description: 'Laranja com cenoura, sem açúcar.',
                    price: 12.90
                }
            ]
        }
    },
    mounted(){
        this.loadProducts();
    },
    methods: {
        loadProducts(){
            setTimeout(() => {
                this.loading = false;
            }, 2000);
        }
    }
}
</script>