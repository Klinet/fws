# Use the official PHP 8.3 CLI image as the base image
FROM php:8.3-cli

# Install system dependencies and PHP extensions
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    zip \
    xml \
    mbstring \
    pdo \
    pdo_mysql \
    mysqli \
    && rm -rf /var/lib/apt/lists/*

# Enable PHP extensions
RUN docker-php-ext-enable pdo_mysql mysqli zip

# Set PHP configuration values for memory limit, execution time, etc.
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/docker-php.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/docker-php.ini && \
    echo "upload_max_filesize = 50M" >> /usr/local/etc/php/conf.d/docker-php.ini && \
    echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/docker-php.ini && \
    echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/docker-php.ini

# Set working directory
WORKDIR /var/www/html

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create a user 'akali' and switch to this user
RUN useradd -m akali
USER akali

# Expose necessary port for the application (optional, you can expose ports if needed)
# EXPOSE 8080

# Keep the container running
CMD ["tail", "-f", "/dev/null"]
