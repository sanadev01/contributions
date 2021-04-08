cd /var/repos/hd
git reset --hard
git fetch
git checkout master
git pull origin master
rsync -av /var/repos/hd/ /var/www/hd
cd /var/www/hd
composer install --no-interaction
php artisan migrate --force
php artisan cache:clear --no-interaction
php artisan view:clear --no-interaction
php artisan config:clear --no-interaction