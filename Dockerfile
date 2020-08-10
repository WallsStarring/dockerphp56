# Base image
FROM php:5.6-apache

RUN mkdir /var/www/html/website
VOLUME web:/var/www/html/website

COPY ./conf/website.conf /etc/apache2/sites-available/website.conf
COPY ./conf/php.ini /usr/local/etc/php/
COPY web /var/www/html/website
RUN chmod -R 777 /var/www/html/website
# Setting ServerName to avoid "Could not reliably determine the server's fully qualified domain name, using 127.0.1.1 for ServerName" warning
RUN echo "ServerName localhost" | tee /etc/apache2/conf-available/servername.conf
RUN a2enconf servername

# Copying webapp/website and configuration files to their respective folders. Configuring Apache
RUN chown -R www-data:www-data /var/www/html/website \
    && a2dissite 000-default.conf \
    && a2ensite website.conf \
    && a2enmod rewrite \
    && service apache2 restart

# Installing PHP, extensions and other necessary packages

RUN apt-get update && apt-get install -y --no-install-recommends \
    libcurl4-openssl-dev \
    libedit-dev \
    libsqlite3-dev \
    libssl-dev \
    libxml2-dev \
    zlib1g-dev \
    freetds-dev \
    freetds-bin \
    freetds-common \
    libdbd-freetds \
    libsybdb5 \
    libqt4-sql-tds \
    libqt5sql5-tds \
    libqxmlrpc-dev \
    && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.so /usr/lib/libsybdb.so \
    && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.a /usr/lib/libsybdb.a \
    && docker-php-ext-install   mssql \
    && docker-php-ext-configure mssql \
    && chmod 755 /var/www/html -R \
    && chown www-data:www-data /var/www/html
COPY conf/php.ini /usr/local/etc/php/
COPY conf.d/ /usr/local/etc/php/conf.d/

# Exposing web ports
EXPOSE 80 81 443

CMD apachectl -D FOREGROUND
