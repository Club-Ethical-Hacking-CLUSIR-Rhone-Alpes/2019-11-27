FROM php:7.1-apache
ENV TZ 'Europe/Paris'
RUN echo $TZ > /etc/timezone && \
    apt-get update && apt-get install -y tzdata libpq-dev libapache2-mod-security2 curl git && \
    rm /etc/localtime && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    apt-get clean && \
	echo "date.timezone = Europe/Paris" > /usr/local/etc/php/php.ini && \
	docker-php-ext-install -j$(nproc) pdo pdo_pgsql mbstring gettext
WORKDIR /var/www/html
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');"
ADD composer.json .
RUN a2enmod rewrite && \
    a2enmod security2 && \
    a2enmod headers && \ 
    php composer.phar install
ADD apache2.conf /etc/apache2/apache2.conf
ADD ports.conf /etc/apache2/ports.conf
RUN service apache2 restart
ADD robots.txt .
ADD index.php .
ADD html html
ADD config.json .
EXPOSE 6007