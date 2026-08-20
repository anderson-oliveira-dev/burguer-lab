<template>
    <div class="container py-4">
        <h1 class="mb-4">👤 Perfil</h1>
        <div v-if="isAuthenticated">

        </div>
        <div v-else>
            <LoginPanel />
        </div>
    </div>
</template>
<script>
import LoginPanel from '../common/LoginPanel.vue';
import { useAuthStore } from '../stores/auth';

export default {
    components: {
        LoginPanel
    },
    computed: {
        isAuthenticated() {
            const auth = useAuthStore();
            return auth.isAuthenticated;
        }
    },
}
</script>