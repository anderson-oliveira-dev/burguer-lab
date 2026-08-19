<!--template>
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
</template-->

<template>
    <header class="p-3 mb-3 border-bottom">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <RouterLink class="navbar-brand link-body-emphasis text-decoration-none mx-2" to="/">
                    <span class="fs-4">
                        BurguerLab
                    </span>
                </RouterLink>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li>
                        <RouterLink active-class="active" to="/" class="nav-link px-2 link-body-emphasis">Início</RouterLink>
                    </li>
                    <li>
                        <RouterLink active-class="active" to="/orders" class="nav-link px-2 link-body-emphasis">Pedidos</RouterLink>
                    </li>
                    <li>
                        <RouterLink active-class="active" to="/cart" class="nav-link px-2 link-body-emphasis">
                            Carrinho
                            <span class="badge text-bg-primary">{{ cartCount }}</span>
                        </RouterLink>
                    </li>
                    <li>
                        <RouterLink active-class="active" to="/profile" class="nav-link px-2 link-body-emphasis">Perfil</RouterLink>
                    </li>
                </ul>
                <div class="dropdown text-end">
                    <LoginButton />
                </div>
            </div>
        </div>
    </header>
</template>

<script>
import LoginButton from './login/LoginButton.vue';

export default {
    components: {
        LoginButton
    }
}
</script>