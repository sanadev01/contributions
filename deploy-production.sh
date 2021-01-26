cd /home/homeqdba/repositories/hd-v2/
git checkout master
git pull
cd /home/homeqdba/public_html/calculator
cp -R /home/homeqdba/repositories/hd-v2/* /home/homeqdba/public_html/calculator/
composer install --no-interaction
php artisan migrate --force
php artisan cache:clear --force
php artisan view:clear --force
php artisan config:clear --force