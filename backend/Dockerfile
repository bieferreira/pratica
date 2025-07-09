FROM php:8.1-apache

# Instala extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Copia os arquivos para dentro do container
COPY . /var/www/html/
