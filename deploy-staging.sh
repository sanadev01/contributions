#!/bin/bash

# Define PHP version path
# PHP_PATH="/usr/local/bin/ea-php73"

# Navigate to repository and update code
cd /home/homeqdba/repositories/hd-v2/
git checkout development
git reset --hard
git pull

# Copy updated code to deployment directory
cd /home/homeqdba/dev.homedeliverybr.com
cp -R /home/homeqdba/repositories/hd-v2/* /home/homeqdba/dev.homedeliverybr.com/

# Install dependencies using Composer
composer install --no-interaction

# Run Laravel Artisan commands
php artisan migrate --no-interaction
php artisan cache:clear --no-interaction
php artisan view:clear --no-interaction
php artisan config:clear --no-interaction

