#!/bin/bash
set -euo pipefail

REPO_URL="https://github.com/airan-maker/triptalktalk.git"
PROJECT_DIR="/opt/wordpress-trip"

echo "========================================="
echo " Flavor Trip - Lightsail 배포 스크립트"
echo "========================================="

# 1. Docker 설치
if ! command -v docker &> /dev/null; then
    echo "[1/4] Docker 설치 중..."
    sudo apt-get update
    sudo apt-get install -y ca-certificates curl gnupg
    sudo install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
    sudo chmod a+r /etc/apt/keyrings/docker.gpg
    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
      $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
      sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
    sudo apt-get update
    sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
    sudo usermod -aG docker "$USER"
    echo "Docker 설치 완료. 그룹 변경 적용을 위해 newgrp docker를 실행합니다."
    newgrp docker <<INNERSCRIPT
    echo "Docker 그룹 적용됨"
INNERSCRIPT
else
    echo "[1/4] Docker 이미 설치됨 - 건너뜁니다."
fi

# 2. 프로젝트 클론
if [ -d "$PROJECT_DIR" ]; then
    echo "[2/4] 기존 프로젝트 업데이트 중..."
    cd "$PROJECT_DIR"
    sudo git pull
else
    echo "[2/4] 프로젝트 클론 중..."
    sudo git clone "$REPO_URL" "$PROJECT_DIR"
    cd "$PROJECT_DIR"
fi

# 3. .env 파일 생성
if [ ! -f "$PROJECT_DIR/.env" ]; then
    echo "[3/4] .env 파일 생성 중 (랜덤 비밀번호)..."
    DB_PASSWORD=$(openssl rand -base64 24 | tr -dc 'A-Za-z0-9' | head -c 32)
    DB_ROOT_PASSWORD=$(openssl rand -base64 24 | tr -dc 'A-Za-z0-9' | head -c 32)

    sudo tee "$PROJECT_DIR/.env" > /dev/null <<EOF
DB_NAME=flavor_trip
DB_USER=flavor_user
DB_PASSWORD=${DB_PASSWORD}
DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
EOF

    sudo chmod 600 "$PROJECT_DIR/.env"
    echo ".env 파일 생성 완료 (비밀번호 자동 생성됨)"
else
    echo "[3/4] .env 파일이 이미 존재합니다 - 건너뜁니다."
fi

# 4. Docker Compose 실행
echo "[4/4] Docker Compose 실행 중..."
cd "$PROJECT_DIR"
sudo docker compose -f docker-compose.prod.yml up -d

echo ""
echo "========================================="
echo " 배포 완료!"
echo "========================================="
echo ""
echo "WordPress: http://$(curl -s http://checkip.amazonaws.com)"
echo "phpMyAdmin: SSH 터널을 통해 접근하세요:"
echo "  ssh -L 8081:127.0.0.1:8081 ubuntu@<인스턴스-IP>"
echo "  → http://localhost:8081"
echo ""
echo "Lightsail 방화벽에서 포트 80(HTTP)이 열려있는지 확인하세요."
echo "========================================="
