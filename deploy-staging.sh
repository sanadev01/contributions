cd /home/homeqdba/repositories/hd-v2/
git checkout development
git pull
cd /home/homeqdba/dev.homedeliverybr.com
cp -R /home/homeqdba/repositories/hd-v2/* /home/homeqdba/dev.homedeliverybr.com/
composer install --no-interaction
php artisan migrate
php artisan cache:clear --no-interaction
php artisan view:clear --no-interaction
php artisan config:clear --no-interaction