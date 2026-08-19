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
        <div class="dropdown" ref="dropdownContainer" v-else>
            <a href="#" class="btn d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Olá, {{ user.name }} 👤
            </a>
            <ul class="dropdown-menu text-small" style="">
                <li><a class="dropdown-item" href="#">Configurações</a></li>
                <li><a class="dropdown-item" @click="$router.push('/profile')" href="#">Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" @click="handleLogout">Sair</a></li>
            </ul>
        </div>
    </div>
</template>

<script>
import { Button, Dropdown } from 'bootstrap';
import { useAuthStore } from '../stores/auth';
import { useModalStore } from '../stores/modal';

export default {
    data() {
        return {
            dropdownInstance: null
        };
    },
    mounted() {
        if (this.isAuthenticated) {
            this.$nextTick(() => {
                this.initDropdown();
            });
        }
    },
    computed: {
        isAuthenticated() {
            return useAuthStore().isAuthenticated;
        },

        user() {
            return useAuthStore().user;
        }
    },
    methods: {
        initDropdown() {
            const container = this.$refs.dropdownContainer;
            if (container) {
                if (this.dropdownInstance) {
                    this.dropdownInstance.dispose();
                }
                this.dropdownInstance = new Dropdown(container.querySelector('[data-bs-toggle="dropdown"]') || container);
            }
        },
        openLoginModal() {
            useModalStore().open('login');
        },
        async handleLogout() {
            await useAuthStore().logout();
        }
    },
    watch: {
        isAuthenticated(newVal) {
            if (newVal) {
                this.$nextTick(() => {
                    this.initDropdown();
                });
            }
        }
    },
};
</script>