#!/bin/bash

# Define PHP version path
PHP_PATH="/usr/local/bin/ea-php73"

# Navigate to repository and update code
cd /home/homeqdba/repositories/hd-v2/
git checkout development
git reset --hard
git pull

# Copy updated code to deployment directory
cd /home/homeqdba/dev.homedeliverybr.com
cp -R /home/homeqdba/repositories/hd-v2/* /home/homeqdba/dev.homedeliverybr.com/

# Install dependencies using Composer
${PHP_PATH} /opt/cpanel/composer/bin/composer install --no-interaction

# Run Laravel Artisan commands
${PHP_PATH} artisan migrate --no-interaction
${PHP_PATH} artisan cache:clear --no-interaction
${PHP_PATH} artisan view:clear --no-interaction
${PHP_PATH} artisan config:clear --no-interaction

