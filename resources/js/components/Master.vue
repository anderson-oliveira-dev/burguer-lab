<template>
    <Navbar />

    <main>
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
import LoginButton from './login/LoginButton.vue';
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