#!/bin/sh

cp .env.example .env

composer install

php artisan key:generate
php artisan migrate

/usr/bin/supervisord -n -c /etc/supervisord/default.conf