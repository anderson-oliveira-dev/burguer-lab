<template>
    <div>
        <h1>Cardápio</h1>
        <div v-if="loading" class="row">
            <div v-for="(item, index) in [1,2,3,4]" :key="index" class="col-3">
                <ProductPlaceholder />
            </div>
        </div>
        <div v-else>
            Produtos
        </div>
    </div>
</template>
<script>
import ProductPlaceholder from './placeholders/ProductPlaceholder.vue';

export default{
    components: {
        ProductPlaceholder
    },
    data() {
        return {
            loading: true,
        }
    },
    mounted(){
        //loadProducts();
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