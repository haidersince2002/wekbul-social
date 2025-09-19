FROM php:8.2-apache

# Enable Apache modules and PHP extensions
RUN a2enmod rewrite \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get update \
    && apt-get install -y --no-install-recommends libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && rm -rf /var/lib/apt/lists/*

# Copy application
COPY . /var/www/html/

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Ensure upload directories exist and permissions are sane
RUN mkdir -p /var/www/html/uploads/posts /var/www/html/uploads/profiles /var/www/html/auth/uploads/profiles \
    && chown -R www-data:www-data /var/www/html \
    && find /var/www/html/uploads -type d -exec chmod 775 {} \; \
    && find /var/www/html/uploads -type f -exec chmod 664 {} \; || true

EXPOSE 80

# Note: On Render we adjust Listen port via render.yaml dockerCommand
CMD ["apache2-foreground"]
