#!/usr/bin/env bash
set -e

composer install
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan key:generate
touch database/database.sqlite
php artisan migrate --force