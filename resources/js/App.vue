<template>
    <div id="welcome">
        <div class="container">
            <h1>🚀 Bem‑vindo ao seu projeto Laravel + Vue 3!</h1>
            <p class="subtitle">
                Este é o seu ambiente de desenvolvimento com Docker, Vite e Vue 3.
                Tudo está pronto para você construir sua aplicação.
            </p>

            <div class="card">
                <h2>Contador reativo</h2>
                <p>Clique nos botões para ver o Vue 3 em ação:</p>
                <div class="counter">
                <button @click="decrement">−</button>
                <span class="count">{{ count }}</span>
                <button @click="increment">+</button>
                </div>
                <p v-if="count > 5" class="highlight">⚡ Você já passou de {{ count - (count % 5) }}!</p>
            </div>

            <div class="footer">
                <p>Edit <code>resources/js/App.vue</code> para personalizar esta tela.</p>
                <p>🔧 Hot‑reload ativo – altere e veja as mudanças instantaneamente.</p>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'App',
    data() {
        return {
            count: 0
        }
    },
    methods: {
        increment() {
            this.count++
        },
        decrement() {
            if (this.count > 0) this.count--
        }
    }
}
</script>
<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

#welcome {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(145deg, #42d392 0%, #647eff 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.container {
    max-width: 700px;
    background: white;
    border-radius: 32px;
    padding: 48px 40px;
    box-shadow: 0 20px 60px rgba(0, 20, 30, 0.15);
    text-align: center;
}

h1 {
    font-size: 2.4rem;
    color: #1a2b3c;
    margin-bottom: 16px;
    font-weight: 700;
}

.subtitle {
    font-size: 1.2rem;
    color: #3d5a73;
    line-height: 1.6;
    margin-bottom: 32px;
}

.card {
    background: #f0f5fa;
    border-radius: 24px;
    padding: 32px 24px;
    margin: 24px 0;
}

.card h2 {
    font-size: 1.6rem;
    color: #1a2b3c;
    margin-bottom: 8px;
}

.card p {
    color: #2c4b5e;
    font-size: 1rem;
}

.counter {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin: 20px 0;
}

.counter button {
    background: #1a73e8;
    color: white;
    border: none;
    border-radius: 50%;
    width: 56px;
    height: 56px;
    font-size: 2rem;
    font-weight: 300;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    box-shadow: 0 4px 10px rgba(26, 115, 232, 0.3);
}

.counter button:hover {
    background: #1557b0;
}

.counter button:active {
    transform: scale(0.94);
}

.count {
    font-size: 3.2rem;
    font-weight: 600;
    color: #1a2b3c;
    min-width: 70px;
}

.highlight {
    color: #d45a2e;
    font-weight: 600;
    margin-top: 8px;
    animation: pulse 1s ease-in-out infinite alternate;
}

@keyframes pulse {
    from { opacity: 0.7; }
    to { opacity: 1; }
}

.footer {
    margin-top: 32px;
    font-size: 0.95rem;
    color: #5e7a8c;
    border-top: 1px solid #dce6ed;
    padding-top: 24px;
}

.footer code {
    background: #eef2f6;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.9rem;
    color: #1a2b3c;
}
</style>