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

# Utiliser l'image officielle de PostgreSQL avec PostGIS
FROM postgis/postgis:latest

# Configurer le volume pour persister la base de données
VOLUME /var/lib/postgresql/data

# Définir l'utilisateur et le mot de passe par défaut pour la base de données
ENV POSTGRES_USER=postgres
ENV POSTGRES_PASSWORD=postgres
ENV POSTGRES_DB=escape_game

# Exposer le port par défaut de PostgreSQL
EXPOSE 5434

# Exposer le port pour le serveur
EXPOSE 8000

# Démarrer le serveur PHP
CMD ["php", "-S", "0.0.0.0:8000"]
