<template>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Alterar Senha</h5>
                        <form @submit.prevent="handlePasswordUpdate">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Senha atual</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="current_password"
                                    v-model="passwordForm.current_password"
                                    required
                                />
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nova senha</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="new_password"
                                    v-model="passwordForm.new_password"
                                    required
                                />
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirmar nova senha</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="new_password_confirmation"
                                    v-model="passwordForm.new_password_confirmation"
                                    required
                                />
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning" :disabled="passwordSaving">
                                    <span v-if="passwordSaving" class="spinner-border spinner-border-sm"></span>
                                    Atualizar senha
                                </button>
                                <button type="button" class="btn btn-secondary" @click="cancel">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { notifyError } from '../services/notify';
import { useAuthStore } from '../stores/auth';
import { mapActions } from 'pinia';

export default {
    data() {
        return {
            passwordForm: {
                current_password: '',
                new_password: '',
                new_password_confirmation: '',
            },
            passwordSaving: false,
        };
    },
    methods: {
        ...mapActions(useAuthStore, ['updatePassword']),

        cancel() {
            this.$router.push('/profile');
        },

        async handlePasswordUpdate() {
            this.passwordSaving = true;

            if (this.passwordForm.new_password !== this.passwordForm.new_password_confirmation) {
                notifyError('As senhas não coincidem.')
                this.passwordSaving = false;
                return;
            }

            try{
                const response = await this.updatePassword({
                    current_password: this.passwordForm.current_password,
                    new_password: this.passwordForm.new_password,
                    new_password_confirmation: this.passwordForm.new_password_confirmation,
                });

                if (response.success) {
                    this.passwordForm = {
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: '',
                    };
                    this.$router.push('/profile');
                }
            }finally{
                this.passwordSaving = false;
            }
        },
    },
};
</script>