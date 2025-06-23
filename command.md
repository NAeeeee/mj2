# 캐시 삭제
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 아파치 명령어
systemctl status httpd
systemctl stop httpd
systemctl start httpd
systemctl restart httpd

# 아파치 로그
/var/log/httpd/error_log
/var/log/httpd/access_log