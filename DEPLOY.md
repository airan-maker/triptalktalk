# TripTalk - AWS Lightsail 배포 가이드

## 1. Lightsail 인스턴스 생성

1. [AWS Lightsail 콘솔](https://lightsail.aws.amazon.com/)에 접속
2. **Create instance** 클릭
3. 설정:
   - **Platform**: Linux/Unix
   - **Blueprint**: OS Only → **Ubuntu 22.04 LTS**
   - **Plan**: $5/월 (1GB RAM, 1 vCPU) — 소규모 블로그에 충분
4. 인스턴스 이름 입력 후 **Create instance**

### 고정 IP 연결

1. Lightsail 콘솔 → **Networking** 탭
2. **Create static IP** → 생성한 인스턴스에 연결

### 방화벽 설정

Lightsail 콘솔 → 인스턴스 → **Networking** 탭 → Firewall 섹션:

| 포트 | 용도 |
|------|------|
| 22   | SSH (기본 열림) |
| 80   | HTTP (추가 필요) |

> phpMyAdmin(8081)은 열지 마세요. SSH 터널로만 접근합니다.

## 2. 배포

### SSH 접속

```bash
ssh -i <키파일.pem> ubuntu@<인스턴스-IP>
```

또는 Lightsail 콘솔에서 브라우저 기반 SSH 사용.

### deploy.sh 실행

```bash
# 배포 스크립트 다운로드 및 실행
curl -fsSL https://raw.githubusercontent.com/airan-maker/triptalktalk/main/deploy.sh -o deploy.sh
chmod +x deploy.sh

# REPO_URL을 실제 저장소 URL로 수정
nano deploy.sh

# 실행
sudo bash deploy.sh
```

배포가 완료되면 `http://<인스턴스-IP>`에서 WordPress 설치 화면이 나타납니다.

## 3. phpMyAdmin 접속 (SSH 터널)

phpMyAdmin은 보안을 위해 `127.0.0.1:8081`에만 바인딩되어 있어 외부에서 직접 접근할 수 없습니다.

**로컬 터미널에서:**

```bash
ssh -L 8081:127.0.0.1:8081 -i <키파일.pem> ubuntu@<인스턴스-IP>
```

이후 브라우저에서 `http://localhost:8081`로 접속.

## 4. 업데이트

테마나 설정을 변경한 후 서버에 반영하려면:

```bash
cd /opt/wordpress-trip
sudo git pull
sudo docker compose -f docker-compose.prod.yml up -d
```

## 5. 백업

### 데이터베이스 백업

```bash
cd /opt/wordpress-trip
sudo docker compose -f docker-compose.prod.yml exec db \
  mysqldump -u root -p"$(grep DB_ROOT_PASSWORD .env | cut -d= -f2)" flavor_trip > backup.sql
```

### WordPress 파일 백업

```bash
sudo docker compose -f docker-compose.prod.yml cp wordpress:/var/www/html ./wp-backup
```

### Lightsail 스냅샷

Lightsail 콘솔에서 인스턴스 스냅샷을 생성하면 전체 서버를 백업할 수 있습니다.

## 6. HTTPS 설정 (선택)

Let's Encrypt로 무료 SSL 인증서를 설정하려면 리버스 프록시(Nginx 등)를 추가해야 합니다. 도메인 연결 후 설정을 권장합니다.
