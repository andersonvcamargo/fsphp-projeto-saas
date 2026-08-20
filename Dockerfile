FROM php:8.2-apache

# Instala a extensão pdo_mysql
RUN docker-php-ext-install pdo_mysql

# Habilita o mod_rewrite do Apache (necessário para o .htaccess funcionar)
RUN a2enmod rewrite

# Habilita o mod_headers do Apache (necessário para as diretivas "Header" no .htaccess)
RUN a2enmod headers

# Habilita mod_expires e mod_deflate (usados no .htaccess para cache e compressão)
RUN a2enmod expires
RUN a2enmod deflate

# Permite que o .htaccess sobrescreva as configurações do Apache
RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf