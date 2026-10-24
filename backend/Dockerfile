
FROM php:8.3-cli-bookworm

# Install library sistem dan ekstensi PHP untuk Laravel + MySQL
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        intl \
    && rm -rf /var/lib/apt/lists/*

# Ambil Composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

#  direktori kerja di dalam container
WORKDIR /var/www/html

# Salin dependency manifest terlebih dahulu agar cache optimal
COPY composer.json composer.lock ./

# Install dependencies Laravel tanpa paket development
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --optimize-autoloader

# Salin seluruh source code Laravel
COPY . .

# Jalankan script Laravel setelah source code tersedia
RUN composer dump-autoload --optimize \
    && mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Port aplikasi Laravel di dalam container
EXPOSE 8000

# Jalankan Laravel ketika container dimulai
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
