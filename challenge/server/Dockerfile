FROM php:7.1-apache
ENV TZ 'Europe/Paris'
RUN echo $TZ > /etc/timezone && \
    apt-get update && apt-get install -y tzdata libpq-dev libapache2-mod-security2 curl git libzip-dev && \
    rm /etc/localtime && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    apt-get clean && \
	echo "date.timezone = Europe/Paris" > /usr/local/etc/php/php.ini && \
	docker-php-ext-install -j$(nproc) pdo pdo_pgsql mbstring gettext zip
WORKDIR /var/www/html
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');"
ADD composer.json .
RUN a2enmod rewrite && \
    a2enmod dump_io && \
    a2enmod security2 && \
    a2enmod headers && \ 
    php composer.phar install
ADD apache2.conf /etc/apache2/apache2.conf
ADD ports.conf /etc/apache2/ports.conf
RUN service apache2 restart
ADD robots.txt .
ADD index.php .
ADD html html
ADD cu cu
ADD config.json .
RUN touch /var/log/apache2/post.log && chmod 777 /var/log/apache2/post.log
EXPOSE 6007
