<template>
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header p-5 pb-4 border-bottom-0">
                    <h1 class="fw-bold mb-0 fs-2" id="loginModalLabel">Login</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 pt-0">
                    <form @submit.prevent="handleLogin">
                        <div class="mb-3">
                            <label for="loginInput" class="form-label">Email ou Telefone</label>
                            <input type="text" class="form-control" id="loginInput" v-model="form.login" required />
                        </div>
                        <div class="mb-3">
                            <label for="passwordInput" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="passwordInput" v-model="form.password" required />
                        </div>
                        <button type="submit" class="btn btn-primary w-100" :disabled="loading">
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
</template>

<script>
import { Modal } from 'bootstrap';
import { useAuthStore } from '../stores/auth';
import { useModalStore } from '../stores/modal';

export default {
    data() {
        return {
            form: { login: '', password: '' },
            loading: false,
            error: null,
            modalInstance: null,

            unwatch: null,
            onShown: null,
            onHidden: null
        };
    },
    mounted() {
        const modalEl = document.getElementById('loginModal');
        if (modalEl) {
            this.modalInstance = new Modal(modalEl);

            this.onShown = () => {
                useModalStore().open('login');
            };
            this.onHidden = () => {
                useModalStore().close('login');
            };

            modalEl.addEventListener('shown.bs.modal', this.onShown);
            modalEl.addEventListener('hidden.bs.modal', this.onHidden);
        }

        this.unwatch = this.$watch(
            () => useModalStore().isOpen('login'),
            (newVal) => {
                if (newVal) {
                    this.modalInstance?.show();
                } else {
                    this.modalInstance?.hide();
                }
            },
            { immediate: true }
        );
    },
    beforeUnmount() {
        if (this.unwatch) this.unwatch();
        const modalEl = document.getElementById('loginModal');
        if (modalEl) {
            modalEl.removeEventListener('shown.bs.modal', this.onShown);
            modalEl.removeEventListener('hidden.bs.modal', this.onHidden);
        }
    },
    methods: {
        closeModal() {
            this.modalInstance?.hide();
        },
        async handleLogin() {
            this.loading = true;
            this.error = null;
            try {
                const authStore = useAuthStore();
                await authStore.login(this.form.login, this.form.password);
                this.closeModal();
                alert('Login realizado com sucesso!');
            } catch (err) {
                this.error = 'Credenciais inválidas. Tente novamente.';
            } finally {
                this.loading = false;
            }
        },
        goToRegister() {
            const modalEl = document.getElementById('loginModal');
            const handleHidden = () => {
                modalEl.removeEventListener('hidden.bs.modal', handleHidden);
                this.$router.push('/register');
            };
            modalEl.addEventListener('hidden.bs.modal', handleHidden);
            this.modalInstance?.hide();
        }
    }
};
</script>
<style scoped>
.modal-dialog {
    width: 380px;
}
</style>