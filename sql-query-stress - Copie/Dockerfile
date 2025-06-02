FROM php:8.2-apache

# Install requirements
RUN apt-get update && apt-get install -y \
    gnupg2 curl apt-transport-https unzip git libicu-dev libxml2-dev libzip-dev libonig-dev libpq-dev unixodbc-dev gcc g++ make autoconf pkg-config

# Remove conflicting odbc packages
RUN apt-get remove -y libodbc2 libodbccr2 libodbcinst2 unixodbc-common || true

# Add Microsoft repository
RUN curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > /etc/apt/trusted.gpg.d/microsoft.gpg \
    && echo "deb [arch=amd64] https://packages.microsoft.com/debian/11/prod bullseye main" > /etc/apt/sources.list.d/mssql-release.list

# Install SQL Server ODBC driver
RUN apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18 unixodbc-dev \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set DocumentRoot to Symfony /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Enable .htaccess override
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Copy code
COPY . /var/www/html
WORKDIR /var/www/html

COPY php.ini /usr/local/etc/php/conf.d/custom.ini


# Composer install
RUN composer install --no-interaction --no-plugins --no-scripts || true

# Permissions
RUN chown -R www-data:www-data /var/www/html
