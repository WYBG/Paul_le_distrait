# Utiliser une image PHP officielle
FROM php:8.1-cli

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y libzip-dev && docker-php-ext-install zip

# Copier le contenu du projet dans l'image Docker
COPY . /app

# Définir le répertoire de travail
WORKDIR /app

# Installer les dépendances avec Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install --no-dev --optimize-autoloader

# Exposer le port pour le serveur
EXPOSE 8000

# Démarrer le serveur PHP
CMD ["php", "-S", "0.0.0.0:8000"]
