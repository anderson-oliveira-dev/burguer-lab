<template>
    <div>
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <RouterLink class="navbar-brand" to="/">BurguerLab</RouterLink>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <RouterLink class="nav-link" active-class="active" to="/">Início</RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink class="nav-link" active-class="active" to="/orders">Pedidos</RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink class="nav-link" active-class="active" to="/cart">Carrinho</RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink class="nav-link" active-class="active" to="/profile">Perfil</RouterLink>
                    </li>
                </ul>
            </div>
        </div>
        </nav>
    </div>

    <div>
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

<style>
.nav-link {
    visibility: visible;
}

</style>