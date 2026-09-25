rm -R ./node_modules
rm -R ./vendor
rm -R ./public/storage

composer install --optimize-autoloader --no-dev

npm install
npm run build
rm -R ./node_modules

php artisan storage:link
php artisan queue:restart
php artisan optimize

echo "Build completed."