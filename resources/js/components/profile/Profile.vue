<template>
    <div class="container py-4">
        <h1 class="mb-4">👤 Meu Perfil</h1>

        <div v-if="!isAuthenticated">
            <LoginPanel />
        </div>

        <div v-else>
            <div v-if="loading" class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>

            <div v-else>
                <div v-if="!editMode" class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nome:</strong> {{ user.name }}</p>
                                <p><strong>E-mail:</strong> {{ user.email }}</p>
                                <p><strong>Telefone:</strong> {{ user.phone || 'Não informado' }}</p>
                                <p><strong>Endereço:</strong> {{ user.address || 'Não informado' }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-primary me-2" @click="enableEdit">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                                <router-link to="/profile/change-password" class="btn btn-outline-warning">
                                    <i class="bi bi-key"></i> Alterar Senha
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="card">
                    <div class="card-body">
                        <form @submit.prevent="handleUpdate">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="name" v-model="form.name" required />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" v-model="form.email" required />
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="phone" v-model="form.phone" />
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="address" v-model="form.address" />
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success" :disabled="saving">
                                    <span v-if="saving" class="spinner-border spinner-border-sm"></span>
                                    Salvar
                                </button>
                                <button type="button" class="btn btn-secondary" @click="cancelEdit">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoginPanel from '../common/LoginPanel.vue';
import { useAuthStore } from '../stores/auth';
import { mapState, mapActions } from 'pinia';

export default {
    components: { LoginPanel },
    data() {
        return {
            editMode: false,
            saving: false,
            loading: false,
            form: {
                name: '',
                email: '',
                phone: '',
                address: '',
            },
        };
    },
    computed: {
        ...mapState(useAuthStore, ['user', 'isAuthenticated']),
    },
    watch: {
        user: {
            immediate: true,
            handler(newUser) {
                if (newUser) {
                    this.form = {
                        name: newUser.name || '',
                        email: newUser.email || '',
                        phone: newUser.phone || '',
                        address: newUser.address || '',
                    };
                }
            },
        },
    },
    methods: {
        ...mapActions(useAuthStore, ['updateUser', 'fetchUser']),

        enableEdit() {
            this.editMode = true;
        },

        cancelEdit() {
            this.editMode = false;
            if (this.user) {
                this.form = { ...this.user };
            }
        },

        async handleUpdate() {
            this.saving = true;

            try{
                const response = await this.updateUser(this.form);

                if (response.success) {
                    this.editMode = false;
                    await this.fetchUser();
                }
            }finally{
                this.saving = false;
            }
        },
    },
    created() {
        if (this.user) {
            this.form = { ...this.user };
        }
    },
};
</script>