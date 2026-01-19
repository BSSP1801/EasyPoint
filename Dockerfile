# Use an official PHP image with Apache
FROM php:8.2-apache

# Install PDO MySQL extension for database connection (Requirement: DWES)
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache rewrite module (Useful for MVC routing)
RUN a2enmod rewrite

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your project files to the container
COPY . /var/www/html/

# Expose port 80
EXPOSE 80