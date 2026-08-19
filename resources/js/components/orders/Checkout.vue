<template>
    <div class="container py-4">
        <h1 class="mb-4">Finalizar pedido</h1>

        <div v-if="cartStore.loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

        <div v-else-if="!cartStore.items.length" class="text-center py-5">
            <h3>Seu carrinho está vazio</h3>
            <router-link to="/" class="btn btn-primary">Voltar às compras</router-link>
        </div>

        <div v-else>
            <div class="row">
                <div class="col-lg-6">
                    <h4 class="mb-3">Itens do pedido</h4>
                    <div class="list-group">
                        <div v-for="item in cartStore.items" :key="item.id" class="list-group-item">
                            <div class="d-flex align-items-start gap-3">
                                <img
                                    v-if="item.product?.image"
                                    :src="item.product.image"
                                    alt="Produto"
                                    class="img-thumbnail"
                                    style="width: 70px; height: 70px; object-fit: cover;"
                                />
                                <div v-else class="bg-light img-thumbnail d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                    <span class="text-muted small">Sem foto</span>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ item.product?.name || 'Produto' }}</strong>
                                        <span class="fw-bold text-success">
                                            R$ {{ calculateItemTotal(item).toFixed(2) }}
                                        </span>
                                    </div>

                                    <div v-if="item.extras && item.extras.length" class="mt-1">
                                        <span
                                            v-for="extra in item.extras"
                                            :key="extra.id"
                                            class="badge bg-secondary me-1"
                                        >
                                            {{ extra.name }} (+ R$ {{ Number(extra.price).toFixed(2) }})
                                        </span>
                                    </div>
                                    <span v-else class="text-muted small">Sem extras</span>

                                    <div class="text-muted small mt-1">
                                        Quantidade: {{ item.quantity }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>R$ {{ cartStore.total.toFixed(2) }}</span>
                        </div>
                        <div v-if="deliveryFee > 0" class="d-flex justify-content-between">
                            <span>Taxa de entrega:</span>
                            <span>R$ {{ deliveryFee.toFixed(2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span class="text-success">R$ {{ totalWithDelivery.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <!-- ETAPA 1: Formulário do pedido -->
                    <div v-if="step === 1">
                        <h4>Dados do pedido</h4>
                        <form @submit.prevent="handleStep1Submit">
                            <div class="mb-3">
                                <label class="form-label">Tipo de pedido</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" id="delivery" value="delivery" v-model="form.type" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="delivery">Entrega</label>

                                    <input type="radio" class="btn-check" id="pickup" value="pickup" v-model="form.type" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="pickup">Retirada</label>
                                </div>
                            </div>

                            <div class="mb-3" v-if="form.type === 'delivery'">
                                <label for="address" class="form-label">Endereço de entrega</label>
                                <input type="text" class="form-control" id="address" v-model="form.address" placeholder="Rua, número, bairro" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone para contato</label>
                                <input type="text" class="form-control" id="phone" v-model="form.phone" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Forma de pagamento</label>
                                <select class="form-select" v-model="form.payment_method" required>
                                    <option value="cash">Dinheiro</option>
                                    <option value="card">Cartão</option>
                                    <option value="pix">PIX</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="observations" class="form-label">Observações (opcional)</label>
                                <textarea class="form-control" id="observations" rows="3" v-model="form.observations" placeholder="Ex: tirar cebola, ponto da carne"></textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <router-link to="/cart" class="btn btn-outline-secondary">Voltar ao carrinho</router-link>
                                <button type="submit" class="btn btn-success btn-lg" :disabled="submitting">
                                    <span v-if="submitting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    {{ authStore.isAuthenticated ? 'Finalizar pedido' : 'Avançar' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- ETAPA 2: Cadastro (somente para não logados) -->
                    <div v-else-if="step === 2 && !authStore.isAuthenticated">
                        <h4>Crie sua conta</h4>
                        <p class="text-muted small">Preencha os dados abaixo para finalizar o pedido.</p>

                        <form @submit.prevent="handleStep2Submit">
                            <div class="mb-2">
                                <label class="form-label">Nome completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="registerForm.name" placeholder="Seu nome completo" required />
                            </div>

                            <div class="mb-2">
                                <label class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" v-model="registerForm.email" placeholder="seu@email.com" required />
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" v-model="registerForm.password" placeholder="Mínimo 6 caracteres" required minlength="6" />
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Confirmar senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" v-model="registerForm.password_confirmation" placeholder="Digite a senha novamente" required />
                            </div>

                            <div class="mb-2 form-check">
                                <input type="checkbox" class="form-check-input" id="agreeCheckbox" v-model="registerForm.agree" />
                                <label class="form-check-label" for="agreeCheckbox">
                                    Concordo em criar uma conta para finalizar o pedido <span class="text-danger">*</span>
                                </label>
                            </div>

                            <div v-if="registerError" class="alert alert-danger mt-2">
                                {{ registerError }}
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <button type="button" class="btn btn-outline-secondary" @click="step = 1">Voltar</button>
                                <button type="submit" class="btn btn-success btn-lg" :disabled="submitting">
                                    <span v-if="submitting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Finalizar pedido
                                </button>
                            </div>

                            <div class="mt-3 text-center">
                                <button type="button" class="btn btn-link" @click="openLoginModal">
                                    Já tenho conta? Faça login
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useCartStore } from '../stores/cartStore';
import { useAuthStore } from '../stores/auth';
import { useOrderStore } from '../stores/orderStore';
import { useModalStore } from '../stores/modal';

export default {
    data() {
        return {
            step: 1,
            submitting: false,
            form: {
                type: 'delivery',
                address: '',
                phone: '',
                payment_method: 'cash',
                observations: '',
            },
            registerForm: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                agree: false,
            },
            registerError: null,
        };
    },
    computed: {
        cartStore() {
            return useCartStore();
        },
        authStore() {
            return useAuthStore();
        },
        deliveryFee() {
            return this.form.type === 'delivery' ? 5 : 0;
        },
        totalWithDelivery() {
            return this.cartStore.total + this.deliveryFee;
        },
    },
    watch: {
        'authStore.isAuthenticated': function(newVal, oldVal) {
            if (newVal && !oldVal) {
                this.step = 1;
                this.registerError = null;
                this.registerForm = { name: '', email: '', password: '', password_confirmation: '', agree: false };
            }
        }
    },
    created() {
        const user = this.authStore.user;
        if (user) {
            this.form.phone = user.phone || '';
            this.form.address = user.address || '';
        }
    },
    methods: {
        calculateItemTotal(item) {
            const extrasTotal = (item.extras || []).reduce((sum, extra) => sum + (extra.price || 0), 0);
            return (Number(item.unit_price) + extrasTotal) * item.quantity;
        },

        openLoginModal() {
            useModalStore().open('login');
        },

        handleStep1Submit() {
            if (this.authStore.isAuthenticated) {
                this.submitOrder();
            } else {
                this.step = 2;
            }
        },

        async handleStep2Submit() {
            this.submitting = true;
            this.registerError = null;

            try {
                const { name, email, password, password_confirmation, agree } = this.registerForm;

                if (!name || !email || !password || !password_confirmation) {
                    throw new Error('Preencha todos os campos de cadastro.');
                }
                if (password.length < 6) {
                    throw new Error('A senha deve ter pelo menos 6 caracteres.');
                }
                if (password !== password_confirmation) {
                    throw new Error('As senhas não coincidem.');
                }
                if (!agree) {
                    throw new Error('Você deve concordar em criar uma conta para finalizar o pedido.');
                }

                await this.authStore.register({
                    name,
                    email,
                    password,
                    password_confirmation,
                });

                await this.submitOrder();
            } catch (error) {
                if (error.response?.data?.errors) {
                    const messages = Object.values(error.response.data.errors).flat();
                    this.registerError = messages.join(' ');
                } else if (error.response?.data?.message) {
                    this.registerError = error.response.data.message;
                } else {
                    this.registerError = error.message || 'Erro ao finalizar pedido. Tente novamente.';
                }
            } finally {
                this.submitting = false;
            }
        },

        async submitOrder() {
            this.submitting = true;
            try {
                const payload = {
                    type: this.form.type,
                    address: this.form.type === 'delivery' ? this.form.address : null,
                    phone: this.form.phone,
                    payment_method: this.form.payment_method,
                    observations: this.form.observations,
                    delivery_fee: this.deliveryFee,
                };

                const orderStore = useOrderStore();
                await orderStore.createOrder(payload);

                this.cartStore.items = [];
                this.cartStore.total = 0;
                this.$router.push({ name: 'orders' });
            } catch (error) {
                throw error;
            } finally {
                this.submitting = false;
            }
        },
    },
};
</script>