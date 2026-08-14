<div align="center">
  <h1>🍔 Burguer Delivery System</h1>
  <p>
    <strong>Sistema de delivery de hambúrgueres com gestão de pedidos, estoque e painel administrativo</strong><br>
    <em style="color: #e67e22;">🚧 Em desenvolvimento – versão inicial</em>
  </p>
  <p>
    <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white" alt="PHP 8.4">
    <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js&logoColor=white" alt="Vue 3">
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white" alt="MySQL">
    <img src="https://img.shields.io/badge/Docker-2496ED?style=flat&logo=docker&logoColor=white" alt="Docker">
    <img src="https://img.shields.io/badge/Pinia-2.x-FFD859?style=flat&logo=vue.js&logoColor=white" alt="Pinia">
  </p>
</div>

---

## 📋 Sobre o projeto

Sistema completo para uma hamburgueria, com:

- Catálogo de produtos (hambúrgueres, combos, bebidas e acompanhamentos)
- Personalização de hambúrgueres com extras (carne extra, bacon, etc.) com cálculo de preço dinâmico
- Carrinho persistente (localStorage + banco)
- Checkout com opções de retirada ou entrega
- Acompanhamento de pedido em tempo real (status)
- Painel administrativo (cozinha/gerência) com:
  - CRUD de produtos, ingredientes e combos
  - Gestão de estoque (ativar/desativar)
  - Fila de pedidos com atualização de status
  - Relatórios de vendas e produtos mais vendidos

**Público-alvo:** Donos de hamburguerias que desejam digitalizar o processo de pedidos e melhorar a experiência do cliente.

---

## 🚀 Tecnologias utilizadas

| Frontend           | Backend            | Infraestrutura    |
|--------------------|--------------------|-------------------|
| Vue 3 (Composition) | Laravel 13.x       | Docker            |
| Pinia (estado)      | PHP 8.4            | Nginx 1.27        |
| Vue Router          | MySQL 8.0          | PHP-FPM           |
| Axios               | Laravel Storage    | Docker Compose    |
| (HTML/CSS/JS)       | Intervention Image |                   |

---

## ⚙️ Funcionalidades (em desenvolvimento)

### 👤 Visitante
- Navega pelo catálogo e vê produtos (hambúrgueres, combos, bebidas)
- Adiciona itens ao carrinho (persistido localmente)
- Ao finalizar, é direcionado para cadastro/login
- Após cadastro, o pedido é criado com status **Aguardando Confirmação**
- Mensagem: *"Seu pedido foi recebido! Em breve entraremos em contato pelo telefone para confirmar os dados e o valor total."*

### 👥 Cliente logado
- Login com e-mail e senha
- Merge automático do carrinho da sessão com o carrinho do banco
- Dados cadastrais (nome, telefone, endereço) já preenchidos no checkout
- Acompanhamento do pedido com status em tempo real

### 🧑‍🍳 Admin / Cozinha
- **Produtos:** CRUD completo, upload de imagens (redimensionadas com Intervention)
- **Estoque:** Ativar/desativar produtos (esgotados aparecem no final da lista com aviso)
- **Pedidos:** Lista filtrada por status, com botões para avançar:
  - Confirmar Pedido (→ Preparando)
  - Preparar / Finalizar (→ Pronto/Saiu para entrega)
  - Entregar / Retirado (→ Entregue/Retirado)
  - Cancelar
- **Relatórios:** Produtos mais vendidos e total recebido (filtrado por período)

---

## 🗄️ Modelo de dados (resumo)

```sql
users
  - id, name, email, password, phone, address, type (client/admin), status

categories
  - id, name (Tradicional, Combo, Bebida, Acompanhamento)

products
  - id, name, image, status (disponível, esgotado, oculto), price, category_id, description, preparation_time

extras
  - id, name, price, image

product_extra (pivot)
  - product_id, extra_id

orders
  - id, user_id, type (entrega/retirada), address, total_price, payment_method, status, created_at

order_items
  - id, order_id, product_id, quantity, unit_price, extras (JSON)

carts
  - id, user_id, items (JSON), updated_at