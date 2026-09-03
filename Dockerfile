FROM php:8.3-apache

# Install dependencies yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    nodejs \
    npm

# Hapus cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP (termasuk pdo_pgsql untuk Neon/Supabase, pdo_mysql untuk MySQL)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Aktifkan mod_rewrite Apache (Penting untuk routing Laravel)
RUN a2enmod rewrite

# Ubah Document Root Apache ke folder public/ Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy file project ke dalam container
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install package PHP via Composer (tanpa package dev)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Build aset frontend (Vite)
RUN npm install
RUN npm run build

# Beri izin akses (permission) ke folder storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Buka port 8000 untuk Koyeb
RUN echo "Listen 8000" >> /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:8000>/g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8000
