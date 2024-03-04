cd /var/repos/hd
git reset --hard
git fetch
git checkout master
git pull origin master
rsync -av /var/repos/hd/ /var/www/hd
cd /var/www/hd
/usr/bin/php7.4 composer.phar install --no-interaction
/usr/bin/php7.4 artisan migrate --force
/usr/bin/php7.4 artisan cache:clear --no-interaction
/usr/bin/php7.4 artisan view:clear --no-interaction
/usr/bin/php7.4 artisan config:clear --no-interaction
