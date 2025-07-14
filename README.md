### 📌 프로젝트 소개
Laravel 기반의 게시판 시스템입니다.  
회원/관리자 권한 분리, 이메일 인증, 게시물 상태 관리, 쪽지 기능 등을 포함합니다.  
개발 환경은 VirtualBox 기반의 Rocky Linux 8,
운영 환경은 AWS EC2 인스턴스에서 구축하여 실제 운영을 고려한 구성으로 제작하였습니다.
또한 Apache + PHP-FPM 조합, SELinux, AWS SES 등 실무 요소를 적용했습니다.


## ✨ 주요 기능
- 회원 가입 및 이메일 인증
- 회원 : 게시글 작성 / 상태 관리 / 파일 첨부 (최대 3개)
- 관리자 : 회원/게시글/공지 관리, 댓글 작성
- 쪽지 전송 (댓글 작성, 게시글 상태 변경 시 자동 발송)
- AWS SES를 통한 메일 발송


## 🛠 기술 스택

- **Language**: PHP 8.2
- **Framework**: Laravel 10
- **Database**: MySQL
- **Web Server**: Apache + PHP-FPM
- **개발 서버**: VirtualBox + Rocky Linux 8
- **운영 서버**: AWS EC2 (Rocky Linux 8 기반)
- **Email**: AWS SES
- **Dev Tools**: Composer, Git, Firewalld, systemctl 등



## ⚙️ 개발 환경 설치 및 실행 (VirtualBox 기준)
※ 운영계는 AWS EC2에 동일하게 Laravel 환경 구축 후 .env와 도메인, 메일 설정 등을 다르게 적용했습니다.


```bash
# 패키지 업데이트
sudo dnf update -y

# 필수 패키지 설치
sudo dnf install -y epel-release git unzip curl wget

# PHP 8.1 설치 (Remi 저장소 이용)
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
sudo dnf module reset php
sudo dnf module enable php:remi-8.1 -y
sudo dnf install php php-cli php-common php-pdo php-mysqlnd php-mbstring php-xml php-bcmath php-opcache php-gd php-curl -y

# Apache 설치 및 시작
sudo dnf install -y httpd
sudo systemctl enable httpd
sudo systemctl start httpd

# Firewall 설정
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload

# Composer 설치
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Laravel 프로젝트 설치
composer create-project laravel/laravel mj2
cd mj2
composer install

# 환경 설정
cp .env.example .env
php artisan key:generate

# .env DB 설정 예시
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mj
DB_USERNAME=mj
DB_PASSWORD=mjUser1212!

# 권한 설정
sudo chown -R apache:apache storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 마이그레이션
php artisan migrate


📌 PHP 설정 예시 (파일 업로드 용량 관련)
# php.ini 경로로 이동
cd /etc
sudo vi php.ini

# 아래 설정값 수정
upload_max_filesize = 10M
max_file_uploads = 20

# PHP-FPM 재시작
sudo systemctl restart php-fpm

```


✨ 기타 참고
- 개발계 서버는 VirtualBox + NAT 구성으로 포트포워딩 설정 (예: MySQL 3307, VSCode 2121 등)
- 운영계 서버는 AWS EC2 인스턴스에 Laravel 배포 및 AWS 도메인(mjnadev.com) 연결
- AWS EC2 보안그룹 설정을 통해 80, 443, 3306 등 포트 허용
- AWS SES를 이용한 인증 메일 / 비밀번호 재설정 메일 발송 구성
- 삭제 게시물, 탈퇴 회원 글 등도 관리하여 유지보수 고려한 설계
