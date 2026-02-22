# Use an official PHP image with Apache
FROM php:8.2-apache

# Update package lists and install necessary packages for SSL and PDO MySQL
RUN apt-get update && apt-get install -y openssl \
    && docker-php-ext-install pdo pdo_mysql

# Enable Apache modules for rewrite, SSL, and shared memory caching
RUN a2enmod rewrite ssl socache_shmcb

# Generate a self-signed SSL certificate for HTTPS
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=ES/ST=Local/L=Local/O=EasyPoint/CN=localhost"

# Copy the custom Apache configuration file to the container
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your project files to the container
COPY . /var/www/html/

# Expose ports 80 for HTTP and 443 for HTTPS
EXPOSE 80 443