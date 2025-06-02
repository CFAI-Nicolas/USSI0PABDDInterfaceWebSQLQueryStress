FROM php:8.2-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    gnupg2 curl apt-transport-https unzip git libicu-dev libxml2-dev libzip-dev libonig-dev libpq-dev unixodbc-dev

# Supprimer les paquets qui posent conflit
RUN apt-get remove -y libodbc2 libodbccr2 libodbcinst2 unixodbc-common || true

# Ajouter le dépôt Microsoft proprement (clé dans trusted.gpg.d)
RUN curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > /etc/apt/trusted.gpg.d/microsoft.gpg \
    && echo "deb [arch=amd64] https://packages.microsoft.com/debian/11/prod bullseye main" > /etc/apt/sources.list.d/mssql-release.list

# Installer ODBC driver SQL Server
RUN apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite
RUN a2enmod rewrite

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Copier le code Symfony
COPY . /var/www/html
WORKDIR /var/www/html

# Installer les dépendances PHP (ignorer erreurs initiales)
RUN composer install --no-interaction --no-plugins --no-scripts || true

# Fixer les permissions
RUN chown -R www-data:www-data /var/www/html

# Définir le dossier public comme racine Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

