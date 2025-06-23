# 
이 프로젝트는 실무 경험을 기반으로 설계된 관리자/회원 게시판 시스템입니다.
단순한 CRUD를 넘어서, 실제 운영 환경에서 요구되는 권한 분리, 상태 관리, 파일 업로드 제한, 탈퇴 회원 데이터 처리, 쪽지 자동 전송 등
실제 서비스 운영에 필요한 기능 흐름을 고려하여 구현하였습니다.

본 프로젝트는 개인 포트폴리오 용도로 제작되었으며,
실무에서 약 2년간 고객사 유지보수 및 기능 개발 업무를 수행한 경험을 바탕으로 구성되었습니다.

## 주요 특징
- 권한 분리: 관리자 / 일반 회원 기능 명확하게 구분
- 답변 시스템 + 쪽지 알림: 관리자가 답변 시 회원에게 자동 쪽지 발송(읽음 처리 표시)
- 파일 첨부 제한: 게시글 및 답글 작성 시 첨부파일 최대 3개 제한
- 게시글 상태 관리: 답변 유무 및 카테고리에 따른 탭 필터 구현
- 삭제 글 / 탈퇴 글 관리: 운영자가 전체 데이터 흐름을 파악할 수 있도록 관리 페이지 구성
- 탈퇴 회원 처리: 개인정보 보호를 위해 탈퇴처리후 탈퇴회원이 남긴 게시물 이름을 '탈퇴회원'으로 표기

## 🛠 기능
👤 회원 기능
- 회원가입 / 로그인
- 회원 정보 수정
- 회원 탈퇴
- 게시글 작성, 수정, 삭제 (CRUD)
- 게시글 파일 업로드 기능 (최대 3개까지 첨부 가능)

👤 관리자 기능
- 회원 게시글에 답글 작성 가능
    → 답글 작성 시, 해당 회원에게 쪽지 자동 발송
- 답글 파일 첨부 가능 (최대 3개까지)
- 회원이 작성한 모든 글 확인 가능
    → 회원이 삭제된 글, 탈퇴한 회원의 글도 확인 가능
    → 단, 탈퇴 회원명은 ‘탈퇴회원’ 등으로 표시되어 개인정보 보호
- 게시글 상태 변경 가능 (ex. 검토 → 처리 등)
- 게시판 전체보기 화면 탭 분류
    → 전체 : 모든 게시글 조회
    → 미답변 : 답변이 없는 게시글만 조회
    → 답변 완료 : 답변이 완료된 게시글만 조회
    → 항목 : 카테고리별 필터링 (견적, 배송, 계정, 기타)
    → 삭제 : 삭제 처리된 게시글 조회
    → 탈퇴 : 탈퇴한 회원이 작성한 게시글 조회


## 📂 기술 스택

- **Language**: PHP 8.1
- **Framework**: Laravel 10
- **Database**: MySQL
- **Web Server**: Apache
- **OS**: Rocky Linux 8 (VirtualBox 가상 서버)
- **Tooling**: Composer, Git, Firewalld, systemctl 등

## 💡 주요 기능

### 👤 회원 기능
- 회원가입 / 로그인
- 회원 정보 수정
- 회원 탈퇴  
  → 탈퇴 후에도 작성한 글은 남아 있으며, 이름은 ‘탈퇴회원’으로 표시
- 게시글 작성, 수정, 삭제 (CRUD)
- 게시글 파일 업로드 (최대 3개 첨부 가능)

---

### 🛠️ 관리자 기능
- 회원 게시글에 답글 작성 가능  
  → 답글 작성 시 해당 회원에게 쪽지 자동 발송
- 답글 파일 업로드 (최대 3개 첨부 가능)
- 게시글 상태 변경 (예: 검토 → 처리 등)
- 회원이 작성한 모든 글 확인 가능  
  → 삭제된 글, 탈퇴 회원의 글 포함
- 게시판 전체보기 탭 분류
  - **전체**: 모든 게시글 조회
  - **미답변**: 답변이 없는 글만 조회
  - **답변 완료**: 답변 완료된 글만 조회
  - **항목별**: 견적 / 배송 / 계정 / 기타
  - **삭제건**: 삭제된 글 조회
  - **탈퇴회원 글**: 탈퇴 회원이 작성한 글 조회

## 🖥️ 개발 환경
- VirtualBox + NAT 포트포워딩 구성
- 리눅스 서버 수동 구성 경험 포함

## 📸 스크린샷

## 💻 소스코드
- https://github.com/NAeeeee/25_mj2


## ⚙️ 설치 방법

# 패키지 업데이트
sudo dnf update -y

# 필수 패키지 설치
sudo dnf install -y epel-release git unzip curl wget

# PHP 8.1 설치 (Remi 저장소 이용)
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
sudo dnf module reset php
sudo dnf module enable php:remi-8.1 -y
sudo dnf install php php-cli php-common php-pdo php-mysqlnd php-mbstring php-xml php-bcmath php-opcache php-gd php-curl -y

# Apache 설치
sudo dnf install -y httpd
sudo systemctl enable httpd
sudo systemctl start httpd

# Firewall 설정 (선택적)
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload

# Composer 설치
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Laravel 프로젝트 생성
composer create-project laravel/laravel mj2
cd mj2(프로젝트폴더명)
composer install

# 환경 파일 설정
cp .env.example .env
php artisan key:generate

# .env DB 설정
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mj
DB_USERNAME=mj
DB_PASSWORD=mjUser1212!

# 권한 변경 (chmod)
sudo chown -R apache:apache storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 마이그레이션 실행
php artisan migrate


✨ 기타 참고
- 포트포워딩을 통해 가상 서버를 외부 접속 가능하도록 구성함 (VirtualBox + NAT)
  → mysql 3307, vscode 2121 등
- 유지보수를 고려한 설계: 삭제 글/탈퇴 회원 글 보존, 상태 기반 응답 처리 등