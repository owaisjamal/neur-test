# Stage 1: Composer Install
FROM composer:2.0 AS composer
WORKDIR /var/www/neur-test
COPY composer.json composer.lock /var/www/neur-test/
RUN composer install --no-interaction --ignore-platform-reqs

# Stage 2: Final Image
FROM php:8.0-fpm
WORKDIR /var/www/neur-test

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd soap

# Copy from the composer stage
COPY --from=composer /var/www/neur-test/vendor/ /var/www/neur-test/vendor/

# Copy the rest of the application files
COPY . /var/www/neur-test

# Install Node.js and npm
RUN apt-get install -y nodejs npm

# Install Vue.js dependencies
RUN npm install

# Build the Vue.js app
RUN npm run dev

# Set folder permissions
RUN chown -R www-data:www-data /var/www/neur-test/storage /var/www/neur-test/bootstrap

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
