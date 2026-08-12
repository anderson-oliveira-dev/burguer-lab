<template>
    <div id="app">
        <nav>
            <RouterLink to="/">Início</RouterLink>
            <RouterLink to="/orders">Pedidos</RouterLink>
            <RouterLink to="/profile">Perfil</RouterLink>
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