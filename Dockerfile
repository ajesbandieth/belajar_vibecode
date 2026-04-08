FROM php:5.6-apache

# Install extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite

# Increase PHP upload and post limits
RUN echo "upload_max_filesize = 512M\npost_max_size = 512M" > /usr/local/etc/php/conf.d/uploads.ini

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80
