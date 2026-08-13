<template>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 fw-bold">Criar Conta</h3>
                        <form @submit.prevent="handleRegister" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nome completo</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Ex: João Silva"
                                    required
                                />
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">E-mail</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    v-model="form.email"
                                    placeholder="seu@email.com"
                                    required
                                />
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">Telefone</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    id="phone"
                                    v-model="form.phone"
                                    placeholder="(00) 00000-0000"
                                    required
                                />
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold">Endereço</label>
                                <textarea
                                    class="form-control"
                                    id="address"
                                    rows="2"
                                    v-model="form.address"
                                    placeholder="Rua, número, bairro, cidade"
                                    required
                                ></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Senha</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    v-model="form.password"
                                    placeholder="Mínimo 6 caracteres"
                                    required
                                    minlength="6"
                                />
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirmar senha</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    placeholder="Digite a senha novamente"
                                    required
                                />
                            </div>

                            <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>

                            <button
                                type="submit"
                                class="btn btn-primary w-100 py-2 fw-bold"
                                :disabled="loading"
                            >
                                {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
                            </button>
                            </form>
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
                name: '',
                email: '',
                phone: '',
                address: '',
                password: '',
                password_confirmation: '',
            },
            loading: false,
            error: null,
        };
    },
    methods: {
        async handleRegister() {
            if (this.form.password !== this.form.password_confirmation) {
                this.error = 'As senhas não coincidem.';
                return;
            }

            this.loading = true;
            this.error = null;

            try {
                const authStore = useAuthStore();
                await authStore.register(this.form);
                this.$router.push('/');
            } catch (err) {
                this.error = err.response?.data?.message || 'Erro ao cadastrar. Tente novamente.';
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.card {
    border-radius: 12px;
}
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
.btn-primary {
    background-color: #0d6efd;
    border: none;
    transition: 0.2s;
}
.btn-primary:hover {
    background-color: #0b5ed7;
}
</style>