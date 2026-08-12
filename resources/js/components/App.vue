<template>
    <div id="app">
        <nav>
            <RouterLink to="/">Produtos</RouterLink>
            <RouterLink to="/track-order">Acompanhar Pedido</RouterLink>
        </nav>
        <RouterView />
    </div>
</template>
<script>
export default {
    name: 'App',
    data() {
        return {
            
        }
    },
    methods: {
        
    }
}
</script>