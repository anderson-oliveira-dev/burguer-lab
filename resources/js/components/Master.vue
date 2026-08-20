<template>
    <Navbar />

    <main class="main-content">
        <div class="container">
            <RouterView />
        </div>
    </main>

    <LoginModal />
    <ProductModal />

    <Footer />
</template>
<script>
import Footer from './Footer.vue';
import ProductModal from './home/ProductModal.vue';
import LoginModal from './login/LoginModal.vue';
import Navbar from './Navbar.vue';

export default {
    components: {
        LoginModal,
        ProductModal,
        Navbar,
        Footer
    }
}
</script>
<style scoped>
.main-content {
    padding-top: 90px;
}

@media (max-width: 990px) {
    .main-content {
        padding-top: 180px;
    }
}

</style>