cd /home/homeqdba/repositories/hd-v2/
git checkout master
git pull
cd /home/homeqdba/public_html/calculator
cp -R /home/homeqdba/repositories/hd-v2/* /home/homeqdba/public_html/calculator/
composer install
php artisan migrate
php artisan cache:clear