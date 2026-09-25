rm -rf ./node_modules
rm -rf ./vendor
rm -rf ./public/storage
rm -rf ./storage/app/public/images

composer install

npm install
npm run build

php artisan storage:link
php artisan optimize:clear