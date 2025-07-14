# 운영 서버 배포 문서 (readme_deploy.md)

## ✅ 운영 환경 구성
- **서버**: AWS EC2 (Rocky Linux 8)
- **웹서버**: Apache + PHP-FPM
- **보안**: SELinux 설정 적용, EC2 보안 그룹 포트 설정
- **도메인**: mjnadev.com 연결
- **이메일 서비스**: AWS SES 연동 (샌드박스 탈출 포함)

---

## 🛠 EC2 인스턴스 설정

1. EC2 인스턴스 생성 (Amazon Linux 계열 → Rocky Linux 8)
2. 운영계 서버의 IP가 변경되지 않도록 Elastic IP(탄력적 IP) 를 할당하여 EC2 인스턴스에 고정
   - 일반 퍼블릭 IP는 EC2를 재시작하면 바뀔 수 있어 접속 불안정 문제가 생김  
   - Elastic IP를 사용하면 항상 고정된 IP로 접근 가능
3. 포트 오픈 (보안 그룹에서 설정)
   - 22 (SSH)
   - 80 (HTTP)
   - 443 (HTTPS)
   - 3306 (MySQL 접속 시 필요)

---

## 📁 Laravel 배포

1. GitHub에서 프로젝트 클론  
```bash
   git clone https://github.com/NAeeeee/mj2.git
   cd mj2
   composer install
   cp .env.example .env
   php artisan key:generate
```

2. .env 운영 환경에 맞게 수정
- DB 연결 정보
- SES 메일 정보
- 도메인 설정

3. 퍼미션 설정

```bash
sudo chown -R apache:apache storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

4. Apache 가상 호스트 설정
```bash
# 설정 : /etc/httpd/conf/vhost.conf
<VirtualHost *:80>
    ServerName mjnadev.com
    DocumentRoot /var/www/mj2/public

    <Directory /var/www/mj2/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog /var/log/httpd/mj_laravel_error.log
    CustomLog /var/log/httpd/mj_laravel_access.log combined
</VirtualHost>

sudo systemctl restart httpd

```

5. 🔐 SELinux 설정
- SELinux가 활성화된 상태에서 Laravel storage/logs, cache 등에 접근 허용 설정

```bash
 sudo chcon -R -t httpd_sys_rw_content_t /var/www/mj2/storage
 sudo chcon -R -t httpd_sys_rw_content_t /var/www/mj2/bootstrap/cache
```

6. 마이그레이션
```bash
php artisan migrate
```

---

# 🌐 도메인 연결
1. Namecheap 에서 mjnadev.com 도메인 구입

2. A 레코드에 EC2의 고정 IP 등록

3. .env에서 APP_URL, MAIL_FROM_ADDRESS 등 도메인에 맞게 설정

---

# 📧 AWS SES 설정
- 발신자 이메일 인증
- 샌드박스 해제 신청 (증진)

---

# ⚙️ 서비스 등록 및 자동 실행
- Apache, PHP-FPM 부팅 시 자동 시작

```bash
sudo systemctl enable httpd
sudo systemctl enable php-fpm
```
