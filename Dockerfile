FROM php:8.3-apache

# Ativa mod_rewrite (caso vá usar URLs amigáveis)
RUN a2enmod rewrite

# Copia tudo do diretório atual (raiz do projeto) para /var/www/html
COPY . /var/www/html/

# Define permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80