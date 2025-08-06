FROM php:8.3-apache

# Ativa mod_rewrite (caso vá usar URLs amigáveis)
RUN a2enmod rewrite

# Instala dependências para pdo_pgsql e instala a extensão
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Copia tudo do diretório atual para /var/www/html
COPY . /var/www/html/

# Define permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80