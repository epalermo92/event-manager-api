docker-compose up -d --force-recreate
docker exec event-manager-app_php_1 /var/www/bin/console cache:clear
docker exec event-manager-app_php_1 /var/www/bin/console cache:warmup
docker exec event-manager-app_php_1 chown -R www-data:www-data /var/www/var
docker exec event-manager-app_php_1 chmod -R 777 /var/www/var
