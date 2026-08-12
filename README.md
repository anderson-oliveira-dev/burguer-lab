<div align="center">
  <h1>🐳 Laravel Docker Base</h1>
  <p>
    <strong>Repositório base para projetos Laravel com ambiente Dockerizado</strong><br>
    Nginx + PHP-FPM + MySQL – pronto para desenvolvimento.
  </p>
  <p>
    <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white" alt="PHP 8.4">
    <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white" alt="MySQL">
    <img src="https://img.shields.io/badge/Docker-2496ED?style=flat&logo=docker&logoColor=white" alt="Docker">
    <img src="https://img.shields.io/badge/Nginx-1.27-009639?style=flat&logo=nginx&logoColor=white" alt="Nginx">
  </p>
  <p>
    <img src="https://img.shields.io/github/stars/anderson-oliveira-dev/laravel-docker-base?style=social" alt="Stars">
    <img src="https://img.shields.io/github/forks/anderson-oliveira-dev/laravel-docker-base?style=social" alt="Forks">
  </p>
</div>

---

## 📦 Tecnologias utilizadas

<ul>
  <li><strong>PHP 8.4</strong> (FPM)</li>
  <li><strong>Nginx</strong> (servidor web)</li>
  <li><strong>MySQL 8.0</strong></li>
  <li><strong>Docker</strong> e <strong>Docker Compose</strong></li>
  <li><strong>Laravel</strong> (última versão)</li>
</ul>

---

## ✅ Pré‑requisitos

Antes de começar, certifique‑se de ter instalado em sua máquina:

<ul>
  <li><a href="https://docs.docker.com/get-docker/" target="_blank">Docker</a> (com Docker Desktop ou Docker Engine)</li>
  <li><a href="https://docs.docker.com/compose/install/" target="_blank">Docker Compose</a> (já incluso no Docker Desktop)</li>
  <li><a href="https://git-scm.com/" target="_blank">Git</a></li>
  <li><a href="https://getcomposer.org/" target="_blank">Composer</a> (opcional, apenas para criar novos projetos)</li>
</ul>

---

## 🚀 Como usar

### 1. Clonar o repositório

```bash
git clone git@github.com:anderson-oliveira-dev/laravel-docker-base.git (nome-do-novo-projeto)
cd laravel-docker-base
```
Atenção, se for um projeto novo, remova o vínculo:
```bash
git remote remove origin
```
Ou troque a URL:
```bash
git remote set-url origin NOVA_URL
```

### 2. Configurar o ambiente

Copie o arquivo de exemplo de variáveis de ambiente:

```bash
cp .env.example .env
```

Edite o <code>.env</code> para definir as credenciais do banco. As configurações padrão já estão alinhadas com o <code>docker-compose.yml</code>:

```bash
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel_user
DB_PASSWORD=secret
```

Nota: Se você alterar as variáveis no .env, lembre‑se de ajustar também as variáveis de ambiente no serviço mysql do <code>docker-compose.yml</code> para manter a consistência.

### 3. Subir os containers

```bash
docker-compose up -d --build
```

Isso irá:

<ul>
    <li>Construir a imagem PHP com todas as extensões necessárias.</li>
    <li>Baixar as imagens do Nginx e MySQL.</li>
    <li>Subir os três containers em segundo plano.</li>
</ul>

### 4. Instalar as dependências do Laravel

```bash
docker-compose exec php composer install
```

### 5. Gerar a chave da aplicação

```bash
docker-compose exec php php artisan key:generate
```

### 6. Executar as migrações

```bash
docker-compose exec php php artisan migrate
```

### 7. Acessar a aplicação

Abra o navegador e acesse:
<a href="http://localhost:8080" target="_blank"><strong>http://localhost:8080</strong></a>

Você verá a página inicial do Laravel.

<hr>

### 🛠 Comandos úteis

<table>
    <tr>
        <th>Ação</th>
        <th>Comando</th>
    </tr>
    <tr>
        <td>Executar comandos Artisan</td>
        <td><code>docker-compose exec php php artisan <comando></code></td>
    </tr>
    <tr>
        <td>Executar o Composer</td>
        <td><code>docker-compose exec php composer (comando) </code></td>
    </tr>
    <tr>
        <td>Acessar o container PHP interativamente</td>
        <td><code>docker-compose exec php bash</code></td>
    </tr>
    <tr>
        <td>Acessar o MySQL via linha de comando</td>
        <td><code>docker-compose exec mysql mysql -u laravel_user -p</code> (senha <code>secret</code>)</td>
    </tr>
    <tr>
        <td>Parar os containers</td>
        <td><code>docker-compose down</code></td>
    </tr>
    <tr>
        <td>Parar e remover volumes (⚠️ apaga dados)</td>
        <td><code>docker-compose down -v</code></td>
    </tr>
    <tr>
        <td>Visualizar logs</td>
        <td><code>docker-compose logs -f</code></td>
    </tr>
    <tr>
        <td>Logs de um serviço específico</td>
        <td><code>docker-compose logs -f php</code> (ou <code>nginx</code>, <code>mysql</code>)</td>
    </tr>
</table>

<hr>

### 📁 Estrutura de diretórios relevante

```bash
laravel-docker-base/
├── docker/
│   ├── nginx/
│   │   └── default.conf       # Configuração do servidor Nginx
│   └── php/
│       └── Dockerfile          # Definição da imagem PHP com extensões
├── docker-compose.yml          # Orquestração dos containers
├── .env                        # Variáveis de ambiente (não versionado)
├── .env.example                # Modelo para o .env
└── ... (arquivos do Laravel)
```

<hr>

### 🔐 Credenciais padrão do banco

As credenciais definidas no <code>docker-compose.yml</code> são:

<table> 
    <tr> 
        <th>Variável</th>
        <th>Valor</th>
    </tr>
    <tr>
        <td><strong>Database</strong></td>
        <td><code>laravel</code></td>
    </tr>
    <tr>
        <td><strong>Usuário</strong></td>
        <td><code>laravel_user</code></td>
    </tr>
    <tr>
        <td><strong>Senha</strong></td>
        <td><code>secret</code></td>
    </tr>
    <tr>
        <td><strong>Root password</strong></td>
        <td><code>root</code></td>
    </tr>
    <tr>
        <td><strong>Host (interno)</strong></td>
        <td><code>mysql</code></td>
    </tr>
    <tr>
        <td><strong>Porta (interna)</strong></td>
        <td><code>3306</code></td>
    </tr>
</table>

A porta do MySQL mapeada para o host é <code><strong>3307</strong></code> (para evitar conflitos com um MySQL local).
Para acessar o banco de fora do container, use <code>localhost:3307</code> com as mesmas credenciais.

<hr>

### 🖥️ phpMyAdmin – Gerenciar o banco de dados

Este ambiente já inclui o **phpMyAdmin** para facilitar a visualização e administração do MySQL via interface web.

### Acesso

1. Com os containers em execução, abra o navegador e acesse:  
   **[http://localhost:8081](http://localhost:8081)**

2. Na tela de login, preencha os campos com as credenciais padrão:

| Campo       | Valor            |
|-------------|------------------|
| **Servidor**| `mysql`          |
| **Usuário** | `laravel_user`   |
| **Senha**   | `secret`         |

> 💡 Se preferir usar o usuário `root`, a senha é `root` – mas o usuário `laravel_user` já tem permissões suficientes para o dia a dia.

3. Clique em **"Ir"** ou **"Executar"**.

### Funcionalidades

- Visualizar tabelas, estruturas e dados.
- Executar consultas SQL diretamente.
- Exportar/importar bancos de dados (útil para backups).
- Gerenciar usuários e permissões (se necessário).

### Dica de segurança

Se você alterar as credenciais do MySQL no `docker-compose.yml` ou no `.env`, lembre‑se de atualizar também as variáveis de ambiente do phpMyAdmin (`PMA_HOST`, `PMA_PORT`, etc.) para manter a consistência.

### Parar ou remover o phpMyAdmin (opcional)

Caso não queira mais usar o phpMyAdmin, você pode removê‑lo do `docker-compose.yml` ou pará‑lo sem afetar os demais serviços:

```bash
docker-compose stop phpmyadmin
```

<hr>

### 🌿 Próximos passos – Branches para frontend

Este repositório foi criado para servir como base para outros projetos. Você pode criar branches separadas para adicionar diferentes frontends:

<ul> 
    <li><strong><code>feature/vue</code></strong> – Adicione o Vue.js via Laravel UI ou Inertia.</li>
    <li><strong><code>feature/react</code></strong> – Adicione o React.js via Laravel UI ou Inertia.</li>
</ul>

Exemplo para criar uma branch com Vue:

```bash
git checkout -b feature/vue
docker-compose exec php composer require laravel/ui
docker-compose exec php php artisan ui vue
npm install && npm run dev
# commit e push
```
<hr>

### 🤝 Contribuição

Sinta‑se à vontade para abrir <a href="https://github.com/anderson-oliveira-dev/laravel-docker-base/issues">issues</a> ou pull requests caso encontre problemas ou tenha sugestões de melhoria.

<div align="center"> <strong>Divirta‑se codando! 🚀</strong> </div>