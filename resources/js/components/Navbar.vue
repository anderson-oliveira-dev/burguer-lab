<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <RouterLink class="navbar-brand" to="/">BurguerLab</RouterLink>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <RouterLink class="nav-link" active-class="active" to="/">Início</RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink class="nav-link" active-class="active" to="/orders">Pedidos</RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink class="nav-link" active-class="active" to="/cart">
                            Carrinho
                            <span class="badge text-bg-primary">{{ cartCount }}</span>
                        </RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink class="nav-link" active-class="active" to="/profile">Perfil</RouterLink>
                    </li>
                </ul>
            </div>
            <div>
                <LoginButton />
            </div>
        </div>
    </nav>
</template>
<script>
import LoginButton from './login/LoginButton.vue';

export default {
    components: {
        LoginButton
    }
}
</script>