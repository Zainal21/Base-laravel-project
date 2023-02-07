web: vendor/bin/heroku-php-nginx -C .heroku/nginx/nginx.conf public/
queue: php artisan queue:work --delay=5 --tries=3