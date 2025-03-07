# First stage: Composer installation
FROM composer:latest as composer

# Set the working directory in the Composer container
WORKDIR /app

# Copy the composer.json and composer.lock files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Optimize the autoloader
RUN composer dump-autoload --optimize

# Second stage: Apache + PHP setup
FROM php:8.2.0-apache

# Set the working directory
WORKDIR /var/www/html

# Copy the application source
COPY . /var/www/html

# copy default files in /public/img so they can be copied if needed in entrypoint
RUN mkdir /tmp/img
RUN cp /var/www/html/public/img/* /tmp/img
VOLUME ["/var/www/html/public/img"]

# Create volume for logs
VOLUME ["/var/www/html/app/logs"]

# Copy the PHP overrides
COPY ./99-php.ini /usr/local/etc/php/conf.d/

# Copy the Composer dependencies from the first stage
COPY --from=composer /app/vendor/ /var/www/html/vendor/

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    default-mysql-client && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Configure and install GD
RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-install gd

# Enable Apache mod_rewrite for .htaccess support
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Copy docker-entrypoint.sh into the container
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set the script as the entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

# Start Apache server (CMD)
CMD ["apache2-foreground"]
