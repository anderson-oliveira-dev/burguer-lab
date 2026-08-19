<template>
    <div>
        <button
            v-if="!isAuthenticated"
            type="button"
            class="btn btn-primary"
            @click="openLoginModal"
        >
            Entrar
        </button>
        <button
            v-else
            type="button"
            class="btn btn-danger"
            @click="handleLogout"
        >
            Sair
        </button>
    </div>
</template>

<script>
import { useAuthStore } from '../stores/auth';
import { useModalStore } from '../stores/modal';

export default {
    computed: {
        isAuthenticated() {
            return useAuthStore().isAuthenticated;
        }
    },
    methods: {
        openLoginModal() {
            useModalStore().open('login');
        },
        async handleLogout() {
            await useAuthStore().logout();
        }
    }
};
</script>