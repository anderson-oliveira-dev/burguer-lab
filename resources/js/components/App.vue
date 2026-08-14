<template>
    <RouterView />
</template>
<script>
    import { useCartStore } from './stores/cartStore';
    import { useAuthStore } from './stores/auth';

    export default {
        async mounted() {
            const authStore = useAuthStore();
            const cartStore = useCartStore();

            authStore.loadToken();

            if (!authStore.isAuthenticated) {
                await cartStore.fetchCart();
            }
        },
    };
</script>