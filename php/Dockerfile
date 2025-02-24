FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip xml mbstring pdo pdo_mysql mysqli

# Enable PHP extensions
RUN echo "extension=pdo_mysql" > /usr/local/etc/php/conf.d/docker-php-ext-pdo_mysql.ini && \
    echo "extension=mysqli" > /usr/local/etc/php/conf.d/docker-php-ext-mysqli.ini && \
    echo "extension=zip" > /usr/local/etc/php/conf.d/docker-php-ext-zip.ini

# Set working directory
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Keep the container running
CMD ["tail", "-f", "/dev/null"]