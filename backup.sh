#!/bin/bash
set -euo pipefail

# ─────────────────────────────────────────────
# Flavor Trip - DB 백업/복원 스크립트
#
# 사용법:
#   ./backup.sh backup          # DB 백업 (backups/ 폴더에 저장)
#   ./backup.sh restore         # 가장 최근 백업으로 복원
#   ./backup.sh restore <파일>  # 특정 백업 파일로 복원
#   ./backup.sh list            # 백업 목록 확인
# ─────────────────────────────────────────────

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
BACKUP_DIR="$SCRIPT_DIR/backups"
ENV_FILE="$SCRIPT_DIR/.env"

# .env 로드
if [ ! -f "$ENV_FILE" ]; then
    echo "오류: .env 파일이 없습니다."
    exit 1
fi
source "$ENV_FILE"

DB_CONTAINER="$(docker compose -f "$SCRIPT_DIR/docker-compose.yml" ps -q db 2>/dev/null || docker compose -f "$SCRIPT_DIR/docker-compose.prod.yml" ps -q db 2>/dev/null)"

if [ -z "$DB_CONTAINER" ]; then
    echo "오류: DB 컨테이너가 실행 중이 아닙니다."
    echo "먼저 docker compose up -d 를 실행하세요."
    exit 1
fi

mkdir -p "$BACKUP_DIR"

backup() {
    local TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    local FILENAME="flavor_trip_${TIMESTAMP}.sql.gz"
    local FILEPATH="$BACKUP_DIR/$FILENAME"

    echo "백업 시작: ${DB_NAME}..."
    docker exec "$DB_CONTAINER" mysqldump \
        -u"$DB_USER" \
        -p"$DB_PASSWORD" \
        --single-transaction \
        --routines \
        --triggers \
        "$DB_NAME" | gzip > "$FILEPATH"

    local SIZE=$(du -h "$FILEPATH" | cut -f1)
    echo "백업 완료: $FILENAME ($SIZE)"

    # 30일 이상 된 백업 자동 삭제
    local OLD_COUNT=$(find "$BACKUP_DIR" -name "*.sql.gz" -mtime +30 | wc -l)
    if [ "$OLD_COUNT" -gt 0 ]; then
        find "$BACKUP_DIR" -name "*.sql.gz" -mtime +30 -delete
        echo "오래된 백업 ${OLD_COUNT}개 정리됨 (30일 초과)"
    fi
}

restore() {
    local FILEPATH="$1"

    if [ ! -f "$FILEPATH" ]; then
        echo "오류: 파일을 찾을 수 없습니다: $FILEPATH"
        exit 1
    fi

    echo "================================================"
    echo "  주의: 현재 DB의 모든 데이터가 덮어씌워집니다!"
    echo "  복원 파일: $(basename "$FILEPATH")"
    echo "================================================"
    read -p "정말 복원하시겠습니까? (y/N): " CONFIRM

    if [[ "$CONFIRM" != "y" && "$CONFIRM" != "Y" ]]; then
        echo "복원 취소됨."
        exit 0
    fi

    echo "복원 중..."
    gunzip -c "$FILEPATH" | docker exec -i "$DB_CONTAINER" mysql \
        -u"$DB_USER" \
        -p"$DB_PASSWORD" \
        "$DB_NAME"

    echo "복원 완료!"
}

list_backups() {
    if [ ! -d "$BACKUP_DIR" ] || [ -z "$(ls -A "$BACKUP_DIR"/*.sql.gz 2>/dev/null)" ]; then
        echo "백업 파일이 없습니다."
        exit 0
    fi

    echo "═══════════════════════════════════════"
    echo "  백업 목록 (backups/ 폴더)"
    echo "═══════════════════════════════════════"
    ls -lh "$BACKUP_DIR"/*.sql.gz | awk '{print NR". "$NF" ("$5")"}'
}

# 메인
case "${1:-help}" in
    backup)
        backup
        ;;
    restore)
        if [ -n "${2:-}" ]; then
            restore "$2"
        else
            # 가장 최근 백업 파일
            LATEST=$(ls -t "$BACKUP_DIR"/*.sql.gz 2>/dev/null | head -1)
            if [ -z "$LATEST" ]; then
                echo "오류: 백업 파일이 없습니다. 먼저 ./backup.sh backup 을 실행하세요."
                exit 1
            fi
            echo "가장 최근 백업: $(basename "$LATEST")"
            restore "$LATEST"
        fi
        ;;
    list)
        list_backups
        ;;
    *)
        echo "사용법:"
        echo "  ./backup.sh backup          # DB 백업"
        echo "  ./backup.sh restore         # 최근 백업으로 복원"
        echo "  ./backup.sh restore <파일>  # 특정 파일로 복원"
        echo "  ./backup.sh list            # 백업 목록"
        ;;
esac
