FROM php:8.2-cli

# Install alat yang dibutuhkan Laravel, PostgreSQL, dan ekstensi GD
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm

# Konfigurasi dan install ekstensi PHP (termasuk pdo, zip, dan gd)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_pgsql zip gd

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
CMD bash -c "php artisan migrate:fresh --force && php artisan db:seed --class=AdminSeeder --force && php -S 0.0.0.0:$PORT -t public"