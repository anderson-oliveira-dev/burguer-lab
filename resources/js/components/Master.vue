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
                        <RouterLink class="nav-link" active-class="active" to="/cart">Carrinho</RouterLink>
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

    <main>
        <div class="container">
            <RouterView />
        </div>
    </main>

    <LoginModal />
    <ProductModal />

    <footer class="footer mt-auto py-3 text-sm lh-normal">
        <div class="container">
            © 2026 <a href="https://github.com/anderson-oliveira-dev" class="link-underline link-underline-opacity-0 link-underline-opacity-75-hover">Anderson Oliveira</a>
        </div>
    </footer>
</template>
<script>
import ProductModal from './home/ProductModal.vue';
import LoginButton from './login/LoginButton.vue';
import LoginModal from './login/LoginModal.vue';

export default {
    components: {
        LoginModal,
        LoginButton,
        ProductModal
    }
}
</script>