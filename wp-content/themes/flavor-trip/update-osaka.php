<?php
/**
 * 오사카 가족여행 3박4일 포스트 업데이트 (Klook 제휴 링크 포함)
 * 출처: https://www.triptalk.me/ko/trip/pTa1RaIUMFEE
 *
 * wp eval-file /var/www/html/wp-content/themes/flavor-trip/update-osaka.php --allow-root
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    exit( 1 );
}

WP_CLI::log( '=== 오사카 가족여행 3박4일 포스트 업데이트 (Klook 링크) ===' );

// ── Klook AID 설정 ──
set_theme_mod( 'ft_klook_aid', '6yjZP2Ac' );
WP_CLI::log( 'Klook AID 설정 완료: 6yjZP2Ac' );

// Klook URL 헬퍼 (AID 자동 부착)
$klook = function ( $path, $label ) {
    $url = 'https://www.klook.com/ko/activity/' . $path;
    return ft_klook_link( $url, $label );
};

// ── 기존 오사카 포스트 찾기 ──
$existing = get_posts( [
    'post_type'   => 'travel_itinerary',
    'meta_key'    => '_ft_destination_name',
    'meta_value'  => '오사카',
    'numberposts' => 1,
] );

// ── Klook 링크가 포함된 본문 ──
$link_rapit   = $klook( '599-kansai-airport-namba-train-ticket-osaka/', '난카이 라피트 예약' );
$link_icoca   = $klook( '1754-icoca-ic-card-osaka/', 'ICOCA 카드 예약' );
$link_kaiyukan = $klook( '598-osaka-aquarium-kaiyukan-japan/', '가이유칸 입장권 예약' );
$link_usj     = $klook( '46604-universal-studios-japan-e-ticket-osaka-qr-code-direct-entry/', 'USJ 티켓 예약' );
$link_express = $klook( '3407-universal-studios-japan-express-pass-osaka/', '익스프레스 패스 예약' );
$link_castle  = $klook( '30110-osaka-castle-ticket/', '오사카성 입장권 예약' );
$link_pass    = $klook( '82312-amazing-pass-osaka/', '오사카 주유패스 예약' );

$content = <<<HTML
<h2>오사카 가족여행 3박 4일 완벽 가이드</h2>

오사카는 아이와 함께하는 가족여행에 최적화된 도시입니다. 난바를 중심으로 도톤보리의 화려한 네온사인과 길거리 음식, 세계 최대급 가이유칸 수족관, 슈퍼 닌텐도 월드가 있는 유니버설 스튜디오 재팬(USJ)까지 — 아이도 어른도 모두 즐길 수 있는 콘텐츠가 가득합니다.

이 일정은 초등학생 자녀를 둔 가족을 기준으로 구성했으며, 난바 지역 호텔을 거점으로 대중교통만으로 모든 일정을 소화할 수 있도록 동선을 최적화했습니다. 1945년 창업한 오코노미야키 미즈노, 오사카의 부엌 구로몬 시장, 551 호라이 부타만 등 오사카의 대표 먹거리도 빠짐없이 담았습니다.

3월 벚꽃 시즌에 방문하면 오사카성 공원의 벚꽃길까지 더해져 더욱 특별한 여행이 됩니다.

<h3>여행 하이라이트</h3>
<ul>
<li><strong>가이유칸 수족관</strong> — 고래상어가 유영하는 세계 최대급 수조, 펭귄·가오리 터치 체험 ({$link_kaiyukan})</li>
<li><strong>유니버설 스튜디오 재팬</strong> — 슈퍼 닌텐도 월드, 해리포터, 미니언 파크 ({$link_usj})</li>
<li><strong>도톤보리 & 신사이바시</strong> — 글리코 간판 포토존, 타코야키·오코노미야키 먹방</li>
<li><strong>오사카성</strong> — 도요토미 히데요시의 성, 3월 벚꽃 명소 ({$link_castle})</li>
<li><strong>구로몬 시장</strong> — 180년 전통, 참치 사시미·고베규 꼬치·딸기 대복</li>
</ul>

<h3>🎫 추천 예약 상품</h3>
<ul>
<li>{$link_rapit} — 간사이공항↔난바 34분 특급열차</li>
<li>{$link_icoca} — 간사이 전역 교통+편의점 결제</li>
<li>{$link_pass} — 교통 무제한 + 오사카성·대관람차·유람선 무료 입장</li>
<li>{$link_usj} — QR코드로 바로 입장</li>
<li>{$link_express} — USJ 대기시간 대폭 절감</li>
</ul>
HTML;

$excerpt = '아이와 함께 즐기는 오사카 가족여행 3박4일 코스. 가이유칸 수족관, 유니버설 스튜디오 재팬(USJ), 도톤보리 먹방, 오사카성까지 — 난바 중심 동선으로 구성한 완벽 가이드입니다.';

$post_data = [
    'post_title'   => '오사카 가족여행 3박 4일',
    'post_content' => $content,
    'post_excerpt' => $excerpt,
    'post_status'  => 'publish',
    'post_type'    => 'travel_itinerary',
    'post_author'  => 1,
];

if ( ! empty( $existing ) ) {
    $post_id = $existing[0]->ID;
    $post_data['ID'] = $post_id;
    wp_update_post( $post_data );
    WP_CLI::log( "기존 포스트 #{$post_id} 업데이트" );
} else {
    $post_id = wp_insert_post( $post_data );
    WP_CLI::log( "새 포스트 #{$post_id} 생성" );
}

// ── 메타 필드 ──
$meta = [
    '_ft_destination_name' => '오사카',
    '_ft_duration'         => '3박4일',
    '_ft_price_range'      => 'moderate',
    '_ft_difficulty'       => 'easy',
    '_ft_best_season'      => '3월~5월 (벚꽃 시즌)',
    '_ft_highlights'       => '가이유칸 수족관, 유니버설 스튜디오 재팬, 도톤보리, 오사카성, 구로몬 시장, 슈퍼 닌텐도 월드',
    '_ft_map_lat'          => '34.6737',
    '_ft_map_lng'          => '135.4998',
    '_ft_map_zoom'         => 12,
    '_ft_gallery'          => [],
];

foreach ( $meta as $key => $value ) {
    update_post_meta( $post_id, $key, $value );
}

// ── Klook 링크용 변수 (description/tip에서 사용) ──
$k_rapit     = $klook( '599-kansai-airport-namba-train-ticket-osaka/', '라피트 티켓 예약' );
$k_icoca     = $klook( '1754-icoca-ic-card-osaka/', 'ICOCA 예약' );
$k_kaiyukan  = $klook( '598-osaka-aquarium-kaiyukan-japan/', '입장권 예약' );
$k_ferris    = $klook( '191266-tempozan-giant-ferris-wheel/', '대관람차 예약' );
$k_santa     = $klook( '72049-osaka-bay-cruise-santa-maria/', '유람선 예약' );
$k_combo     = $klook( '20241-aquarium-ticket-nankai-airport-express-osaka/', '가이유칸+라피트 콤보' );
$k_usj       = $klook( '46604-universal-studios-japan-e-ticket-osaka-qr-code-direct-entry/', 'USJ 입장권 예약' );
$k_express   = $klook( '3407-universal-studios-japan-express-pass-osaka/', '익스프레스 패스 예약' );
$k_castle    = $klook( '30110-osaka-castle-ticket/', '입장권 예약' );
$k_pass      = $klook( '82312-amazing-pass-osaka/', '주유패스 예약' );

// ── 일자별 일정 (Day 1~4) + Klook 링크 ──
$days = [
    [
        'title'       => 'Day 1: 도착 및 난바 지역 탐방',
        'description' => "간사이공항에 도착해 난카이 라피트 특급(920엔, 약 34분)으로 난바역으로 이동합니다. ({$k_rapit}) 호텔 니코 오사카에 체크인 후 도톤보리로 출발합니다. 글리코 간판과 거대한 게 간판이 있는 화려한 네온사인 거리를 걸으며 타코야키, 이카야키 등 길거리 음식을 맛봅니다. 이어서 600m 길이의 아케이드형 쇼핑가 신사이바시스지에서 포켓몬센터, 디즈니스토어, 드럭스토어를 구경합니다. 저녁은 1945년 창업한 오코노미야키 명가 미즈노에서 바삭한 겉면과 부드러운 속이 일품인 오코노미야키를 즐깁니다.",
        'places'      => '간사이공항, 호텔 니코 오사카, 도톤보리, 신사이바시스지, 오코노미야키 미즈노',
        'tip'         => "ICOCA 교통카드를 공항에서 미리 구입하세요. ({$k_icoca}) 오코노미야키 미즈노는 대기가 길 수 있으니 오후 5시 전에 방문을 추천합니다.",
    ],
    [
        'title'       => 'Day 2: 해양생물 체험 및 항구 관광',
        'description' => "오사카 항구 지역에서 온종일 즐기는 날입니다. 가이유칸 수족관에서 고래상어가 유영하는 대형 중앙 수조를 감상하고, 14개 대수조에서 580종 3만 점의 해양생물을 관찰합니다. ({$k_kaiyukan}) 펭귄, 바다표범, 가오리 터치 체험은 아이들에게 잊지 못할 추억이 됩니다. 점심은 바로 옆 템포잔 마켓플레이스에서 타코야키와 오코노미야키를 맛봅니다. 오후에는 높이 112.5m 템포잔 대관람차(투명 곤돌라 옵션!)에 올라 오사카만 전경을 감상하고 ({$k_ferris}), 콜럼버스의 산타마리아 호를 모델로 한 유람선으로 45분간 오사카항을 둘러봅니다. ({$k_santa})",
        'places'      => '가이유칸 수족관, 템포잔 마켓플레이스, 템포잔 대관람차, 산타마리아 유람선',
        'tip'         => "가이유칸 입장료는 어른 2,700엔, 아이 1,400엔입니다. 라피트 왕복권과 세트로 구매하면 할인됩니다. ({$k_combo}) 오사카 주유패스가 있으면 대관람차와 유람선이 무료! ({$k_pass})",
    ],
    [
        'title'       => 'Day 3: 구로몬 시장 & 유니버설 스튜디오 재팬',
        'description' => "아침 일찍 오사카의 부엌 구로몬 시장에 들러 180년 전통의 시장을 구경합니다. 참치 사시미, 고베규 꼬치, 딸기 대복을 먹으며 활력을 채운 뒤 USJ로 출발합니다. ({$k_usj}) 슈퍼 닌텐도 월드에서 파워업 밴드로 코인을 수집하고 AR 기술의 마리오 카트 라이드를 체험합니다. 쿠파 성과 피치 성 포토존에서 인생샷을 남기고, 해리포터 구역에서 호그와트 성과 호그스미드 마을을 탐험합니다. 금지된 여행 라이드를 타고 버터비어로 목을 축입니다. 식사는 해리포터 구역의 삼손탕 술집이나 마리오 구역의 키노피오 카페에서 해결합니다.",
        'places'      => '구로몬 시장, 유니버설 스튜디오 재팬(USJ), 슈퍼 닌텐도 월드, 해리포터 구역',
        'tip'         => "USJ는 개장 30분 전에 도착해 입장 대기하세요. 익스프레스 패스를 구매하면 인기 어트랙션 대기시간을 대폭 줄일 수 있습니다. ({$k_express}) 슈퍼 닌텐도 월드는 입장 정리권이 필요할 수 있으니 USJ 앱을 미리 설치하세요.",
    ],
    [
        'title'       => 'Day 4: 오사카성, 쇼핑 & 귀국',
        'description' => "오전에 도요토미 히데요시가 세운 오사카성을 방문합니다. ({$k_castle}) 8층 천수각에서 오사카 시내 전경을 감상하고(엘리베이터 이용 가능), 3월에는 성 주변 벚꽃길이 장관입니다. 점심은 난바로 돌아와 551 호라이 본점에서 1945년부터 이어온 전통의 부타만(돼지고기 만두, 1개 200엔)을 맛봅니다. 포장도 가능하니 비행기 간식으로 추천합니다. 오후에는 타카시마야, 난바 파크스, 돈키호테에서 일본 과자, 화장품, 장난감 등 마지막 쇼핑을 하고, 난카이 공항급행으로 간사이공항으로 이동합니다.",
        'places'      => '오사카성, 551 호라이 난바 본점, 타카시마야, 난바 파크스, 돈키호테, 간사이공항',
        'tip'         => "오사카 주유패스가 있으면 오사카성 천수각도 무료 입장! ({$k_pass}) 국제선은 2시간 전 체크인을 권장합니다. 선물 구입은 공항보다 시내에서 미리 하는 것이 저렴합니다.",
    ],
];

update_post_meta( $post_id, '_ft_days', $days );

// ── 택소노미 연결 ──
$dest_ids = [];
$osaka_term = get_term_by( 'slug', 'osaka', 'destination' );
if ( $osaka_term ) $dest_ids[] = $osaka_term->term_id;
$japan_term = get_term_by( 'slug', 'japan', 'destination' );
if ( $japan_term ) $dest_ids[] = $japan_term->term_id;
if ( ! empty( $dest_ids ) ) {
    wp_set_object_terms( $post_id, $dest_ids, 'destination' );
}

$style_ids = [];
$family_term = get_term_by( 'slug', 'family', 'travel_style' );
if ( $family_term ) $style_ids[] = $family_term->term_id;
$food_term = get_term_by( 'slug', 'food-tour', 'travel_style' );
if ( $food_term ) $style_ids[] = $food_term->term_id;
if ( ! empty( $style_ids ) ) {
    wp_set_object_terms( $post_id, $style_ids, 'travel_style' );
}

WP_CLI::success( "오사카 가족여행 3박4일 포스트 #{$post_id} 업데이트 완료!" );
WP_CLI::log( '' );
WP_CLI::log( 'Klook 링크 삽입 위치:' );
WP_CLI::log( '  본문: 하이라이트 (가이유칸, USJ, 오사카성) + 추천 예약 섹션' );
WP_CLI::log( '  Day 1 description: 난카이 라피트 | tip: ICOCA 카드' );
WP_CLI::log( '  Day 2 description: 가이유칸, 대관람차, 유람선 | tip: 가이유칸+라피트 콤보, 주유패스' );
WP_CLI::log( '  Day 3 description: USJ 입장권 | tip: 익스프레스 패스' );
WP_CLI::log( '  Day 4 description: 오사카성 | tip: 주유패스' );
