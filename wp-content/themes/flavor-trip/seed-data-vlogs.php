<?php
/**
 * 브이로그 큐레이션 시드 데이터
 *
 * 사용법: wp eval-file wp-content/themes/flavor-trip/seed-data-vlogs.php
 *
 * @package Flavor_Trip
 */

if (!defined('ABSPATH')) {
    echo "WordPress 환경에서 실행하세요.\n";
    exit;
}

// ── 브이로그 카테고리 생성 ──
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

// ── 여행지 확인/생성 함수 ──
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

// ── 브이로그 데이터 ──
$vlogs = [
    // 1. 오사카 혼자여행 브이로그
    [
        'title'        => '오사카 3박 4일 혼자여행 브이로그 | 도톤보리 맛집, 유니버설 스튜디오',
        'excerpt'      => '처음 혼자 떠난 오사카 여행! 도톤보리에서 타코야키 먹방, 유니버설 스튜디오 어트랙션, 신사이바시 쇼핑까지 알찬 3박 4일 일정을 담았습니다.',
        'content'      => '<p>오사카는 혼자 여행하기에 정말 좋은 도시입니다. 교통이 편리하고, 혼밥 문화가 잘 발달되어 있어서 혼자여도 전혀 어색하지 않아요.</p>
<p>이 브이로그에서는 도톤보리의 유명 맛집들을 둘러보고, 유니버설 스튜디오 재팬에서 해리포터 월드를 체험하는 모습을 담았습니다. 특히 구로몬 시장의 신선한 해산물은 꼭 드셔보세요!</p>
<p>숙소는 난바역 근처 호스텔을 이용했는데, 접근성이 매우 좋았습니다. 오사카 주유패스를 활용하면 교통비와 입장료를 절약할 수 있어요.</p>',
        'youtube_id'   => 'cOSjXrVZpGE',
        'channel_name' => '곱창킴 여행',
        'channel_url'  => 'https://www.youtube.com/@gopchangkim',
        'duration'     => '18:42',
        'destinations' => [
            ['name' => '일본', 'slug' => 'japan', 'parent' => ''],
            ['name' => '오사카', 'slug' => 'osaka', 'parent' => 'japan'],
        ],
        'vlog_cats'    => ['혼자여행', '먹방'],
        'timeline'     => [
            ['time' => '0:00', 'title' => '오사카 도착', 'description' => '간사이 공항에서 난바까지 이동'],
            ['time' => '2:15', 'title' => '도톤보리 탐방', 'description' => '글리코 간판, 거리 풍경'],
            ['time' => '5:30', 'title' => '타코야키 먹방', 'description' => '아치치혼포 vs 쿠쿠루 비교'],
            ['time' => '8:45', 'title' => '구로몬 시장', 'description' => '신선한 해산물, 와규 꼬치'],
            ['time' => '11:20', 'title' => '유니버설 스튜디오', 'description' => '해리포터 월드, 닌텐도 월드'],
            ['time' => '15:00', 'title' => '신사이바시 쇼핑', 'description' => '드럭스토어, 돈키호테'],
            ['time' => '17:30', 'title' => '야경 & 마무리', 'description' => '우메다 스카이빌딩 전망대'],
        ],
        'spots'        => [
            ['name' => '도톤보리', 'lat' => 34.6687, 'lng' => 135.5013, 'description' => '오사카의 대표 번화가, 글리코 간판'],
            ['name' => '구로몬 시장', 'lat' => 34.6631, 'lng' => 135.5066, 'description' => '오사카의 부엌, 신선한 해산물'],
            ['name' => '유니버설 스튜디오 재팬', 'lat' => 34.6654, 'lng' => 135.4323, 'description' => '해리포터·닌텐도 월드'],
            ['name' => '우메다 스카이빌딩', 'lat' => 34.7052, 'lng' => 135.4906, 'description' => '공중정원 전망대'],
        ],
    ],

    // 2. 방콕 커플여행 브이로그
    [
        'title'        => '방콕 4박 5일 커플여행 | 왓포, 카오산로드, 조드페어 야시장',
        'excerpt'      => '방콕에서 보낸 달달한 커플 여행기. 사원 투어부터 야시장 쇼핑, 루프탑 바까지 방콕의 모든 것을 담았습니다.',
        'content'      => '<p>방콕은 커플 여행지로 정말 추천합니다! 사원의 웅장한 분위기에서 찍는 커플 사진은 최고이고, 야시장에서 함께 먹는 팟타이와 망고 스티키라이스는 잊지 못할 추억이 됩니다.</p>
<p>숙소는 카오산로드 근처를 추천해요. 도보로 왕궁과 왓포를 갈 수 있고, 밤에는 카오산로드의 활기찬 분위기를 즐길 수 있습니다.</p>
<p>마지막 날 저녁은 꼭 루프탑 바에서 보내세요. 방콕의 야경과 함께하는 칵테일 한 잔은 여행의 완벽한 마무리가 됩니다.</p>',
        'youtube_id'   => 'Nk45jAfhkWs',
        'channel_name' => '여행기록 커플',
        'channel_url'  => 'https://www.youtube.com/@travelcouple',
        'duration'     => '22:15',
        'destinations' => [
            ['name' => '태국', 'slug' => 'thailand', 'parent' => ''],
            ['name' => '방콕', 'slug' => 'bangkok', 'parent' => 'thailand'],
        ],
        'vlog_cats'    => ['커플여행', '감성'],
        'timeline'     => [
            ['time' => '0:00', 'title' => '방콕 도착', 'description' => '수완나품 공항에서 호텔까지'],
            ['time' => '3:20', 'title' => '왓포 사원', 'description' => '와불상, 타이 마사지'],
            ['time' => '6:45', 'title' => '왕궁 투어', 'description' => '에메랄드 사원, 왕궁 건축물'],
            ['time' => '9:30', 'title' => '카오산로드', 'description' => '스트리트푸드, 야간 분위기'],
            ['time' => '12:00', 'title' => '조드페어 야시장', 'description' => '빈티지 쇼핑, 먹거리'],
            ['time' => '16:40', 'title' => '루프탑 바', 'description' => '방콕 야경, 칵테일'],
            ['time' => '20:00', 'title' => '여행 마무리', 'description' => '마지막 팟타이, 공항으로'],
        ],
        'spots'        => [
            ['name' => '왓포', 'lat' => 13.7468, 'lng' => 100.4927, 'description' => '거대한 와불상으로 유명한 사원'],
            ['name' => '왕궁', 'lat' => 13.7516, 'lng' => 100.4916, 'description' => '태국 왕실의 공식 거주지'],
            ['name' => '카오산로드', 'lat' => 13.7583, 'lng' => 100.4971, 'description' => '배낭여행자의 성지'],
            ['name' => '조드페어 야시장', 'lat' => 13.7633, 'lng' => 100.5385, 'description' => '트렌디한 야시장'],
        ],
    ],

    // 3. 도쿄 먹방 브이로그
    [
        'title'        => '도쿄 먹방 투어 | 츠키지, 신주쿠, 시부야 맛집 총정리',
        'excerpt'      => '도쿄에서 먹어본 음식만 20가지! 츠키지 시장 해산물부터 신주쿠 라멘, 시부야 디저트까지 도쿄 맛집을 총정리합니다.',
        'content'      => '<p>도쿄는 미식의 도시입니다. 미슐랭 별이 가장 많은 도시답게, 어디를 가든 수준 높은 음식을 만날 수 있어요.</p>
<p>이 브이로그에서는 츠키지 외시장에서 시작해 신주쿠의 오모이데요코초, 시부야의 트렌디한 카페까지 도쿄의 다양한 맛을 탐험합니다.</p>
<p>특히 츠키지의 달걀말이와 참치 덮밥, 신주쿠의 후우린지 라멘은 반드시 맛보셔야 할 메뉴입니다.</p>',
        'youtube_id'   => 'KIPqVosTfrA',
        'channel_name' => '먹방여행 TV',
        'channel_url'  => 'https://www.youtube.com/@mukbangtravel',
        'duration'     => '25:10',
        'destinations' => [
            ['name' => '일본', 'slug' => 'japan', 'parent' => ''],
            ['name' => '도쿄', 'slug' => 'tokyo', 'parent' => 'japan'],
        ],
        'vlog_cats'    => ['먹방', '가성비'],
        'timeline'     => [
            ['time' => '0:00', 'title' => '츠키지 외시장', 'description' => '아침 시장 탐방 시작'],
            ['time' => '3:30', 'title' => '참치 덮밥', 'description' => '신선한 참치 카이센동'],
            ['time' => '6:00', 'title' => '달걀말이', 'description' => '츠키지 명물 타마고야키'],
            ['time' => '8:45', 'title' => '신주쿠 라멘', 'description' => '후우린지 돈코츠 라멘'],
            ['time' => '12:20', 'title' => '오모이데요코초', 'description' => '추억의 골목, 야키토리'],
            ['time' => '16:00', 'title' => '시부야 카페', 'description' => '수플레 팬케이크, 말차 디저트'],
            ['time' => '20:30', 'title' => '이자카야 체험', 'description' => '일본식 술집 문화'],
            ['time' => '23:00', 'title' => '편의점 간식', 'description' => '일본 편의점 추천 먹거리'],
        ],
        'spots'        => [
            ['name' => '츠키지 외시장', 'lat' => 35.6654, 'lng' => 139.7707, 'description' => '도쿄 대표 수산시장'],
            ['name' => '오모이데요코초', 'lat' => 35.6938, 'lng' => 139.6984, 'description' => '신주쿠 추억의 골목'],
            ['name' => '시부야 센터가이', 'lat' => 35.6595, 'lng' => 139.6985, 'description' => '트렌디한 카페 거리'],
        ],
    ],

    // 4. 제주도 감성 브이로그
    [
        'title'        => '제주도 2박 3일 감성 브이로그 | 협재해변, 카페투어, 한라산',
        'excerpt'      => '제주도의 자연과 감성 카페를 담은 힐링 브이로그. 협재해변의 에메랄드빛 바다, 제주 감성 카페, 한라산 트레킹까지.',
        'content'      => '<p>제주도는 언제 가도 좋지만, 특히 봄가을 시즌이 최고입니다. 맑은 날씨에 에메랄드빛 바다가 더욱 빛나거든요.</p>
<p>이번 여행에서는 협재해변에서 여유로운 시간을 보내고, 제주 감성 카페들을 투어하며, 한라산 영실코스로 트레킹도 했습니다.</p>
<p>제주도 렌터카 여행의 장점은 시간에 쫓기지 않고 자유롭게 움직일 수 있다는 것이에요.</p>',
        'youtube_id'   => 'v8Fj6CXBJ-4',
        'channel_name' => '감성여행자',
        'channel_url'  => 'https://www.youtube.com/@aesthetictraveler',
        'duration'     => '16:20',
        'destinations' => [
            ['name' => '한국', 'slug' => 'korea', 'parent' => ''],
            ['name' => '제주', 'slug' => 'jeju', 'parent' => 'korea'],
        ],
        'vlog_cats'    => ['감성', '혼자여행'],
        'timeline'     => [
            ['time' => '0:00', 'title' => '제주 도착', 'description' => '렌터카 픽업, 첫인상'],
            ['time' => '2:30', 'title' => '협재해변', 'description' => '에메랄드빛 바다, 산책'],
            ['time' => '5:15', 'title' => '감성 카페 1', 'description' => '바다 뷰 카페, 시그니처 음료'],
            ['time' => '7:40', 'title' => '한라산 영실코스', 'description' => '트레킹, 윗세오름 도착'],
            ['time' => '11:00', 'title' => '감성 카페 2', 'description' => '제주 돌담 카페, 디저트'],
            ['time' => '13:30', 'title' => '성산일출봉', 'description' => '일출봉 등반, 정상 풍경'],
            ['time' => '15:00', 'title' => '마무리', 'description' => '제주 흑돼지 저녁, 여행 소감'],
        ],
        'spots'        => [
            ['name' => '협재해변', 'lat' => 33.3940, 'lng' => 126.2397, 'description' => '제주 서쪽의 아름다운 해변'],
            ['name' => '한라산 영실코스', 'lat' => 33.3547, 'lng' => 126.5167, 'description' => '한라산 등반 코스'],
            ['name' => '성산일출봉', 'lat' => 33.4612, 'lng' => 126.9405, 'description' => 'UNESCO 세계자연유산'],
        ],
    ],

    // 5. 베트남 나트랑 가성비 브이로그
    [
        'title'        => '나트랑 5박 6일 가성비 여행 | 해변, 머드스파, 야시장 완벽 가이드',
        'excerpt'      => '5박 6일 100만원 이하! 나트랑에서 즐긴 가성비 최고의 여행. 해변 리조트, 머드 스파, 야시장 해산물까지 알차게 즐겼습니다.',
        'content'      => '<p>나트랑은 가성비 해외여행지로 최근 큰 인기를 끌고 있습니다. 물가가 저렴하면서도 아름다운 해변과 다양한 액티비티를 즐길 수 있거든요.</p>
<p>이 브이로그에서는 나트랑 해변에서의 휴식, 타프바 머드 스파 체험, 야시장에서의 해산물 먹방, 그리고 빈펄랜드까지 나트랑의 하이라이트를 모두 담았습니다.</p>
<p>숙소는 해변가 4성급 리조트를 1박 5만원 대에 잡았는데, 한국에서는 상상도 못할 가격이에요.</p>',
        'youtube_id'   => '4K6Sh1tsAW4',
        'channel_name' => '가성비 트래블러',
        'channel_url'  => 'https://www.youtube.com/@budgettraveler',
        'duration'     => '20:55',
        'destinations' => [
            ['name' => '베트남', 'slug' => 'vietnam', 'parent' => ''],
            ['name' => '나트랑', 'slug' => 'nhatrang', 'parent' => 'vietnam'],
        ],
        'vlog_cats'    => ['가성비', '먹방'],
        'timeline'     => [
            ['time' => '0:00', 'title' => '나트랑 도착', 'description' => '깜라인 공항에서 시내까지'],
            ['time' => '2:45', 'title' => '해변 리조트', 'description' => '4성급 리조트 1박 5만원'],
            ['time' => '5:20', 'title' => '나트랑 해변', 'description' => '해변 산책, 파라솔 대여'],
            ['time' => '8:00', 'title' => '타프바 머드 스파', 'description' => '머드 온천, 워터파크'],
            ['time' => '11:30', 'title' => '야시장 해산물', 'description' => '랍스터, 새우, 조개구이'],
            ['time' => '14:45', 'title' => '빈펄랜드', 'description' => '케이블카, 놀이공원, 수족관'],
            ['time' => '18:00', 'title' => '쌀국수 맛집', 'description' => '현지인 추천 쌀국수'],
            ['time' => '19:30', 'title' => '경비 총정리', 'description' => '5박 6일 총 비용 공개'],
        ],
        'spots'        => [
            ['name' => '나트랑 해변', 'lat' => 12.2451, 'lng' => 109.1943, 'description' => '6km의 아름다운 해변'],
            ['name' => '타프바 머드 스파', 'lat' => 12.2350, 'lng' => 109.1456, 'description' => '머드 온천과 워터파크'],
            ['name' => '빈펄랜드', 'lat' => 12.2180, 'lng' => 109.2308, 'description' => '테마파크, 케이블카'],
            ['name' => '나트랑 야시장', 'lat' => 12.2474, 'lng' => 109.1908, 'description' => '해산물과 기념품 쇼핑'],
        ],
    ],
];

// ── 포스트 생성 ──
$created = 0;

foreach ($vlogs as $vlog) {
    // 중복 체크
    $existing = get_page_by_title($vlog['title'], OBJECT, 'vlog_curation');
    if ($existing) {
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
