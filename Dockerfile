FROM php:8.2-apache

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Définir le dossier de travail
WORKDIR /var/www/html