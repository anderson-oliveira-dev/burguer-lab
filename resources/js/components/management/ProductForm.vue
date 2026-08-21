<template>
    <div class="container py-4">
        <h1 class="mb-4">{{ isEdit ? '✏️ Editar produto' : '➕ Adicionar produto' }}</h1>

        <div v-if="loading" class="alert alert-info">Carregando...</div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <form @submit.prevent="submit" v-else>
            <div class="mb-3">
                <label for="name" class="form-label">Nome *</label>
                <input
                    type="text"
                    id="name"
                    class="form-control"
                    v-model="form.name"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <textarea
                    id="description"
                    class="form-control"
                    rows="3"
                    v-model="form.description"
                ></textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Preço (R$) *</label>
                <input
                    type="number"
                    id="price"
                    class="form-control"
                    step="0.01"
                    min="0"
                    v-model.number="form.price"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Categoria</label>
                <select
                    id="category"
                    class="form-select"
                    v-model="form.category"
                >
                    <option value="">Selecione uma categoria</option>
                    <option
                        v-for="cat in categories"
                        :key="cat.id"
                        :value="cat.name"
                    >
                        {{ cat.name }}
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">URL da imagem</label>
                <input
                    type="url"
                    id="image"
                    class="form-control"
                    v-model="form.image"
                    placeholder="https://exemplo.com/imagem.jpg"
                />
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" :disabled="saving">
                    {{ saving ? 'Salvando...' : 'Salvar' }}
                </button>
                <button type="button" class="btn btn-secondary" @click="cancel">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { useProductStore } from '../stores/productStore';
import axios from 'axios';

export default {
    props: {
        id: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            form: {
                name: '',
                description: '',
                price: 0,
                category: '',
                image: '',
            },
            saving: false,
            error: null,
            loading: false,
            categories: [], // lista de categorias
        };
    },
    computed: {
        isEdit() {
            return !!this.id;
        },
    },
    methods: {
        async fetchCategories() {
            try {
                const response = await axios.get('/api/categories');
                this.categories = response.data || [];
            } catch (err) {
                console.error('Erro ao carregar categorias:', err);
            }
        },

        async loadProduct() {
            if (!this.id) return;
            this.loading = true;
            this.error = null;
            try {
                const productStore = useProductStore();
                const product = await productStore.fetchProductById(this.id);
                this.form = {
                    name: product.name || '',
                    description: product.description || '',
                    price: product.price || 0,
                    category: product.category || '',
                    image: product.image || '',
                };
            } catch (err) {
                this.error = 'Erro ao carregar produto. Tente novamente.';
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        async submit() {
            this.saving = true;
            this.error = null;
            try {
                const productStore = useProductStore();
                if (this.isEdit) {
                    await productStore.updateProduct(this.id, this.form);
                } else {
                    await productStore.createProduct(this.form);
                }
                this.$router.push('/');
            } catch (err) {
                this.error = 'Erro ao salvar produto. Verifique os dados.';
                console.error(err);
            } finally {
                this.saving = false;
            }
        },

        cancel() {
            this.$router.push('/');
        },
    },
    mounted() {
        this.fetchCategories();
        this.loadProduct();
    },
};
</script>