<template>
    <div v-if="isAuthenticated">
        <h1>Perfil</h1>
    </div>
    <div v-else>
        <LoginModal class="d-flex justify-content-center align-items-center vh-100" />
    </div>
</template>
<script>
import LoginModal from '../login/LoginModal.vue';
import { useAuthStore } from '../stores/auth';

export default {
    components: {
        LoginModal
    },
    computed: {
        isAuthenticated() {
            const auth = useAuthStore();
            return auth.isAuthenticated;
        }
    },
}
</script>