cd /home/homeqdba/repositories/hd-v2/
git checkout development
git pull
cd /home/homeqdba/dev.homedeliverybr.com
cp -R /home/homeqdba/repositories/hd-v2/* /home/homeqdba/dev.homedeliverybr.com/
composer install
php artisan migrate
php artisan cache:clear