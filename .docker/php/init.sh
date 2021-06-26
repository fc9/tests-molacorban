#!/bin/sh

echo 'chwon...'
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/

echo 'install composer...'
composer install

echo 'copy .env...'
cp .env.example .env

echo 'app key...'
php artisan key:generate

echo 'migrates...'
php artisan migrate

echo 'supervisord...'
/usr/bin/supervisord -n -c /etc/supervisord/default.conf