# Flavor Trip 다국어 설정 가이드

이 가이드는 Polylang 플러그인을 사용하여 Flavor Trip 테마의 다국어 지원을 설정하는 방법을 설명합니다.

## 1. Polylang 플러그인 설치

### WordPress 관리자 패널에서 설치
1. **플러그인 > 새로 추가** 메뉴로 이동
2. "Polylang" 검색
3. **Polylang** 플러그인 설치 및 활성화
4. (선택) **Polylang Pro** - 자동 번역 기능 포함 (유료)

### WP-CLI로 설치
```bash
wp plugin install polylang --activate
```

## 2. 언어 설정

### 언어 추가
1. **언어 > 언어** 메뉴로 이동
2. 다음 언어들을 추가:

| 언어 | 코드 | 로케일 | 기본값 |
|------|------|--------|--------|
| 한국어 | ko | ko_KR | ✓ (기본 언어) |
| English | en | en_US | |
| 中文 | zh | zh_CN | |
| 日本語 | ja | ja | |

### URL 구조 설정
**언어 > 설정**에서:
- **URL 수정** → "언어 코드가 포함된 디렉토리" 선택
  - 예: `example.com/en/`, `example.com/zh/`, `example.com/ja/`
- **기본 언어 URL 숨기기** → 체크 (한국어는 `example.com/`으로 유지)

## 3. 테마 번역 파일 적용

테마 번역 파일이 이미 준비되어 있습니다:
- `languages/en_US.po` - 영어
- `languages/zh_CN.po` - 중국어 (간체)
- `languages/ja.po` - 일본어

### MO 파일 생성
PO 파일을 MO 파일로 컴파일해야 합니다:

**방법 1: Poedit 사용**
1. [Poedit](https://poedit.net/) 설치
2. PO 파일 열기
3. **파일 > MO로 컴파일** 또는 저장 시 자동 생성

**방법 2: WP-CLI 사용**
```bash
# msgfmt 필요 (gettext 패키지)
cd wp-content/themes/flavor-trip/languages
msgfmt -o en_US.mo en_US.po
msgfmt -o zh_CN.mo zh_CN.po
msgfmt -o ja.mo ja.po
```

**방법 3: Loco Translate 플러그인**
1. Loco Translate 플러그인 설치
2. **Loco Translate > 테마 > Flavor Trip** 선택
3. 각 언어의 PO 파일 편집 및 저장 (자동으로 MO 생성)

## 4. 콘텐츠 번역

### Custom Post Type 번역 활성화
**언어 > 설정 > 커스텀 포스트 타입 및 택소노미**에서:
- ✓ `travel_itinerary` (여행 일정)
- ✓ `destination` (여행지)
- ✓ `travel_style` (여행 스타일)

### 콘텐츠 번역 방법

#### 방법 1: 수동 번역
1. 기존 한국어 여행 일정 편집
2. 오른쪽 사이드바에서 **언어** 메타박스 확인
3. 각 언어 옆 **+** 버튼 클릭하여 번역본 생성
4. 번역된 내용 입력

#### 방법 2: 자동 번역 (Polylang Pro + DeepL/Google)
1. Polylang Pro 라이센스 구매
2. DeepL API 또는 Google Cloud Translation API 키 설정
3. 콘텐츠 자동 번역 기능 사용

#### 방법 3: 번역 시드 스크립트 사용
`seed-data-multilingual.php` 스크립트로 번역된 콘텐츠 일괄 생성:
```bash
wp eval-file seed-data-multilingual.php
```

## 5. 메뉴 및 위젯 다국어화

### 메뉴 번역
1. **외모 > 메뉴**로 이동
2. 각 언어별 메뉴 생성 (예: Main Menu - EN, Main Menu - ZH)
3. Polylang 설정에서 언어별 메뉴 연결

### 위젯 번역
1. **외모 > 위젯**으로 이동
2. Polylang 언어 위젯 추가 (언어 전환기)
3. 위치: 헤더 또는 푸터 권장

## 6. 언어 전환기 추가

### 위젯으로 추가
**외모 > 위젯**에서 "Polylang 언어 전환기" 위젯을 원하는 위치에 추가

### 메뉴에 추가
**외모 > 메뉴**에서:
1. "언어 전환기" 항목 추가 (Polylang 섹션)
2. 드롭다운 또는 플래그 형식 선택

### PHP 코드로 추가 (header.php)
```php
<?php if (function_exists('pll_the_languages')) : ?>
<nav class="language-switcher">
    <?php pll_the_languages(['show_flags' => 1, 'show_names' => 1]); ?>
</nav>
<?php endif; ?>
```

## 7. SEO 최적화

### hreflang 태그
Polylang이 자동으로 추가합니다:
```html
<link rel="alternate" hreflang="ko" href="https://example.com/travel/osaka/" />
<link rel="alternate" hreflang="en" href="https://example.com/en/travel/osaka/" />
<link rel="alternate" hreflang="zh" href="https://example.com/zh/travel/osaka/" />
<link rel="alternate" hreflang="ja" href="https://example.com/ja/travel/osaka/" />
```

### Yoast SEO 연동
Yoast SEO가 설치되어 있다면:
1. **Yoast SEO > 일반 > 기능**에서 "Advanced settings pages" 활성화
2. Polylang과 자동 연동됨

## 8. 문제 해결

### 번역이 표시되지 않는 경우
1. MO 파일이 올바르게 생성되었는지 확인
2. 언어 코드가 파일명과 일치하는지 확인
3. 캐시 플러그인 사용 시 캐시 삭제

### URL이 올바르게 작동하지 않는 경우
1. **설정 > 고유주소**에서 "변경 사항 저장" 클릭
2. .htaccess 파일 권한 확인

### 커스텀 필드가 번역되지 않는 경우
Polylang은 기본적으로 메타 필드를 복사합니다. 언어별 다른 값이 필요하면:
1. **언어 > 설정 > 동기화**에서 해당 필드 동기화 해제
2. 또는 Polylang Pro의 번역 가능 필드 기능 사용

## 9. 추천 플러그인 조합

| 플러그인 | 용도 |
|----------|------|
| Polylang | 다국어 기본 기능 |
| Polylang Pro | 자동 번역, 고급 기능 |
| Loco Translate | 테마/플러그인 번역 편집 |
| Yoast SEO | SEO 최적화 (다국어 지원) |
| WP Super Cache | 캐시 (다국어 URL 지원) |

## 10. Klook 제휴 링크 다국어화

현재 Klook 링크는 한국어(`/ko/`)로 설정되어 있습니다.
언어별 Klook 링크를 사용하려면 `seed-data-v2.php`의 `klook_url()` 함수를 수정하세요:

```php
function klook_url($activity_id, $lang = 'ko') {
    $lang_codes = [
        'ko' => 'ko',
        'en' => 'en-US',
        'zh' => 'zh-CN',
        'ja' => 'ja',
    ];
    $code = $lang_codes[$lang] ?? 'en-US';
    return "https://www.klook.com/{$code}/activity/{$activity_id}/?aid=" . KLOOK_AID;
}
```

---

## 빠른 시작 체크리스트

- [ ] Polylang 플러그인 설치 및 활성화
- [ ] 언어 추가 (한국어, 영어, 중국어, 일본어)
- [ ] URL 구조 설정
- [ ] PO → MO 파일 컴파일
- [ ] Custom Post Type 번역 활성화
- [ ] 언어 전환기 추가
- [ ] 콘텐츠 번역 시작
