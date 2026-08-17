<template>
    <div class="text-center py-5">
        <h3>Já possui conta?</h3>
        <p class="text-muted">Faça login para ter acesso completo aos serviços.</p>
        <button
            v-if="!isAuthenticated"
            type="button"
            class="btn btn-primary"
            @click="openLoginModal"
        >
            Entrar ou cadastrar
        </button>
    </div>
</template>
<script>
import { useModalStore } from '../stores/modal';

export default {
    methods: {
        openLoginModal() {
            useModalStore().open('login');
        }
    }
};
</script>