# Use PHP with Apache
FROM php:7.4-apache

ENV IN_DOCKER='yes'

# Install mysqli and pdo_mysql extensions
RUN docker-php-ext-install mysqli pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite


# Expose port 8080
EXPOSE 8080
