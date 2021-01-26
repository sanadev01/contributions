cd /home/homeqdba/repositories/hd-v2/
git checkout development
git pull
cd /home/homeqdba/dev.homedeliverybr.com
cp -R /home/homeqdba/repositories/hd-v2/* /home/homeqdba/dev.homedeliverybr.com/
composer install --no-interaction
php artisan migrate
php artisan cache:clear
php artisan view:clear --force
php artisan config:clear --force