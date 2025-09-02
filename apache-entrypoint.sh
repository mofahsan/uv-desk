#!/bin/bash

# Export environment variables for Apache/PHP
export APP_ENV=dev

# Update the .env file with the correct database URL
if [ -f /var/www/html/.env ]; then
    sed -i "s|DATABASE_URL=.*|DATABASE_URL=mysql://uvdesk:uvdesk@uvdesk_mysql:3306/uvdesk|g" /var/www/html/.env
fi
chmod -R 777 /var/www/html #please add appropriate permission later to allow uvdesk to update config and .env files

RUN cd /var/www/html/ && composer install --optimize-autoloader
# Start Apache
exec apache2ctl -D FOREGROUND