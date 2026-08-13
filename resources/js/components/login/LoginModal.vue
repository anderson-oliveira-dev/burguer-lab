<template>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
            Entrar
        </button>

        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="handleLogin">
                            <div class="mb-3">
                                <label for="loginInput" class="form-label">Email ou Telefone</label>
                                <input type="text" class="form-control" id="loginInput" v-model="form.login" required />
                            </div>
                            <div class="mb-3">
                                <label for="passwordInput" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="passwordInput" v-model="form.password" required />
                            </div>
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                {{ loading ? 'Entrando...' : 'Entrar' }}
                            </button>
                            <div v-if="error" class="text-danger mt-2">{{ error }}</div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <p class="mb-0">
                            Não tem conta?
                            <a href="#" @click.prevent="goToRegister">Cadastre-se</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '../stores/auth';

export default {
    data() {
        return {
            form: {
                login: '',
                password: '',
            },
            loading: false,
            error: null,
        };
    },
    methods: {
        async handleLogin() {
            this.loading = true;
            this.error = null;
            try {
                const authStore = useAuthStore();
                await authStore.login(this.form.login, this.form.password);
                window.location.href = '/';
            } catch (err) {
                this.error = 'Credenciais inválidas. Tente novamente.';
            } finally {
                this.loading = false;
            }
        },
        goToRegister() {
            const modalEl = document.getElementById('loginModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            this.$router.push('/register');
        },
    },
};
</script>