<?php
/**
 * 브이로그 큐레이션 시드 데이터 v2
 *
 * 실제 유튜브 영상 기반 큐레이션 (8편)
 * - 히로시마 (산보노트, Sam and Victor)
 * - 마츠야마 (산보노트)
 * - 일본 소도시 비교 (산보노트)
 * - 영월 (큰손 노희영)
 * - 도쿄 (Mei Time)
 * - 교토 근교 (Mei Time)
 * - 도야마 (Mei Time)
 *
 * 사용법: docker-compose exec -T wordpress bash -c \
 *   "wp eval-file /var/www/html/wp-content/themes/flavor-trip/seed-data-vlogs-v2.php --allow-root"
 *
 * @package Flavor_Trip
 */

if (!defined('ABSPATH')) {
    echo "WordPress 환경에서 실행하세요.\n";
    exit;
}

// ── 여행지 확인/생성 함수 ──
if (!function_exists('ft_vlog_ensure_destination')) {
    function ft_vlog_ensure_destination($name, $slug, $parent_slug = '') {
        $term = get_term_by('slug', $slug, 'destination');
        if ($term) return $term->term_id;

        $parent_id = 0;
        if ($parent_slug) {
            $parent = get_term_by('slug', $parent_slug, 'destination');
            if ($parent) $parent_id = $parent->term_id;
        }

        $result = wp_insert_term($name, 'destination', [
            'slug'   => $slug,
            'parent' => $parent_id,
        ]);

        if (is_wp_error($result)) {
            $existing = get_term_by('slug', $slug, 'destination');
            return $existing ? $existing->term_id : 0;
        }
        return $result['term_id'];
    }
}

// ── 브이로그 카테고리 확인 ──
$vlog_categories = [
    '혼자여행'  => 'solo-travel',
    '커플여행'  => 'couple-travel',
    '먹방'      => 'mukbang',
    '한달살기'  => 'long-stay',
    '감성'      => 'aesthetic',
    '가성비'    => 'budget',
];

foreach ($vlog_categories as $name => $slug) {
    if (!term_exists($name, 'vlog_category')) {
        wp_insert_term($name, 'vlog_category', ['slug' => $slug]);
        echo "✅ 카테고리 생성: {$name}\n";
    }
}

// ── 브이로그 데이터 ──
$vlogs = [

    // ────────────────────────────────────────────
    // 1. 산보노트 - 히로시마 소도시 (오노미치·도모노우라·미야지마)
    // ────────────────────────────────────────────
    [
        'title'        => '한국인 없는 일본의 작은 마을 | 히로시마 오노미치·도모노우라·미야지마 여행',
        'excerpt'      => '애니 실사판 같은 히로시마의 소도시 3곳을 여행합니다. 오노미치 언덕 골목과 고양이, 포뇨의 배경 도모노우라, 그리고 히로시마 오코노미야키까지.',
        'content'      => '<p>오노미치는 바다와 산 사이 좁은 골목길을 걷는 것이 핵심인 \'언덕의 도시\'입니다. 센코지 로프웨이로 산 정상에 올라 세토 내해를 조망하고, 고양이 골목과 레트로 상점가를 거니는 슬로 트래블이 매력적입니다.</p>
<p>오노미치에서 열차와 버스로 약 50분 거리의 도모노우라는 에도시대 항구가 그대로 남아 있는 마을로, 미야자키 하야오 감독이 \'벼랑 위의 포뇨\'를 구상한 곳으로 유명합니다. 랜드마크인 조야토 석등과 호메이슈 양조장 체험이 볼거리입니다.</p>
<p>마지막으로 히로시마 시내에서 히로시마 성 야경을 감상하고, 오코노미무라에서 히로시마식 오코노미야키로 여행을 마무리합니다.</p>',
        'youtube_id'   => 'gMi8qDdxV9s',
        'channel_name' => '산보노트',
        'channel_url'  => 'https://www.youtube.com/@sanbonote',
        'duration'     => '',
        'destinations' => [
            ['name' => '일본', 'slug' => 'japan', 'parent' => ''],
            ['name' => '히로시마', 'slug' => 'hiroshima', 'parent' => 'japan'],
        ],
        'vlog_cats'    => ['감성', '혼자여행'],
        'timeline'     => [
            ['time' => '0:00', 'title' => '오노미치역 도착', 'description' => '레트로 상점가와 언덕 마을 첫인상'],
            ['time' => '2:00', 'title' => '센코지 로프웨이', 'description' => '산 정상에서 세토 내해 조망'],
            ['time' => '5:00', 'title' => '센코지 공원 & 절', 'description' => '806년 창건 사찰, 미하라시테이 카페'],
            ['time' => '8:00', 'title' => '고양이 골목', 'description' => '네코노 호소미치, 돌벽 고양이 그림'],
            ['time' => '10:00', 'title' => '오노미치 상점가', 'description' => '혼도리 아케이드, 오노미치 라멘'],
            ['time' => '13:00', 'title' => '도모노우라 도착', 'description' => '포뇨의 배경, 에도시대 항구 마을'],
            ['time' => '17:00', 'title' => '조야토 & 호메이슈', 'description' => '석등 랜드마크, 약용주 양조장 시음'],
            ['time' => '20:00', 'title' => '히로시마 시내', 'description' => '히로시마 성 야경, 오코노미무라 저녁'],
        ],
        'spots'        => [
            ['name' => '오노미치역', 'lat' => 34.4089, 'lng' => 133.1946, 'description' => '여행 시작점, 레트로 상점가 입구'],
            ['name' => '센코지 공원', 'lat' => 34.4075, 'lng' => 133.2012, 'description' => '세토 내해 최고 뷰포인트'],
            ['name' => '고양이 골목 (네코노 호소미치)', 'lat' => 34.4072, 'lng' => 133.1996, 'description' => '골목길 고양이 벽화와 실제 고양이'],
            ['name' => '혼도리 상점가', 'lat' => 34.4094, 'lng' => 133.1978, 'description' => '오노미치 라멘 맛집 거리'],
            ['name' => '도모노우라 항구', 'lat' => 34.3834, 'lng' => 133.3831, 'description' => '벼랑 위의 포뇨 배경지, 에도시대 항구'],
            ['name' => '조야토 (상등루)', 'lat' => 34.3838, 'lng' => 133.3826, 'description' => '도모노우라 랜드마크 석등'],
            ['name' => '히로시마 성', 'lat' => 34.4015, 'lng' => 132.4594, 'description' => '야간 조명이 아름다운 성'],
            ['name' => '오코노미무라', 'lat' => 34.3920, 'lng' => 132.4567, 'description' => '히로시마식 오코노미야키 전문 건물'],
        ],
    ],

    // ────────────────────────────────────────────
    // 2. 산보노트 - 일본 소도시 9곳 비교 큐레이션
    // ────────────────────────────────────────────
    [
        'title'        => '직항으로 갈 수 있는 일본 소도시 9곳 비교 | 취향별 완벽 가이드',
        'excerpt'      => '미식·예술·자연·온천·역사, 취향에 따라 골라가는 일본 소도시 가이드. 다카마쓰, 오카야마, 요나고, 시즈오카, 마츠야마, 사가, 가나자와, 기타큐슈까지 한눈에 비교합니다.',
        'content'      => '<p>일본 소도시 여행, 어디로 갈지 고민이라면 취향에 따라 골라보세요. 이 영상은 직항 노선이 있는 주요 소도시를 5가지 테마로 분류합니다.</p>
<p>미식과 예술이라면 우동의 본고장 다카마쓰와 쿠라시키 미관지구의 오카야마. 대자연을 원한다면 모래언덕의 요나고·돗토리와 후지산의 시즈오카. 온천 힐링이라면 도고 온천의 마츠야마와 미인 온천의 사가. 역사와 전통이라면 작은 교토 가나자와. 가볍게 다녀오고 싶다면 후쿠오카 옆 기타큐슈가 정답입니다.</p>
<p>대부분 JR 패스나 지역별 교통 패스를 활용해 인근 대도시(오사카, 후쿠오카, 도쿄)와 묶어서 여행하기 좋습니다.</p>',
        'youtube_id'   => '0llIvXzH-Tk',
        'channel_name' => '산보노트',
        'channel_url'  => 'https://www.youtube.com/@sanbonote',
        'duration'     => '',
        'destinations' => [
            ['name' => '일본', 'slug' => 'japan', 'parent' => ''],
        ],
        'vlog_cats'    => ['감성'],
        'timeline'     => [
            ['time' => '0:00', 'title' => '인트로: 일본 소도시 선택법', 'description' => '취향별 5가지 카테고리 소개'],
            ['time' => '2:00', 'title' => '감성 & 미식: 다카마쓰', 'description' => '우동 본고장, 나오시마 예술섬, 리쓰린 공원'],
            ['time' => '5:00', 'title' => '감성 & 사진: 오카야마', 'description' => '쿠라시키 미관지구 수로, 고라쿠엔 정원'],
            ['time' => '8:00', 'title' => '자연: 요나고·돗토리', 'description' => '돗토리 사구, 코난 마을, 다이센산'],
            ['time' => '10:00', 'title' => '자연: 시즈오카', 'description' => '후지산 조망, 녹차밭, 해안 드라이브'],
            ['time' => '13:00', 'title' => '온천: 마츠야마', 'description' => '도고 온천, 노면전차, 귤의 도시'],
            ['time' => '15:00', 'title' => '온천: 사가', 'description' => '우레시노 미인 온천, 다케오 도서관, 도자기 마을'],
            ['time' => '18:00', 'title' => '역사: 가나자와', 'description' => '겐로쿠엔, 에도시대 거리, 금박 공예'],
            ['time' => '20:00', 'title' => '가벼운 여행: 기타큐슈', 'description' => '고쿠라성, 모지코 야끼카레, 사라쿠라산 야경'],
        ],
        'spots'        => [
            ['name' => '다카마쓰 (리쓰린 공원)', 'lat' => 34.3301, 'lng' => 134.0465, 'description' => '우동 본고장, 예술의 섬 나오시마 거점'],
            ['name' => '오카야마 (쿠라시키 미관지구)', 'lat' => 34.5947, 'lng' => 133.7716, 'description' => '수로 풍경, 일본 3대 정원 고라쿠엔'],
            ['name' => '돗토리 사구', 'lat' => 35.5403, 'lng' => 134.2289, 'description' => '거대 모래 언덕, 코난 마을 인근'],
            ['name' => '시즈오카', 'lat' => 34.9756, 'lng' => 138.3828, 'description' => '후지산 조망, 녹차밭과 해안선'],
            ['name' => '마츠야마 (도고 온천)', 'lat' => 33.8520, 'lng' => 132.7874, 'description' => '일본 최고(最古) 온천, 지브리 감성'],
            ['name' => '사가 (우레시노 온천)', 'lat' => 33.0923, 'lng' => 130.0458, 'description' => '미인 온천, 다케오 도서관, 도자기 마을'],
            ['name' => '가나자와 (겐로쿠엔)', 'lat' => 36.5613, 'lng' => 136.6562, 'description' => '작은 교토, 에도시대 거리, 금박 공예'],
            ['name' => '기타큐슈 (모지코)', 'lat' => 33.9462, 'lng' => 130.9614, 'description' => '고쿠라성, 모지코 레트로, 사라쿠라산 야경'],
        ],
    ],

    // ────────────────────────────────────────────
    // 4. 큰손 노희영 - 강원도 영월 럭셔리 소도시
    // ────────────────────────────────────────────
    [
        'title'        => '서울 근교 럭셔리 소도시 여행 | 강원도 영월, 한옥 스테이와 단종의 역사',
        'excerpt'      => '최고급 한옥 호텔을 거점으로 영월의 나물밥, 옹심이, 닭강정 미식과 단종의 유배지 청령포, 한반도 지형까지. 휴식과 인문학이 있는 럭셔리 소도시 여행.',
        'content'      => '<p>서울에서 차로 2시간, 강원도 영월은 \'아만 리조트\'에 비견되는 한옥 호텔 \'더 한옥 헤리티지 하우스\'가 여행의 이유가 되는 곳입니다. 선돌이 보이는 뷰와 수영장, 임금님 수라상 컨셉의 12첩 반상까지 갖춘 이 숙소에서 여행이 시작됩니다.</p>
<p>영월의 미식은 소박하지만 깊습니다. 제철 나물밥 전문점 \'산속의 친구들\', 한옥 카페 \'팔괴리\'의 옥수수 아이스크림, 투명하고 쫄깃한 감자 옹심이, 그리고 서부시장의 메밀 전병과 명물 닭강정까지. 강원도 식재료의 정수를 맛볼 수 있습니다.</p>
<p>영월은 단종의 유배지이기도 합니다. 삼면이 강으로 둘러싸인 고립된 섬 청령포, 소박하지만 슬픈 사연의 장릉까지. 단순한 관광을 넘어 역사적 성찰이 있는 여행입니다. 젊은달 와이파크의 현대미술과 한반도 지형의 절경도 빠뜨릴 수 없는 볼거리입니다.</p>',
        'youtube_id'   => 'Rs6KqKhL_7k',
        'channel_name' => '큰손 노희영',
        'channel_url'  => 'https://www.youtube.com/@bighandroh',
        'duration'     => '',
        'destinations' => [
            ['name' => '한국', 'slug' => 'korea', 'parent' => ''],
            ['name' => '영월', 'slug' => 'yeongwol', 'parent' => 'korea'],
        ],
        'vlog_cats'    => ['감성'],
        'timeline'     => [
            ['time' => '0:00', 'title' => '영월 도착 & 숙소', 'description' => '더 한옥 헤리티지 하우스, 선돌 뷰 수영장'],
            ['time' => '3:00', 'title' => '임금님 수라상', 'description' => '호텔 12첩 반상, 서리태 콩물 아침'],
            ['time' => '6:00', 'title' => '산속의 친구들', 'description' => '나물밥 전문점, 어수리·곤드레·직접 만든 두부'],
            ['time' => '9:00', 'title' => '카페 팔괴리 & 영월 옹심이', 'description' => '한옥 카페 옥수수 아이스크림, 감자 옹심이·팥죽'],
            ['time' => '12:00', 'title' => '서부시장', 'description' => '메밀 전병, 순대, 영월 명물 닭강정'],
            ['time' => '14:00', 'title' => '젊은달 와이파크', 'description' => '현대미술 복합문화공간, 붉은 대나무 설치미술'],
            ['time' => '17:00', 'title' => '한반도 지형', 'description' => '전망대까지 15분 도보, 한반도 모양 절경'],
            ['time' => '20:00', 'title' => '장릉 & 청령포', 'description' => '단종의 묘, 삼면이 강인 유배지, 관음송'],
        ],
        'spots'        => [
            ['name' => '더 한옥 헤리티지 하우스', 'lat' => 37.1700, 'lng' => 128.4500, 'description' => '선돌 뷰 럭셔리 한옥 호텔, 12첩 반상'],
            ['name' => '산속의 친구들', 'lat' => 37.1830, 'lng' => 128.4560, 'description' => '나물밥 전문점, 직접 만든 두부·고추장'],
            ['name' => '서부시장', 'lat' => 37.1835, 'lng' => 128.4557, 'description' => '메밀 전병, 순대, 영월 닭강정'],
            ['name' => '젊은달 와이파크', 'lat' => 37.1594, 'lng' => 128.4176, 'description' => '조형·현대미술 복합문화공간'],
            ['name' => '한반도 지형', 'lat' => 37.2219, 'lng' => 128.5503, 'description' => '강물 침식으로 형성된 한반도 모양 절경'],
            ['name' => '장릉', 'lat' => 37.1891, 'lng' => 128.4770, 'description' => '단종의 묘, 엄흥도가 수습한 소박한 왕릉'],
            ['name' => '청령포', 'lat' => 37.1808, 'lng' => 128.4442, 'description' => '단종 유배지, 삼면이 강인 육지 속 섬'],
        ],
    ],

    // ────────────────────────────────────────────
    // 5. Mei Time - 레트로 도쿄 (퍼펙트 데이즈 성지순례)
    // ────────────────────────────────────────────
    [
        'title'        => '레트로 도쿄 3박 4일 | 퍼펙트 데이즈 성지순례, 헌책방 거리, 야나카 골목 산책',
        'excerpt'      => '진보초 고서점가, 퍼펙트 데이즈 촬영지, 야나카의 옛 정취, 시모키타자와 빈티지까지. 골목 산책과 건축 감상에 최적화된 감성 도쿄 여행.',
        'content'      => '<p>이번 도쿄 여행의 테마는 "레트로 도쿄와 영화 속 산책"입니다. 세계적인 고서점 거리 진보초에서 시작해, 영화 &lt;퍼펙트 데이즈&gt;의 촬영지인 스미다의 자판기와 신사, 아사쿠사의 일본 최고(最古) 지하상가를 탐방합니다.</p>
<p>야나카 긴자는 전쟁과 지진을 피해 에도·쇼와 시대의 모습이 그대로 남은 곳입니다. 유야케 단단(저녁노을 계단)에서 시장으로 내려가 1938년 개업한 카야바 커피의 달걀 샌드위치를 맛보고, 주민들이 지켜낸 히말라야 삼나무 아래를 거닙니다. 롯폰기에서는 &lt;너의 이름은&gt; 배경인 국립신미술관과 안도 타다오의 21_21 디자인 사이트를 봅니다.</p>
<p>마지막 날은 시모키타자와에서 빈티지 쇼핑과 홋카이도 수프 카레, 맥주와 책의 서점 B&amp;B를 즐기고, 도쿄도청 무료 전망대에서 야경을 보며 마무리합니다.</p>',
        'youtube_id'   => 'Xqwu0Wx4fIQ',
        'channel_name' => 'Mei Time',
        'channel_url'  => 'https://www.youtube.com/@Meitimeyt',
        'duration'     => '',
        'destinations' => [
            ['name' => '일본', 'slug' => 'japan', 'parent' => ''],
            ['name' => '도쿄', 'slug' => 'tokyo', 'parent' => 'japan'],
        ],
        'vlog_cats'    => ['감성', '혼자여행'],
        'timeline'     => [
            ['time' => '0:00', 'title' => 'Day1: 도쿄역 도착', 'description' => '마루노우치 붉은 벽돌 역사, 유럽풍 돔 천장'],
            ['time' => '3:00', 'title' => '진보초 고서점 거리', 'description' => '보헤미안스 길드, 공유 서점'],
            ['time' => '6:00', 'title' => '퍼펙트 데이즈 성지순례', 'description' => '타카기 신사, 캔커피 자판기, 스카이트리 골목'],
            ['time' => '9:00', 'title' => '아사쿠사 레트로', 'description' => '관광안내소 전망대, 일본 최고(最古) 지하상가'],
            ['time' => '12:00', 'title' => 'Day2: 야나카 긴자', 'description' => '유야케 단단, 고양이 굿즈, 전통 반찬 가게'],
            ['time' => '15:00', 'title' => '카야바 커피 & 야나카 골목', 'description' => '1938년 카페, 달걀 샌드위치, 히말라야 삼나무'],
            ['time' => '18:00', 'title' => '롯폰기 예술 산책', 'description' => '국립신미술관, 21_21 디자인 사이트'],
            ['time' => '21:00', 'title' => 'Day3: 시모키타자와', 'description' => '리로드, 수프 카레, 보너스 트랙, B&B 서점'],
            ['time' => '24:00', 'title' => '도쿄도청 야경', 'description' => '무료 전망대, 프로젝션 매핑 쇼'],
        ],
        'spots'        => [
            ['name' => '도쿄역 마루노우치', 'lat' => 35.6812, 'lng' => 139.7671, 'description' => '붉은 벽돌 역사, 유럽풍 돔 천장'],
            ['name' => '진보초 고서점 거리', 'lat' => 35.6960, 'lng' => 139.7577, 'description' => '세계적 고서점가, 보헤미안스 길드'],
            ['name' => '타카기 신사', 'lat' => 35.7105, 'lng' => 139.8130, 'description' => '오니기리 테마 신사, 퍼펙트 데이즈 촬영지'],
            ['name' => '아사쿠사 지하상점가', 'lat' => 35.7115, 'lng' => 139.7952, 'description' => '일본 최고(最古) 지하상가, 레트로 식당'],
            ['name' => '야나카 긴자', 'lat' => 35.7270, 'lng' => 139.7680, 'description' => '유야케 단단, 에도·쇼와 시대 상점가'],
            ['name' => '카야바 커피', 'lat' => 35.7225, 'lng' => 139.7707, 'description' => '1938년 개업, 다다미 방 달걀 샌드위치'],
            ['name' => '국립신미술관', 'lat' => 35.6653, 'lng' => 139.7262, 'description' => '물결 유리 외관, 너의 이름은 배경'],
            ['name' => '시모키타자와', 'lat' => 35.6614, 'lng' => 139.6680, 'description' => '리로드, 보너스 트랙, B&B 서점'],
            ['name' => '도쿄도청 전망대', 'lat' => 35.6896, 'lng' => 139.6922, 'description' => '무료 전망대, 도쿄 야경'],
        ],
    ],

    // ────────────────────────────────────────────
    // 6. Mei Time - 교토 근교 (오하라·요괴마을·미야마)
    // ────────────────────────────────────────────
    [
        'title'        => '교토 근교 시골 여행 | 오하라 산젠인, 요괴 마을, 미야마 초가지붕 마을',
        'excerpt'      => '교토 시내를 벗어나 1,200년 사찰 산젠인의 이끼 정원, 요괴 퍼레이드, 그리고 살아있는 문화유산 미야마 초가지붕 마을까지. 일본의 원풍경을 만나는 슬로우 트래블.',
        'content'      => '<p>교토 시내의 인파를 벗어나 버스로 1시간, 오하라는 가을 단풍이 시작되는 조용한 산골입니다. 1,200년 역사의 산젠인은 이끼 정원과 표정 풍부한 지장보살 석상이 신비로운 풍경을 만들어내며, 사찰 앞 상점가에서는 통오이 장아찌와 현지 채소 정식을 맛볼 수 있습니다.</p>
<p>오후에는 옛 풍경을 잃어버린 요괴들이 이사 와 산다는 컨셉의 요괴 마을에서 해질 무렵의 퍼레이드를 체험합니다. 교탄바 지역의 검은콩과 말차 맥주가 이 지역만의 특산물입니다.</p>
<p>둘째 날은 미야마의 카야부키노사토. 사람들이 실제로 거주하는 초가지붕 가옥들이 보존된 \'살아있는 문화유산\'입니다. 무인 매점의 청유자, 아지트 같은 쪽 염색 박물관, 계란 직판장 카페까지. 강물 소리를 들으며 걷는 일본 원풍경 속 슬로우 트래블입니다.</p>',
        'youtube_id'   => 'd8WWRZ54fGM',
        'channel_name' => 'Mei Time',
        'channel_url'  => 'https://www.youtube.com/@Meitimeyt',
        'duration'     => '',
        'destinations' => [
            ['name' => '일본', 'slug' => 'japan', 'parent' => ''],
            ['name' => '교토', 'slug' => 'kyoto', 'parent' => 'japan'],
        ],
        'vlog_cats'    => ['감성', '혼자여행'],
        'timeline'     => [
            ['time' => '0:00', 'title' => 'Day1: 오하라 도착', 'description' => '교토역에서 버스 1시간, 가을 산골 마을'],
            ['time' => '3:00', 'title' => '산젠인', 'description' => '1,200년 사찰, 이끼 정원, 지장보살 석상'],
            ['time' => '6:00', 'title' => '오하라 점심', 'description' => '통오이 장아찌, 현지 채소 정식, 유자 드레싱'],
            ['time' => '9:00', 'title' => '요괴 마을', 'description' => '해질 무렵 요괴 퍼레이드, 옛 풍경 컨셉 행사'],
            ['time' => '12:00', 'title' => '저녁 & 시음회', 'description' => '말차 맥주, 교탄바 검은콩, 덴푸라 정식'],
            ['time' => '15:00', 'title' => 'Day2: 미야마 초가지붕 마을', 'description' => '카야부키노사토, 무인 매점 청유자'],
            ['time' => '18:00', 'title' => '브런치 & 쪽 염색 박물관', 'description' => '폭신 계란 요리, Little Indigo Museum'],
            ['time' => '21:00', 'title' => '카페 & 마무리', 'description' => '계란 직판장 카페, 언덕 위 신사 참배'],
        ],
        'spots'        => [
            ['name' => '산젠인', 'lat' => 35.1204, 'lng' => 135.8347, 'description' => '1,200년 역사, 이끼 정원과 지장보살 석상'],
            ['name' => '오하라 상점가', 'lat' => 35.1195, 'lng' => 135.8335, 'description' => '통오이 장아찌, 현지 채소 정식'],
            ['name' => '요괴 마을 (교탄바)', 'lat' => 35.1744, 'lng' => 135.4483, 'description' => '요괴 퍼레이드, 검은콩 특산물'],
            ['name' => '미야마 카야부키노사토', 'lat' => 35.2847, 'lng' => 135.6192, 'description' => '초가지붕 마을, 살아있는 문화유산'],
            ['name' => '쪽 염색 박물관', 'lat' => 35.2840, 'lng' => 135.6180, 'description' => 'Little Indigo Museum, 쪽 염색 공방·전시'],
        ],
    ],

    // ────────────────────────────────────────────
    // 7. Sam and Victor - 히로시마 (G7 호텔·미야지마·평화공원)
    // ────────────────────────────────────────────
    [
        'title'        => '히로시마 2박 3일 | G7 호텔 호캉스, 미야지마 미식, 평화공원 그리고 감성 쇼핑',
        'excerpt'      => 'G7 정상회의 호텔에서 호캉스, 미야지마 굴 미슐랭과 모미지 만주, 오리즈루 타워 일몰, 평화 기념 공원까지. 럭셔리와 역사가 어우러진 히로시마 여행.',
        'content'      => '<p>히로시마 여행의 시작은 G7 정상회의가 열렸던 그랜드 프린스 호텔입니다. G7 클럽 플로어 오션뷰 객실에서 클럽 라운지 칵테일과 굴 요리 뷔페를 즐기며 여유로운 첫날을 보냅니다.</p>
<p>둘째 날은 호텔 앞 선착장에서 고속선으로 26분, 미야지마 당일치기입니다. 바다 위 이쓰쿠시마 신사의 붉은 도리이를 보고, 미슐랭 등재 굴 전문점에서 점심, 모미지 만주 자판기와 BEAMS 한정판까지. 저녁에는 히로시마 시내로 돌아와 오리즈루 타워에서 원폭 돔 너머의 일몰을 감상하고, 히로시마식 오코노미야키와 오더메이드 칵테일 바를 즐깁니다.</p>
<p>마지막 날은 평화 기념 공원과 박물관에서 전쟁의 참상과 평화의 메시지를 되새긴 뒤, 연필 전문점, 북유럽 편집샵, 포켓몬 스토어 등 히로시마의 감각적인 가게들을 둘러보며 마무리합니다.</p>',
        'youtube_id'   => 'pxxAeLZ2Q7E',
        'channel_name' => 'Sam and Victor',
        'channel_url'  => 'https://www.youtube.com/@SamandVictor',
        'duration'     => '',
        'destinations' => [
            ['name' => '일본', 'slug' => 'japan', 'parent' => ''],
            ['name' => '히로시마', 'slug' => 'hiroshima', 'parent' => 'japan'],
        ],
        'vlog_cats'    => ['커플여행', '감성'],
        'timeline'     => [
            ['time' => '0:00', 'title' => 'Day1: 히로시마 도착 & 호캉스', 'description' => '그랜드 프린스 호텔, G7 클럽 플로어 오션뷰'],
            ['time' => '3:00', 'title' => '클럽 라운지', 'description' => 'G7 테이블 전시, 칵테일, 굴 요리 뷔페'],
            ['time' => '6:00', 'title' => 'Day2: 미야지마 이쓰쿠시마 신사', 'description' => '고속선 26분, 바다 위 붉은 도리이'],
            ['time' => '9:00', 'title' => '미야지마 미식', 'description' => '미슐랭 굴 전문점, 모미지 만주, 신사 라떼'],
            ['time' => '12:00', 'title' => '미야지마 쇼핑', 'description' => 'BEAMS 한정판, 샤모지 기념품, 지브리 샵'],
            ['time' => '15:00', 'title' => '오리즈루 타워 일몰', 'description' => '원폭 돔 뷰 전망대, 미끄럼틀 하산'],
            ['time' => '18:00', 'title' => '히로시마 야식', 'description' => '히로시마식 오코노미야키, 오더메이드 칵테일 바'],
            ['time' => '21:00', 'title' => 'Day3: 평화 기념 공원', 'description' => '원폭 돔, 평화의 불꽃, 박물관'],
            ['time' => '24:00', 'title' => '감성 쇼핑 & 마무리', 'description' => '연필 전문점, 편집샵, 포켓몬 스토어'],
        ],
        'spots'        => [
            ['name' => '그랜드 프린스 호텔 히로시마', 'lat' => 34.3694, 'lng' => 132.4775, 'description' => 'G7 정상회의 호텔, 클럽 플로어 오션뷰'],
            ['name' => '이쓰쿠시마 신사', 'lat' => 34.2960, 'lng' => 132.3198, 'description' => '바다 위 붉은 도리이, 미야지마 랜드마크'],
            ['name' => '미야지마 상점가', 'lat' => 34.2979, 'lng' => 132.3195, 'description' => '미슐랭 굴 전문점, 모미지 만주, BEAMS'],
            ['name' => '오리즈루 타워', 'lat' => 34.3951, 'lng' => 132.4530, 'description' => '원폭 돔 뷰 전망대, 일몰 명소'],
            ['name' => '히로시마 평화 기념 공원', 'lat' => 34.3915, 'lng' => 132.4536, 'description' => '원폭 돔, 평화의 불꽃, 위령비, 박물관'],
            ['name' => '히로시마역 포켓몬 스토어', 'lat' => 34.3983, 'lng' => 132.4752, 'description' => '히로시마 한정 피카츄, 붉은 갸라도스'],
        ],
    ],

    // ────────────────────────────────────────────
    // 8. Mei Time - 도야마 (바다·스시·유리미술관)
    // ────────────────────────────────────────────
    [
        'title'        => '도야마 1박 2일 | 초여름 바다와 흰새우 스시, 세계에서 가장 아름다운 스타벅스',
        'excerpt'      => '비가 멎는 역 아마하라시 해안, 히미 어항의 갓 경매된 해산물, 제철 흰새우 스시, 이끼 숲 사찰, 쿠마 켄고의 유리 미술관까지. 바다·산·예술이 어우러진 도야마 소도시 여행.',
        'content'      => '<p>도야마 여행은 낭만적인 이름의 아마하라시역에서 시작됩니다. 해안을 따라 걸으며 도야마만 건너편 다테야마 연봉을 바라보고, 히미 어항 2층 식당에서 갓 경매된 해산물로 점심을 먹습니다. 오후에는 렌터카로 외딴 산속 사찰을 찾아 이끼 덮인 절벽과 시냇물 속에서 마음을 정화합니다.</p>
<p>도야마 시내로 돌아오면 노면 전차가 오가는 풍경이 반깁니다. 저녁은 도야마 여행의 꽃, 이 시기 제철인 흰새우(시로에비)와 은빛 생선이 올라간 초밥으로 사치스러운 시간을 보냅니다.</p>
<p>둘째 날 아침은 2008년 스토어 디자인 어워드 대상, \'세계에서 가장 아름다운 스타벅스\'로 알려진 후간 운하 환수 공원의 스타벅스에서 시작합니다. 여행의 마무리는 쿠마 켄고 설계의 도야마 유리 미술관. 나무와 유리가 어우러진 건축과 섬세한 유리 공예 작품이 도야마의 마지막 인상을 남깁니다.</p>',
        'youtube_id'   => 'O0Mui60YG8A',
        'channel_name' => 'Mei Time',
        'channel_url'  => 'https://www.youtube.com/@Meitimeyt',
        'duration'     => '',
        'destinations' => [
            ['name' => '일본', 'slug' => 'japan', 'parent' => ''],
            ['name' => '도야마', 'slug' => 'toyama', 'parent' => 'japan'],
        ],
        'vlog_cats'    => ['감성', '혼자여행'],
        'timeline'     => [
            ['time' => '0:00', 'title' => 'Day1: 아마하라시 해안', 'description' => '비가 멎는 역, 다테야마 연봉 조망, 도라에몽 굿즈'],
            ['time' => '3:00', 'title' => '히미 어항 점심', 'description' => '1층 어시장, 2층 식당 갓 경매 해산물'],
            ['time' => '6:00', 'title' => '산속 사찰 & 이끼 숲', 'description' => '폭포 수행 사찰, 이끼 절벽, 시냇물'],
            ['time' => '9:00', 'title' => '도야마 시내 & 노면전차', 'description' => '복고풍~신형 트램, 시내 풍경'],
            ['time' => '12:00', 'title' => '흰새우 스시 저녁', 'description' => '제철 시로에비, 은빛 생선 초밥'],
            ['time' => '15:00', 'title' => 'Day2: 후간 운하 스타벅스', 'description' => '세계에서 가장 아름다운 스타벅스, 운하 뷰'],
            ['time' => '18:00', 'title' => '도야마 유리 미술관', 'description' => '쿠마 켄고 설계, 유리 공예 전시'],
        ],
        'spots'        => [
            ['name' => '아마하라시 해안', 'lat' => 36.7833, 'lng' => 136.9750, 'description' => '비가 멎는 역, 다테야마 연봉 조망'],
            ['name' => '히미 어항', 'lat' => 36.8563, 'lng' => 136.9828, 'description' => '어시장 + 2층 식당, 갓 경매 해산물'],
            ['name' => '도야마역', 'lat' => 36.7013, 'lng' => 137.2135, 'description' => '노면전차 풍경, 시내 거점'],
            ['name' => '후간 운하 환수 공원 스타벅스', 'lat' => 36.7053, 'lng' => 137.2178, 'description' => '세계에서 가장 아름다운 스타벅스'],
            ['name' => '도야마 유리 미술관', 'lat' => 36.6939, 'lng' => 137.2113, 'description' => '쿠마 켄고 설계, 유리의 도시 상징'],
        ],
    ],

];

// ── 포스트 생성 ──
$created = 0;

foreach ($vlogs as $vlog) {
    // 중복 체크 (youtube_id 기준)
    $existing = get_posts([
        'post_type'   => 'vlog_curation',
        'meta_key'    => '_ft_vlog_youtube_id',
        'meta_value'  => $vlog['youtube_id'],
        'numberposts' => 1,
    ]);
    if (!empty($existing)) {
        echo "⏭️ 이미 존재: {$vlog['title']}\n";
        continue;
    }

    // 포스트 생성
    $post_id = wp_insert_post([
        'post_type'    => 'vlog_curation',
        'post_title'   => $vlog['title'],
        'post_content' => $vlog['content'],
        'post_excerpt' => $vlog['excerpt'],
        'post_status'  => 'publish',
        'post_author'  => 1,
    ]);

    if (is_wp_error($post_id)) {
        echo "❌ 실패: {$vlog['title']} — {$post_id->get_error_message()}\n";
        continue;
    }

    // 메타 저장
    update_post_meta($post_id, '_ft_vlog_youtube_id', $vlog['youtube_id']);
    update_post_meta($post_id, '_ft_vlog_channel_name', $vlog['channel_name']);
    update_post_meta($post_id, '_ft_vlog_channel_url', $vlog['channel_url']);
    update_post_meta($post_id, '_ft_vlog_duration', $vlog['duration']);
    update_post_meta($post_id, '_ft_vlog_timeline', $vlog['timeline']);
    update_post_meta($post_id, '_ft_vlog_spots', $vlog['spots']);

    // 여행지 택소노미
    foreach ($vlog['destinations'] as $dest) {
        $term_id = ft_vlog_ensure_destination($dest['name'], $dest['slug'], $dest['parent']);
        if ($term_id) {
            wp_set_post_terms($post_id, [$term_id], 'destination', true);
        }
    }

    // 브이로그 카테고리
    $cat_ids = [];
    foreach ($vlog['vlog_cats'] as $cat_name) {
        $term = get_term_by('name', $cat_name, 'vlog_category');
        if ($term) {
            $cat_ids[] = $term->term_id;
        }
    }
    if ($cat_ids) {
        wp_set_post_terms($post_id, $cat_ids, 'vlog_category');
    }

    // Polylang 한국어 설정
    if (function_exists('pll_set_post_language')) {
        pll_set_post_language($post_id, 'ko');
    }

    $created++;
    echo "✅ 생성: {$vlog['title']} (ID: {$post_id})\n";
}

echo "\n📊 결과: {$created}개 브이로그 생성 완료\n";
