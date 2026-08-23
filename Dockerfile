FROM php:8.2-cli

# Install alat yang dibutuhkan Laravel & PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm

# Install ekstensi PHP untuk PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Pindahkan file project ke dalam sistem
WORKDIR /app
COPY . .

# Install dependency Laravel & Build Frontend (Tailwind/Vite)
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Buat link storage untuk foto
RUN php artisan storage:link

# Perintah otomatis saat server menyala
CMD bash -c "php artisan migrate --force && php -S 0.0.0.0:$PORT -t public"