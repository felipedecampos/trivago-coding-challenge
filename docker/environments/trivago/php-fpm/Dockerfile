FROM phpdockerio/php72-fpm:latest

WORKDIR "/var/www"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -q -y install --no-install-recommends ca-certificates curl acl sudo vim \
    php7.2-mbstring php-xdebug php7.2-sockets php7.2-pgsql php7.2-pdo-pgsql php7.2-redis \
    php7.2-xml php7.2-json php7.2-gd php7.2-curl

RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create a user to use composer
RUN adduser --disabled-password --gecos "" docker

# install from nodesource using apt-get
# https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-an-ubuntu-14-04-server
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - && \
    apt-get install -yq nodejs

RUN apt-get install -yq gcc g++ make

# fix npm - not the latest version installed by apt-get
RUN npm install -g npm
