cd /home/homeqdba/repositories/hd-v2/
git checkout development
git reset --hard
git pull
cd /home/homeqdba/dev.homedeliverybr.com
cp -R /home/homeqdba/repositories/hd-v2/* /home/homeqdba/dev.homedeliverybr.com/
/usr/local/bin/ea-php73 /opt/cpanel/composer/bin/composer install --no-interaction
php artisan migrate
php artisan cache:clear --no-interaction
php artisan view:clear --no-interaction
php artisan config:clear --no-interaction