# TripTalkTalk Import Pipeline (GitHub Actions + WP-CLI)

이 폴더는 `triptalktalk` 레포에 붙여서 사용하는 업로드 파이프라인 템플릿입니다.

## 1) 파일 배치

- `ops/triptalktalk/import-vlogs.wpcli.php`
- `.github/workflows/import-vlogs.yml` (본 폴더의 `import-vlogs.yml` 내용으로 생성)

## 2) GitHub Secrets

레포 `Settings > Secrets and variables > Actions`에 아래 시크릿 추가:

- `WP_SSH_HOST`: 서버 호스트 (예: `blog.triptalk.me`)
- `WP_SSH_USER`: SSH 사용자
- `WP_SSH_KEY`: SSH 개인키 (멀티라인 전체)
- `WP_PATH`: 워드프레스 루트 경로 (예: `/var/www/triptalktalk`)
- `WP_IMPORT_DIR`: 서버에서 import 파일을 둘 경로 (예: `/var/www/triptalktalk/shared/import`)
- `WP_IMPORT_AUTHOR_ID`: 작성자 user ID (예: `1`)

## 3) 서버 준비

서버에서 아래 확인:

```bash
mkdir -p /var/www/triptalktalk/shared/import
cd /var/www/triptalktalk
wp --info
```

또한 `wp-content` 아래에 `ops/triptalktalk/import-vlogs.wpcli.php`가 존재해야 합니다.

## 4) import 파일 준비

`data/vlog-import/vlogs.jsonl` 경로에 업로드할 JSONL을 커밋합니다.

각 줄은 JSON object이며, 최소한 아래 구조 필요:

- `source.videoUrl` 또는 `source.youtubeId`
- `vlogDraft.title` 또는 `source.videoTitle`

권장:

- `source.channelName`, `source.channelUrl`, `source.duration`
- `vlogDraft.timeline[]`, `vlogDraft.spots[]`

## 5) 실행

### 수동 실행

GitHub Actions에서 `Import Vlogs to WordPress` 선택 후 `Run workflow`.

입력:
- `jsonl_path`: `data/vlog-import/vlogs.jsonl`
- `post_status`: `draft` (검수 후 publish 권장)

### 자동 실행

`main` 브랜치에 아래 파일 변경이 push되면 자동 실행:
- `data/vlog-import/*.jsonl`
- `ops/triptalktalk/import-vlogs.wpcli.php`
- `.github/workflows/import-vlogs.yml`

## 6) 동작 방식

- 같은 `youtube_id`(`_ft_vlog_youtube_id`)가 있으면 `update`
- 없으면 새 `vlog_curation` 생성
- 메타 업데이트:
  - `_ft_vlog_youtube_id`
  - `_ft_vlog_channel_name`
  - `_ft_vlog_channel_url`
  - `_ft_vlog_duration`
  - `_ft_vlog_timeline`
  - `_ft_vlog_spots`

## 7) 안전 운영 팁

- 기본 `post_status=draft`로 먼저 올려서 검수
- 워크플로우용 SSH 키는 deploy 전용/읽기-실행 최소권한 계정 사용
- import 전에 DB 백업 또는 스냅샷 권장
