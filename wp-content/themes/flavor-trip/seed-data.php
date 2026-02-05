<?php
/**
 * TripTalk 시드 데이터 생성 스크립트
 *
 * 실행 방법 (Docker 컨테이너 내):
 * wp eval-file /var/www/html/wp-content/themes/flavor-trip/seed-data.php --allow-root
 *
 * 강제 재실행:
 * wp eval-file /var/www/html/wp-content/themes/flavor-trip/seed-data.php --allow-root -- --force
 *
 * @package TripTalk
 */

// WP-CLI 전용
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    echo "이 스크립트는 WP-CLI에서만 실행할 수 있습니다.\n";
    exit( 1 );
}

// --force 인자 체크
$force = false;
if ( isset( $args ) && is_array( $args ) && in_array( '--force', $args, true ) ) {
    $force = true;
}
// $argv fallback
if ( ! $force && isset( $argv ) && is_array( $argv ) && in_array( '--force', $argv, true ) ) {
    $force = true;
}

// 중복 실행 방지
if ( ! $force && get_option( 'triptalk_seed_done' ) ) {
    WP_CLI::warning( '시드 데이터가 이미 생성되었습니다. 강제 재실행하려면 --force 인자를 추가하세요.' );
    return;
}

if ( $force ) {
    WP_CLI::log( '강제 재실행 모드: 기존 시드 데이터를 무시하고 새로 생성합니다.' );
}

WP_CLI::log( '=== TripTalk 시드 데이터 생성 시작 ===' );

// ─────────────────────────────────────────────
// 1. Destination 택소노미 (계층형)
// ─────────────────────────────────────────────
WP_CLI::log( '' );
WP_CLI::log( '── 1단계: Destination 택소노미 생성 ──' );

$destinations = [
    '일본'     => [
        'slug'     => 'japan',
        'children' => [
            '도쿄' => 'tokyo',
            '오사카' => 'osaka',
        ],
    ],
    '동남아시아' => [
        'slug'     => 'southeast-asia',
        'children' => [
            '방콕' => 'bangkok',
            '다낭' => 'danang',
        ],
    ],
    '유럽'     => [
        'slug'     => 'europe',
        'children' => [
            '파리' => 'paris',
        ],
    ],
    '한국'     => [
        'slug'     => 'korea',
        'children' => [
            '제주도' => 'jeju',
        ],
    ],
];

$dest_term_ids = []; // slug => term_id 매핑

foreach ( $destinations as $parent_name => $parent_data ) {
    $parent_term = term_exists( $parent_name, 'destination' );
    if ( ! $parent_term ) {
        $parent_term = wp_insert_term( $parent_name, 'destination', [ 'slug' => $parent_data['slug'] ] );
    }
    if ( is_wp_error( $parent_term ) ) {
        WP_CLI::warning( "부모 택소노미 '{$parent_name}' 생성 실패: " . $parent_term->get_error_message() );
        continue;
    }
    $parent_id = is_array( $parent_term ) ? $parent_term['term_id'] : $parent_term;
    $dest_term_ids[ $parent_data['slug'] ] = (int) $parent_id;
    WP_CLI::log( "  [+] {$parent_name} ({$parent_data['slug']})" );

    foreach ( $parent_data['children'] as $child_name => $child_slug ) {
        $child_term = term_exists( $child_name, 'destination' );
        if ( ! $child_term ) {
            $child_term = wp_insert_term( $child_name, 'destination', [
                'slug'   => $child_slug,
                'parent' => (int) $parent_id,
            ] );
        }
        if ( is_wp_error( $child_term ) ) {
            WP_CLI::warning( "  자식 택소노미 '{$child_name}' 생성 실패: " . $child_term->get_error_message() );
            continue;
        }
        $child_id = is_array( $child_term ) ? $child_term['term_id'] : $child_term;
        $dest_term_ids[ $child_slug ] = (int) $child_id;
        WP_CLI::log( "    [+] {$child_name} ({$child_slug})" );
    }
}

// ─────────────────────────────────────────────
// 2. Travel Style 택소노미 (비계층형)
// ─────────────────────────────────────────────
WP_CLI::log( '' );
WP_CLI::log( '── 2단계: Travel Style 택소노미 생성 ──' );

$travel_styles = [
    '맛집투어'   => 'food-tour',
    '자연탐방'   => 'nature',
    '문화체험'   => 'culture',
    '가성비여행' => 'budget-travel',
    '럭셔리'     => 'luxury',
    '배낭여행'   => 'backpacking',
    '가족여행'   => 'family',
    '커플여행'   => 'couple',
];

$style_term_ids = []; // name => term_id 매핑

foreach ( $travel_styles as $name => $slug ) {
    $term = term_exists( $name, 'travel_style' );
    if ( ! $term ) {
        $term = wp_insert_term( $name, 'travel_style', [ 'slug' => $slug ] );
    }
    if ( is_wp_error( $term ) ) {
        WP_CLI::warning( "  Travel Style '{$name}' 생성 실패: " . $term->get_error_message() );
        continue;
    }
    $term_id = is_array( $term ) ? $term['term_id'] : $term;
    $style_term_ids[ $name ] = (int) $term_id;
    WP_CLI::log( "  [+] {$name} ({$slug})" );
}

// ─────────────────────────────────────────────
// 3. Travel Itinerary 포스트 (6개)
// ─────────────────────────────────────────────
WP_CLI::log( '' );
WP_CLI::log( '── 3단계: Travel Itinerary 포스트 생성 ──' );

$itineraries = [
    [
        'title'       => '도쿄 3박4일 완전정복',
        'excerpt'     => '도쿄의 핵심 명소와 숨은 맛집을 모두 담은 알찬 3박4일 여행 코스입니다.',
        'content'     => '도쿄는 전통과 현대가 공존하는 매력적인 도시입니다. 이 일정은 시부야, 하라주쿠, 아사쿠사 등 도쿄의 대표 관광지는 물론, 현지인들이 즐겨 찾는 골목 맛집까지 알차게 구성했습니다. 츠키지 외시장에서 신선한 해산물을 맛보고, 아키하바라에서 일본 서브컬처를 체험하며, 메이지 신궁에서 고즈넉한 분위기를 즐겨보세요. 저녁에는 신주쿠 오모이데 요코초에서 현지식 이자카야 문화를 경험할 수 있습니다.',
        'dest_slugs'  => [ 'tokyo', 'japan' ],
        'styles'      => [ '맛집투어', '문화체험' ],
        'meta'        => [
            '_ft_destination_name' => '도쿄',
            '_ft_duration'         => '3박4일',
            '_ft_price_range'      => 'moderate',
            '_ft_difficulty'       => 'easy',
            '_ft_best_season'      => '3월~5월, 9월~11월',
            '_ft_highlights'       => '시부야 스크램블, 츠키지 시장, 아사쿠사 센소지, 하라주쿠 다케시타도리',
            '_ft_map_lat'          => '35.6762',
            '_ft_map_lng'          => '139.6503',
            '_ft_map_zoom'         => 12,
            '_ft_gallery'          => [],
        ],
        'days'        => [
            [
                'title'       => '도쿄 도착 & 시부야 탐험',
                'description' => '나리타/하네다 공항에서 도쿄 시내로 이동합니다. 체크인 후 시부야 스크램블 교차로와 하치코 동상을 방문하고, 시부야 스카이 전망대에서 도쿄 야경을 감상합니다.',
                'places'      => '시부야 스크램블, 시부야 스카이, 하치코 동상',
                'tip'         => '스이카/파스모 교통카드를 공항에서 미리 구매하세요.',
            ],
            [
                'title'       => '아사쿠사 & 아키하바라',
                'description' => '아침 일찍 아사쿠사 센소지를 방문하고 나카미세도리에서 간식을 즐깁니다. 오후에는 아키하바라에서 전자상가와 애니메이션 문화를 체험합니다.',
                'places'      => '센소지, 나카미세도리, 아키하바라 전자상가',
                'tip'         => '센소지는 오전 6시부터 개방되니 이른 아침 방문이 한적합니다.',
            ],
            [
                'title'       => '하라주쿠 & 신주쿠',
                'description' => '메이지 신궁 산책 후 하라주쿠 다케시타도리에서 트렌디한 쇼핑을 즐깁니다. 저녁에는 신주쿠 오모이데 요코초에서 야키토리와 맥주를 맛봅니다.',
                'places'      => '메이지 신궁, 다케시타도리, 신주쿠 오모이데 요코초',
                'tip'         => '오모이데 요코초는 좁은 골목이니 현금을 준비하세요.',
            ],
            [
                'title'       => '츠키지 & 귀국',
                'description' => '아침 일찍 츠키지 외시장에서 신선한 스시와 해산물을 맛봅니다. 마지막 쇼핑 후 공항으로 이동합니다.',
                'places'      => '츠키지 외시장, 긴자 쇼핑거리',
                'tip'         => '츠키지 시장은 오전 5시부터 영업하는 가게가 많습니다.',
            ],
        ],
    ],
    [
        'title'       => '오사카 먹방 투어 2박3일',
        'excerpt'     => '일본 최고의 먹거리 도시 오사카에서 즐기는 가성비 먹방 여행입니다.',
        'content'     => '오사카는 "천하의 부엌"이라 불릴 만큼 일본 최고의 미식 도시입니다. 도톤보리의 화려한 네온사인 아래에서 타코야키와 오코노미야키를 맛보고, 신세카이에서 쿠시카츠를 즐기며, 쿠로몬 시장에서 신선한 해산물을 경험해보세요. 오사카성과 텐노지 동물원 등 관광 명소도 함께 돌아볼 수 있는 알찬 2박3일 코스입니다. 가성비 좋은 맛집 위주로 구성하여 부담 없이 즐길 수 있습니다.',
        'dest_slugs'  => [ 'osaka', 'japan' ],
        'styles'      => [ '맛집투어', '가성비여행' ],
        'meta'        => [
            '_ft_destination_name' => '오사카',
            '_ft_duration'         => '2박3일',
            '_ft_price_range'      => 'budget',
            '_ft_difficulty'       => 'easy',
            '_ft_best_season'      => '3월~5월, 10월~11월',
            '_ft_highlights'       => '도톤보리, 쿠로몬 시장, 오사카성, 신세카이 쿠시카츠',
            '_ft_map_lat'          => '34.6937',
            '_ft_map_lng'          => '135.5023',
            '_ft_map_zoom'         => 12,
            '_ft_gallery'          => [],
        ],
        'days'        => [
            [
                'title'       => '오사카 도착 & 도톤보리',
                'description' => '간사이 공항에서 난바역으로 이동합니다. 도톤보리 거리를 걸으며 글리코 사인을 배경으로 사진을 찍고, 타코야키와 오코노미야키를 맛봅니다.',
                'places'      => '도톤보리, 글리코 사인, 호젠지 골목',
                'tip'         => '난카이 라피트 특급을 이용하면 공항에서 34분 만에 난바에 도착합니다.',
            ],
            [
                'title'       => '쿠로몬 시장 & 오사카성',
                'description' => '아침에 쿠로몬 시장에서 신선한 회와 과일을 즐기고, 오후에는 오사카성 천수각에 올라 시내 전경을 감상합니다. 저녁에는 신세카이에서 쿠시카츠를 맛봅니다.',
                'places'      => '쿠로몬 시장, 오사카성, 신세카이',
                'tip'         => '쿠로몬 시장은 일요일에 쉬는 가게가 많으니 평일 방문을 추천합니다.',
            ],
            [
                'title'       => '텐노지 & 귀국',
                'description' => '텐노지 동물원과 아베노하루카스 전망대를 방문합니다. 마지막으로 난바에서 쇼핑 후 공항으로 이동합니다.',
                'places'      => '텐노지 동물원, 아베노하루카스, 난바 쇼핑거리',
                'tip'         => '아베노하루카스 전망대는 일몰 시간에 맞춰 방문하면 환상적입니다.',
            ],
        ],
    ],
    [
        'title'       => '방콕 자유여행 4박5일',
        'excerpt'     => '가성비 최고의 동남아 여행지 방콕에서 즐기는 배낭여행 코스입니다.',
        'content'     => '방콕은 화려한 사원, 활기찬 시장, 저렴하고 맛있는 길거리 음식이 가득한 도시입니다. 왓 프라깨우와 왕궁의 황금빛 건축물에 감탄하고, 카오산 로드에서 배낭여행자들의 자유로운 분위기를 느끼며, 짜뚜짝 주말시장에서 쇼핑을 즐겨보세요. 차오프라야 강변의 야경은 잊지 못할 추억이 됩니다. 태국 전통 마사지로 여행의 피로를 풀고, 팟타이와 똠얌꿍으로 미각을 만족시키는 완벽한 4박5일 여정입니다.',
        'dest_slugs'  => [ 'bangkok', 'southeast-asia' ],
        'styles'      => [ '배낭여행', '맛집투어' ],
        'meta'        => [
            '_ft_destination_name' => '방콕',
            '_ft_duration'         => '4박5일',
            '_ft_price_range'      => 'budget',
            '_ft_difficulty'       => 'easy',
            '_ft_best_season'      => '11월~2월',
            '_ft_highlights'       => '왓 프라깨우, 카오산 로드, 짜뚜짝 시장, 차오프라야 강 야경',
            '_ft_map_lat'          => '13.7563',
            '_ft_map_lng'          => '100.5018',
            '_ft_map_zoom'         => 12,
            '_ft_gallery'          => [],
        ],
        'days'        => [
            [
                'title'       => '방콕 도착 & 카오산 로드',
                'description' => '수완나품 공항에서 시내로 이동합니다. 카오산 로드에서 체크인 후 길거리 음식과 야시장을 즐기며 방콕의 첫날 밤을 보냅니다.',
                'places'      => '카오산 로드, 람부뜨리 거리',
                'tip'         => '공항 택시보다 에어포트 레일링크 + BTS가 저렴하고 빠릅니다.',
            ],
            [
                'title'       => '왕궁 & 왓 포',
                'description' => '아침 일찍 왕궁과 왓 프라깨우(에메랄드 사원)를 관람합니다. 인접한 왓 포에서 거대한 와불상을 보고 전통 태국 마사지를 받습니다.',
                'places'      => '왕궁, 왓 프라깨우, 왓 포',
                'tip'         => '왕궁 방문 시 긴 바지와 어깨를 가리는 복장을 착용하세요.',
            ],
            [
                'title'       => '짜뚜짝 시장 & 쇼핑',
                'description' => '주말이라면 짜뚜짝 시장에서 쇼핑을 즐기고, 평일이라면 MBK 센터와 시암 스퀘어를 방문합니다. 저녁에는 아시아티크 야시장을 구경합니다.',
                'places'      => '짜뚜짝 시장, 시암 스퀘어, 아시아티크',
                'tip'         => '짜뚜짝 시장은 토·일요일만 열리니 일정을 맞춰주세요.',
            ],
            [
                'title'       => '차오프라야 강 & 왓 아룬',
                'description' => '수상버스를 타고 차오프라야 강을 따라 이동하며 왓 아룬(새벽 사원)을 방문합니다. 오후에는 탈랏 롯파이 기차 야시장을 즐깁니다.',
                'places'      => '왓 아룬, 차오프라야 강, 탈랏 롯파이',
                'tip'         => '왓 아룬은 일몰 직전에 방문하면 황금빛으로 빛나는 모습을 볼 수 있습니다.',
            ],
            [
                'title'       => '마사지 & 귀국',
                'description' => '마지막 날 아침 태국 전통 마사지로 여행의 피로를 풀고, 면세점에서 쇼핑 후 공항으로 이동합니다.',
                'places'      => '왓 포 마사지 스쿨, 킹파워 면세점',
                'tip'         => '귀국 전 공항 면세점보다 시내 킹파워가 더 저렴합니다.',
            ],
        ],
    ],
    [
        'title'       => '다낭 & 호이안 힐링 3박4일',
        'excerpt'     => '베트남 중부의 아름다운 해변과 고풍스러운 구시가지를 즐기는 힐링 여행입니다.',
        'content'     => '다낭과 호이안은 베트남 중부의 보석 같은 여행지입니다. 미케 비치에서 여유로운 해변 시간을 보내고, 오행산 대리석 동굴을 탐험하며, 호이안 구시가지의 알록달록한 등불 아래를 산책해보세요. 바나힐의 골든 브릿지에서 인생샷을 남기고, 현지 시장에서 반미와 까오라우를 맛보는 것도 빼놓을 수 없는 경험입니다. 커플 여행에 특히 추천하는 로맨틱한 3박4일 코스입니다.',
        'dest_slugs'  => [ 'danang', 'southeast-asia' ],
        'styles'      => [ '커플여행', '자연탐방' ],
        'meta'        => [
            '_ft_destination_name' => '다낭',
            '_ft_duration'         => '3박4일',
            '_ft_price_range'      => 'moderate',
            '_ft_difficulty'       => 'easy',
            '_ft_best_season'      => '2월~5월',
            '_ft_highlights'       => '미케 비치, 호이안 구시가지, 바나힐 골든 브릿지, 오행산',
            '_ft_map_lat'          => '16.0544',
            '_ft_map_lng'          => '108.2022',
            '_ft_map_zoom'         => 12,
            '_ft_gallery'          => [],
        ],
        'days'        => [
            [
                'title'       => '다낭 도착 & 미케 비치',
                'description' => '다낭 공항에서 호텔로 이동합니다. 오후에는 미케 비치에서 해수욕과 일광욕을 즐기고, 저녁에는 한강 다리의 드래곤 브릿지 야경을 감상합니다.',
                'places'      => '미케 비치, 드래곤 브릿지, 한 시장',
                'tip'         => '드래곤 브릿지는 주말 밤 9시에 불쇼를 합니다.',
            ],
            [
                'title'       => '바나힐 종일 투어',
                'description' => '바나힐 테마파크에서 골든 브릿지를 배경으로 사진을 찍고, 프랑스 마을과 놀이공원을 즐깁니다. 케이블카에서 바라보는 경치가 압권입니다.',
                'places'      => '바나힐, 골든 브릿지, 프랑스 마을',
                'tip'         => '오전 일찍 방문하면 인파를 피할 수 있습니다.',
            ],
            [
                'title'       => '호이안 구시가지',
                'description' => '오행산 대리석 동굴을 탐험한 후 호이안으로 이동합니다. 구시가지에서 등불 축제 분위기를 느끼며 투본 강에서 소원 등불을 띄워봅니다.',
                'places'      => '오행산, 호이안 구시가지, 내원교, 투본 강',
                'tip'         => '호이안은 매달 음력 14일에 등불 축제가 열립니다.',
            ],
            [
                'title'       => '호이안 자유시간 & 귀국',
                'description' => '아침에 호이안 중앙시장에서 까오라우와 반미를 맛보고 자유시간을 보냅니다. 오후에 다낭 공항으로 이동하여 귀국합니다.',
                'places'      => '호이안 중앙시장, 안방 비치',
                'tip'         => '호이안 맞춤 의상은 24시간 내 완성되니 첫날 주문하세요.',
            ],
        ],
    ],
    [
        'title'       => '파리 문화예술 5박6일',
        'excerpt'     => '예술과 낭만의 도시 파리에서 즐기는 프리미엄 문화 여행입니다.',
        'content'     => '파리는 세계 예술과 문화의 중심지입니다. 루브르 박물관에서 모나리자를 만나고, 에펠탑에서 파리 전경을 감상하며, 샹젤리제 거리를 거닐어보세요. 몽마르트르 언덕의 사크레쾨르 대성당에서 일몰을 바라보고, 세느 강 유람선에서 로맨틱한 저녁을 보내는 것도 특별한 경험입니다. 베르사유 궁전 당일치기와 마레 지구의 트렌디한 카페 투어까지 포함한 알찬 5박6일 코스입니다.',
        'dest_slugs'  => [ 'paris', 'europe' ],
        'styles'      => [ '문화체험', '럭셔리' ],
        'meta'        => [
            '_ft_destination_name' => '파리',
            '_ft_duration'         => '5박6일',
            '_ft_price_range'      => 'premium',
            '_ft_difficulty'       => 'moderate',
            '_ft_best_season'      => '4월~6월, 9월~10월',
            '_ft_highlights'       => '에펠탑, 루브르 박물관, 몽마르트르, 베르사유 궁전, 세느 강 유람선',
            '_ft_map_lat'          => '48.8566',
            '_ft_map_lng'          => '2.3522',
            '_ft_map_zoom'         => 12,
            '_ft_gallery'          => [],
        ],
        'days'        => [
            [
                'title'       => '파리 도착 & 에펠탑',
                'description' => '샤를 드골 공항에서 시내로 이동합니다. 에펠탑 전망대에 올라 파리 시내를 한눈에 감상하고, 트로카데로 광장에서 에펠탑 야경 사진을 찍습니다.',
                'places'      => '에펠탑, 트로카데로 광장, 샹 드 마르스 공원',
                'tip'         => '에펠탑 티켓은 최소 2주 전에 온라인으로 예매하세요.',
            ],
            [
                'title'       => '루브르 & 튈르리 정원',
                'description' => '루브르 박물관에서 모나리자, 밀로의 비너스 등 걸작들을 감상합니다. 오후에는 튈르리 정원을 산책하고 콩코르드 광장을 방문합니다.',
                'places'      => '루브르 박물관, 튈르리 정원, 콩코르드 광장',
                'tip'         => '수요일과 금요일에는 루브르가 밤 9시 45분까지 연장 운영합니다.',
            ],
            [
                'title'       => '몽마르트르 & 사크레쾨르',
                'description' => '몽마르트르 언덕을 올라 사크레쾨르 대성당을 방문하고, 테르트르 광장에서 거리 화가들의 작품을 구경합니다. 저녁에는 물랭루즈 근처에서 식사를 즐깁니다.',
                'places'      => '사크레쾨르 대성당, 테르트르 광장, 물랭루즈',
                'tip'         => '몽마르트르는 소매치기가 많으니 소지품에 주의하세요.',
            ],
            [
                'title'       => '베르사유 궁전 당일치기',
                'description' => 'RER C선을 타고 베르사유 궁전으로 이동합니다. 거울의 방, 정원, 트리아농을 둘러봅니다. 저녁에 파리로 돌아와 마레 지구에서 식사합니다.',
                'places'      => '베르사유 궁전, 거울의 방, 마레 지구',
                'tip'         => '뮤지엄 패스를 이용하면 줄을 서지 않고 입장할 수 있습니다.',
            ],
            [
                'title'       => '세느 강 & 오르세 미술관',
                'description' => '오르세 미술관에서 인상파 작품들을 감상합니다. 오후에는 생제르맹 데 프레 거리의 카페에서 여유를 즐기고, 저녁에는 세느 강 유람선 디너 크루즈를 즐깁니다.',
                'places'      => '오르세 미술관, 생제르맹 데 프레, 세느 강 유람선',
                'tip'         => '유람선 디너 크루즈는 사전 예약이 필수입니다.',
            ],
            [
                'title'       => '샹젤리제 & 귀국',
                'description' => '샹젤리제 거리에서 마지막 쇼핑을 즐기고, 개선문에 올라 파리의 방사형 도로를 감상합니다. 오후에 공항으로 이동합니다.',
                'places'      => '샹젤리제 거리, 개선문, 갤러리 라파예트',
                'tip'         => '면세 쇼핑은 갤러리 라파예트에서 한 번에 해결하세요.',
            ],
        ],
    ],
    [
        'title'       => '제주도 가족여행 2박3일',
        'excerpt'     => '아이와 함께 즐기는 제주도 자연 속 가족 여행 코스입니다.',
        'content'     => '제주도는 온 가족이 함께 즐길 수 있는 최고의 국내 여행지입니다. 성산일출봉에서 장엄한 일출을 감상하고, 만장굴에서 신비로운 용암 동굴을 탐험하며, 천지연 폭포의 시원한 물줄기를 느껴보세요. 아이들이 좋아하는 에코랜드 기차 여행과 감귤 따기 체험도 포함했습니다. 제주 흑돼지 구이와 해물뚝배기로 제주만의 맛을 즐기고, 협재 해수욕장의 에메랄드빛 바다에서 가족 사진을 남기는 알찬 2박3일 코스입니다.',
        'dest_slugs'  => [ 'jeju', 'korea' ],
        'styles'      => [ '가족여행', '자연탐방' ],
        'meta'        => [
            '_ft_destination_name' => '제주도',
            '_ft_duration'         => '2박3일',
            '_ft_price_range'      => 'moderate',
            '_ft_difficulty'       => 'easy',
            '_ft_best_season'      => '4월~6월, 9월~10월',
            '_ft_highlights'       => '성산일출봉, 만장굴, 협재 해수욕장, 천지연 폭포, 에코랜드',
            '_ft_map_lat'          => '33.4996',
            '_ft_map_lng'          => '126.5312',
            '_ft_map_zoom'         => 10,
            '_ft_gallery'          => [],
        ],
        'days'        => [
            [
                'title'       => '제주 도착 & 동쪽 코스',
                'description' => '제주 공항에서 렌터카를 수령합니다. 성산일출봉을 등반하고, 섭지코지 해변을 산책합니다. 저녁에는 성산 근처에서 해물뚝배기를 맛봅니다.',
                'places'      => '성산일출봉, 섭지코지, 만장굴',
                'tip'         => '성산일출봉은 오전에 방문해야 역광을 피할 수 있습니다.',
            ],
            [
                'title'       => '에코랜드 & 서쪽 코스',
                'description' => '에코랜드에서 숲속 기차를 타고 자연을 즐깁니다. 오후에는 협재 해수욕장에서 물놀이를 하고, 한림공원을 방문합니다. 저녁에는 제주 흑돼지 구이를 즐깁니다.',
                'places'      => '에코랜드, 협재 해수욕장, 한림공원',
                'tip'         => '협재 해수욕장은 수심이 얕아 아이들에게 안전합니다.',
            ],
            [
                'title'       => '천지연 폭포 & 귀국',
                'description' => '천지연 폭포를 방문하고, 서귀포 올레시장에서 감귤 아이스크림과 흑돼지 꼬치를 맛봅니다. 감귤 따기 체험 후 공항으로 이동합니다.',
                'places'      => '천지연 폭포, 서귀포 올레시장, 감귤 체험농장',
                'tip'         => '귀국일에는 제주시 방면 숙소를 잡으면 공항 이동이 편리합니다.',
            ],
        ],
    ],
];

foreach ( $itineraries as $i => $itinerary ) {
    $post_id = wp_insert_post( [
        'post_title'   => $itinerary['title'],
        'post_content' => $itinerary['content'],
        'post_excerpt' => $itinerary['excerpt'],
        'post_status'  => 'publish',
        'post_type'    => 'travel_itinerary',
        'post_author'  => 1,
    ] );

    if ( is_wp_error( $post_id ) ) {
        WP_CLI::warning( "  일정 '{$itinerary['title']}' 생성 실패: " . $post_id->get_error_message() );
        continue;
    }

    // 메타 필드
    foreach ( $itinerary['meta'] as $key => $value ) {
        update_post_meta( $post_id, $key, $value );
    }

    // 일자별 일정 리피터
    update_post_meta( $post_id, '_ft_days', $itinerary['days'] );

    // Destination 택소노미
    $dest_ids = [];
    foreach ( $itinerary['dest_slugs'] as $slug ) {
        if ( isset( $dest_term_ids[ $slug ] ) ) {
            $dest_ids[] = $dest_term_ids[ $slug ];
        }
    }
    if ( ! empty( $dest_ids ) ) {
        wp_set_object_terms( $post_id, $dest_ids, 'destination' );
    }

    // Travel Style 택소노미
    $style_ids = [];
    foreach ( $itinerary['styles'] as $name ) {
        if ( isset( $style_term_ids[ $name ] ) ) {
            $style_ids[] = $style_term_ids[ $name ];
        }
    }
    if ( ! empty( $style_ids ) ) {
        wp_set_object_terms( $post_id, $style_ids, 'travel_style' );
    }

    WP_CLI::log( "  [+] #{$post_id} {$itinerary['title']}" );
}

// ─────────────────────────────────────────────
// 4. 일반 블로그 포스트 (3개)
// ─────────────────────────────────────────────
WP_CLI::log( '' );
WP_CLI::log( '── 4단계: 블로그 포스트 생성 ──' );

// 카테고리 생성
$cat_tip = term_exists( '여행 팁', 'category' );
if ( ! $cat_tip ) {
    $cat_tip = wp_insert_term( '여행 팁', 'category', [ 'slug' => 'travel-tips' ] );
}
$cat_tip_id = is_array( $cat_tip ) ? $cat_tip['term_id'] : $cat_tip;

$cat_guide = term_exists( '여행 가이드', 'category' );
if ( ! $cat_guide ) {
    $cat_guide = wp_insert_term( '여행 가이드', 'category', [ 'slug' => 'travel-guide' ] );
}
$cat_guide_id = is_array( $cat_guide ) ? $cat_guide['term_id'] : $cat_guide;

$blog_posts = [
    [
        'title'    => '여행 짐 싸기 체크리스트: 가벼운 캐리어를 위한 팁',
        'excerpt'  => '효율적인 짐 싸기로 여행을 더 편하게 만드는 실전 팁을 소개합니다.',
        'content'  => '여행 짐 싸기는 항상 고민되는 부분입니다. 먼저 여행 기간과 목적지의 날씨를 확인하고, 꼭 필요한 물건만 챙기는 것이 핵심입니다. 옷은 3일치만 준비하고 현지에서 세탁하는 것을 추천합니다. 압축팩을 활용하면 부피를 절반으로 줄일 수 있습니다. 세면도구는 여행용 미니 사이즈로 준비하고, 전자기기는 멀티 충전기 하나로 통일하세요. 중요 서류는 원본과 사본을 분리 보관하고, 여권 사진은 스마트폰에 저장해두면 응급 상황에 유용합니다. 기내 반입 가방에는 장시간 비행에 필요한 목베개, 안대, 이어폰, 간식을 챙기세요. 마지막으로 빈 접이식 가방을 하나 넣어두면 쇼핑한 물건을 담아올 때 매우 유용합니다.',
        'category' => (int) $cat_tip_id,
    ],
    [
        'title'    => '해외여행 필수 앱 5가지 추천',
        'excerpt'  => '해외여행을 더 스마트하게 만들어주는 필수 모바일 앱을 소개합니다.',
        'content'  => '스마트폰 하나면 해외여행이 한결 편해집니다. 첫 번째로 구글 번역은 카메라로 간판을 비추면 실시간 번역이 되어 현지 메뉴판을 읽을 때 필수입니다. 두 번째, 구글 맵은 오프라인 지도를 미리 다운로드하면 데이터 없이도 길을 찾을 수 있습니다. 세 번째로 XE Currency는 실시간 환율 계산기로 현지에서 합리적인 소비를 도와줍니다. 네 번째, TripIt은 항공편, 호텔, 렌터카 예약을 한곳에서 관리할 수 있어 일정 관리가 편합니다. 마지막으로 Grab이나 Bolt 같은 현지 택시 앱은 동남아 여행에서 바가지 요금을 피하는 데 큰 도움이 됩니다. 출발 전에 미리 설치하고 계정을 만들어두면 현지에서 바로 사용할 수 있습니다.',
        'category' => (int) $cat_tip_id,
    ],
    [
        'title'    => '초보 배낭여행자를 위한 동남아 루트 가이드',
        'excerpt'  => '처음 배낭여행을 떠나는 분들을 위한 동남아 추천 루트와 예산 가이드입니다.',
        'content'  => '동남아시아는 배낭여행 입문자에게 최적의 지역입니다. 물가가 저렴하고, 교통이 발달해 있으며, 영어 소통이 비교적 수월합니다. 추천 루트로는 방콕에서 시작하여 치앙마이로 이동하고, 라오스 루앙프라방을 거쳐 베트남 하노이까지 가는 인도차이나 루트가 인기입니다. 2주 기준 항공 포함 150~200만 원이면 충분합니다. 숙소는 호스텔 도미토리를 이용하면 1박 1~2만 원으로 해결 가능합니다. 이동은 야간 버스를 활용하면 숙박비를 아끼면서 시간도 절약할 수 있습니다. 여행자 보험은 필수이며, 신용카드는 비자와 마스터카드 두 장을 준비하세요. 현지 SIM 카드는 공항에서 구매하는 것이 가장 편리합니다.',
        'category' => (int) $cat_guide_id,
    ],
];

foreach ( $blog_posts as $post_data ) {
    $post_id = wp_insert_post( [
        'post_title'   => $post_data['title'],
        'post_content' => $post_data['content'],
        'post_excerpt' => $post_data['excerpt'],
        'post_status'  => 'publish',
        'post_type'    => 'post',
        'post_author'  => 1,
        'post_category' => [ $post_data['category'] ],
    ] );

    if ( is_wp_error( $post_id ) ) {
        WP_CLI::warning( "  블로그 포스트 '{$post_data['title']}' 생성 실패: " . $post_id->get_error_message() );
        continue;
    }

    WP_CLI::log( "  [+] #{$post_id} {$post_data['title']}" );
}

// ─────────────────────────────────────────────
// 5. 고정 페이지 (2개)
// ─────────────────────────────────────────────
WP_CLI::log( '' );
WP_CLI::log( '── 5단계: 고정 페이지 생성 ──' );

$pages = [
    [
        'title'   => '소개',
        'slug'    => 'about',
        'content' => 'TripTalk에 오신 것을 환영합니다! 저희는 직접 경험한 여행 일정과 맛집 정보를 공유하는 여행 블로그입니다. 전 세계 다양한 여행지의 실속 있는 코스를 소개하고, 여행자들이 더 쉽고 즐겁게 여행을 계획할 수 있도록 돕고 있습니다. 현지인처럼 여행하고 싶다면 TripTalk과 함께하세요.',
    ],
    [
        'title'   => '문의하기',
        'slug'    => 'contact',
        'content' => "문의사항이 있으시면 아래 방법으로 연락해주세요.\n\n이메일: hello@triptalk.kr\n인스타그램: @triptalk_kr\n\n광고, 협업, 여행 코스 제안 등 모든 문의를 환영합니다. 보통 24시간 이내에 답변드립니다.",
    ],
];

foreach ( $pages as $page_data ) {
    // 기존 페이지 체크
    $existing = get_page_by_path( $page_data['slug'] );
    if ( $existing ) {
        WP_CLI::log( "  [=] '{$page_data['title']}' 페이지가 이미 존재합니다 (#{$existing->ID})" );
        continue;
    }

    $page_id = wp_insert_post( [
        'post_title'   => $page_data['title'],
        'post_content' => $page_data['content'],
        'post_name'    => $page_data['slug'],
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_author'  => 1,
    ] );

    if ( is_wp_error( $page_id ) ) {
        WP_CLI::warning( "  페이지 '{$page_data['title']}' 생성 실패: " . $page_id->get_error_message() );
        continue;
    }

    WP_CLI::log( "  [+] #{$page_id} {$page_data['title']} (/{$page_data['slug']})" );
}

// ─────────────────────────────────────────────
// 완료 플래그 저장
// ─────────────────────────────────────────────
update_option( 'triptalk_seed_done', true );

WP_CLI::log( '' );
WP_CLI::success( '시드 데이터 생성이 완료되었습니다!' );
WP_CLI::log( '' );
WP_CLI::log( '=== 생성된 콘텐츠 요약 ===' );
WP_CLI::log( "  Destination 택소노미: " . count( $dest_term_ids ) . "개" );
WP_CLI::log( "  Travel Style 택소노미: " . count( $style_term_ids ) . "개" );
WP_CLI::log( "  여행 일정 포스트: " . count( $itineraries ) . "개" );
WP_CLI::log( "  블로그 포스트: " . count( $blog_posts ) . "개" );
WP_CLI::log( "  고정 페이지: " . count( $pages ) . "개" );
WP_CLI::log( '' );
WP_CLI::log( '검증 명령어:' );
WP_CLI::log( '  wp post list --post_type=travel_itinerary --allow-root' );
WP_CLI::log( '  wp post list --post_type=post --allow-root' );
WP_CLI::log( '  wp post list --post_type=page --allow-root' );
WP_CLI::log( '  wp term list destination --allow-root' );
WP_CLI::log( '  wp term list travel_style --allow-root' );
