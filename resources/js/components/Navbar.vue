<template>
    <div class="bg-primary-subtle header-fixed" :class="{ 'header-hidden': isHidden }">
        <header class="p-3 mb-3 border-bottom border-primary-subtle">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                    <RouterLink
                        class="navbar-brand link-body-emphasis text-decoration-none mx-2 d-flex align-items-center flex-nowrap"
                        to="/"
                    >
                        <img src="/images/icon.png" alt="BurguerLab" width="40" class="me-2 flex-shrink-0">
                        <span class="fs-4">BurguerLab</span>
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
    </div>
</template>

<script>
import LoginButton from './login/LoginButton.vue';
import { useCartStore } from './stores/cartStore.js';

export default {
    components: { LoginButton },
    data() {
        return {
            lastScrollY: 0,
            isHidden: false
        };
    },
    computed: {
        cartCount() {
            return useCartStore().itemCount;
        }
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        handleScroll() {
            const currentScrollY = window.scrollY;
            if (currentScrollY > this.lastScrollY && currentScrollY > 50) {
                this.isHidden = true;
            } else {
                this.isHidden = false;
            }
            this.lastScrollY = currentScrollY;
        }
    }
};
</script>

<style scoped>
.header-fixed {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    transition: transform 0.3s ease-in-out;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.header-hidden {
    transform: translateY(-100%);
}
</style>