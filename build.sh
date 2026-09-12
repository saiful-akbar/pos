rm -R ./vendor
rm -R ./node_modules
rm -R ./public/storage

composer install --optimize-autoloader --no-dev

npm install
npm run build

php artisan storage:link
php artisan queue:restart
php artisan optimize

echo "Build completed."