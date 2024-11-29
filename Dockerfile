# Utiliser une image de base PHP
FROM php:8.1-cli

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Installer les dépendances PHP
RUN apt-get update && apt-get install -y libzip-dev && docker-php-ext-install zip

# Copier les fichiers du projet
COPY . /app

# Définir le répertoire de travail
WORKDIR /app

# Installer les dépendances avec Composer
RUN composer install --no-dev --optimize-autoloader

# Exposer le port pour le serveur
EXPOSE 8000

# Démarrer le serveur PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
