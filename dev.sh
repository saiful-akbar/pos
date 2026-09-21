rm -R ./node_modules
rm -R ./vendor
rm -R ./public/storage
rm -R ./storage/app/public/images

composer install

npm install
npm run build

php artisan storage:link
php artisan optimize:clear