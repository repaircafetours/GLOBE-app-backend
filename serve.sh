export APP_KEY=`php artisan key:generate --show`

php artisan serve --host=${LISTEN_ON} --port=${PORT}