<template>
    <div v-if="isAuthenticated">
        <h1>Lista de Pedidos</h1>
    </div>
    <div v-else>
        <LoginButton class="d-flex justify-content-center align-items-center vh-100" />
    </div>
</template>
<script>
import LoginButton from '../login/LoginButton.vue';
import { useAuthStore } from '../stores/auth';

export default {
    components: {
        LoginButton
    },
    computed: {
        isAuthenticated() {
            const auth = useAuthStore();
            return auth.isAuthenticated;
        }
    },
}
</script>