cd /var/repos/hd
git fetch
git reset --hard
git checkout master
git pull
cd /var/www/hd
rsync -av /var/repos/hd /var/www/hd
composer install --no-interaction
php artisan migrate --force
php artisan cache:clear --no-interaction
php artisan view:clear --no-interaction
php artisan config:clear --no-interaction