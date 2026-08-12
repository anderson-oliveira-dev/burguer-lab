FROM php:8.4-fpm

# Instalar dependências do sistema e extensões do PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instale o Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto para dentro do container
COPY . .

# Ajustar permissões (importante para o Laravel)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Cria a pasta de cache do npm com permissão para www-data
RUN mkdir -p /var/www/.npm \
    && chown -R www-data:www-data /var/www/.npm

USER www-data

# Expor portas
EXPOSE 9000
EXPOSE 5173

CMD ["sh", "-c", "php-fpm -D && npm run dev -- --host"]