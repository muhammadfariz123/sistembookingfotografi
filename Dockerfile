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

# Konfigurasi dan install ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_pgsql zip gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Pindahkan file project ke dalam sistem
WORKDIR /app
COPY . .

# Install dependency Laravel & Build Frontend
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Buat link storage & Berikan izin akses folder storage
RUN php artisan storage:link
RUN chmod -R 777 storage bootstrap/cache

# Buat file konfigurasi custom untuk PHP agar batas upload foto besar
RUN echo "post_max_size = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Perintah otomatis saat server menyala (Trik || true agar anti-crash saat tabrakan Render)
CMD bash -c "(php artisan migrate:fresh --force && php artisan db:seed --class=AdminSeeder --force || true) && php -S 0.0.0.0:$PORT -t public"