<?php
/**
 * Flavor Trip 다국어 콘텐츠 시드 데이터
 *
 * 사용법: wp eval-file seed-data-multilingual.php
 *
 * 주의: Polylang 플러그인이 먼저 설치 및 설정되어 있어야 합니다.
 *
 * @package Flavor_Trip
 */

if (!defined('ABSPATH')) {
    exit;
}

// Polylang 활성화 확인
if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
    echo "Error: Polylang 플러그인이 활성화되어 있지 않습니다.\n";
    echo "먼저 Polylang을 설치하고 언어를 설정하세요.\n";
    return;
}

define('KLOOK_AID', '6yjZP2Ac');

/**
 * Klook URL 생성 (언어별)
 */
function klook_url_ml($activity_id, $lang = 'ko') {
    $lang_codes = [
        'ko' => 'ko',
        'en' => 'en-US',
        'zh' => 'zh-CN',
        'ja' => 'ja',
    ];
    $code = $lang_codes[$lang] ?? 'en-US';
    return "https://www.klook.com/{$code}/activity/{$activity_id}/?aid=" . KLOOK_AID;
}

/**
 * 여행지 번역 데이터
 */
$destination_translations = [
    '제주도' => ['en' => 'Jeju Island', 'zh' => '济州岛', 'ja' => '済州島'],
    '일본' => ['en' => 'Japan', 'zh' => '日本', 'ja' => '日本'],
    '태국' => ['en' => 'Thailand', 'zh' => '泰国', 'ja' => 'タイ'],
    '베트남' => ['en' => 'Vietnam', 'zh' => '越南', 'ja' => 'ベトナム'],
    '프랑스' => ['en' => 'France', 'zh' => '法国', 'ja' => 'フランス'],
    '싱가포르' => ['en' => 'Singapore', 'zh' => '新加坡', 'ja' => 'シンガポール'],
    '홍콩' => ['en' => 'Hong Kong', 'zh' => '香港', 'ja' => '香港'],
    '인도네시아' => ['en' => 'Indonesia', 'zh' => '印度尼西亚', 'ja' => 'インドネシア'],
    '대만' => ['en' => 'Taiwan', 'zh' => '台湾', 'ja' => '台湾'],
    '필리핀' => ['en' => 'Philippines', 'zh' => '菲律宾', 'ja' => 'フィリピン'],
    '미국' => ['en' => 'USA', 'zh' => '美国', 'ja' => 'アメリカ'],
    '호주' => ['en' => 'Australia', 'zh' => '澳大利亚', 'ja' => 'オーストラリア'],
    '스페인' => ['en' => 'Spain', 'zh' => '西班牙', 'ja' => 'スペイン'],
    '이탈리아' => ['en' => 'Italy', 'zh' => '意大利', 'ja' => 'イタリア'],
    '스위스' => ['en' => 'Switzerland', 'zh' => '瑞士', 'ja' => 'スイス'],
];

/**
 * 여행 스타일 번역 데이터
 */
$style_translations = [
    '자연' => ['en' => 'Nature', 'zh' => '自然', 'ja' => '自然'],
    '맛집' => ['en' => 'Food', 'zh' => '美食', 'ja' => 'グルメ'],
    '가족여행' => ['en' => 'Family', 'zh' => '家庭游', 'ja' => '家族旅行'],
    '문화탐방' => ['en' => 'Culture', 'zh' => '文化', 'ja' => '文化'],
    '힐링' => ['en' => 'Relaxation', 'zh' => '休闲', 'ja' => 'ヒーリング'],
    '도시여행' => ['en' => 'City', 'zh' => '城市', 'ja' => '都市'],
    '쇼핑' => ['en' => 'Shopping', 'zh' => '购物', 'ja' => 'ショッピング'],
    '액티비티' => ['en' => 'Activity', 'zh' => '活动', 'ja' => 'アクティビティ'],
    '역사' => ['en' => 'History', 'zh' => '历史', 'ja' => '歴史'],
    '예술' => ['en' => 'Art', 'zh' => '艺术', 'ja' => 'アート'],
    '미식' => ['en' => 'Gourmet', 'zh' => '美食', 'ja' => '美食'],
    '로맨틱' => ['en' => 'Romantic', 'zh' => '浪漫', 'ja' => 'ロマンチック'],
    '모험' => ['en' => 'Adventure', 'zh' => '冒险', 'ja' => 'アドベンチャー'],
    '비치' => ['en' => 'Beach', 'zh' => '海滩', 'ja' => 'ビーチ'],
    '휴양' => ['en' => 'Resort', 'zh' => '度假', 'ja' => 'リゾート'],
    '배낭여행' => ['en' => 'Backpacking', 'zh' => '背包游', 'ja' => 'バックパック'],
    '럭셔리' => ['en' => 'Luxury', 'zh' => '奢华', 'ja' => 'ラグジュアリー'],
];

/**
 * 샘플 일정 번역 데이터 (오사카 가족 여행)
 */
$itinerary_translations = [
    'osaka-family' => [
        'ko' => [
            'title' => '오사카 가족 여행 4박 5일',
            'excerpt' => '유니버설 스튜디오, 오사카 성, 도톤보리까지! 아이와 함께 즐기는 오사카 완전 정복 코스',
            'content' => '<p>오사카는 가족 여행지로 최고의 선택입니다. 테마파크, 맛집, 쇼핑을 모두 즐길 수 있어 모든 가족 구성원이 만족할 수 있습니다.</p>',
        ],
        'en' => [
            'title' => 'Osaka Family Trip: 5 Days 4 Nights',
            'excerpt' => 'Universal Studios, Osaka Castle, Dotonbori! Complete Osaka itinerary for families with kids',
            'content' => '<p>Osaka is the perfect choice for family travel. With theme parks, great food, and shopping, every family member will be satisfied.</p>',
        ],
        'zh' => [
            'title' => '大阪家庭游 5天4夜',
            'excerpt' => '环球影城、大阪城、道顿堀！带孩子畅游大阪完美攻略',
            'content' => '<p>大阪是家庭旅行的最佳选择。这里有主题公园、美食和购物，能满足每位家庭成员的需求。</p>',
        ],
        'ja' => [
            'title' => '大阪家族旅行 4泊5日',
            'excerpt' => 'ユニバーサルスタジオ、大阪城、道頓堀まで！子供と一緒に楽しむ大阪完全攻略コース',
            'content' => '<p>大阪は家族旅行に最適な場所です。テーマパーク、グルメ、ショッピングすべてを楽しめ、家族全員が満足できます。</p>',
        ],
        'days' => [
            'en' => [
                [
                    'title' => 'Arrival & Dotonbori Night',
                    'summary' => 'Arrival at Kansai Airport → Shinsaibashi Hotel → Dotonbori dinner',
                    'spots' => [
                        ['time' => '15:00', 'type' => 'place', 'name' => 'Kansai Airport', 'description' => 'Buy JR Kansai Pass for family discounts'],
                        ['time' => '18:00', 'type' => 'restaurant', 'name' => 'Dotonbori', 'cuisine' => 'Street Food', 'menu' => 'Takoyaki, Okonomiyaki'],
                    ],
                ],
                [
                    'title' => 'Universal Studios Japan',
                    'summary' => 'Full day at Universal Studios Japan with Express Pass',
                    'spots' => [
                        ['time' => '09:00', 'type' => 'place', 'name' => 'Universal Studios Japan', 'description' => 'Harry Potter, Super Nintendo World, and more', 'link' => klook_url_ml('691-universal-studios-japan-osaka', 'en')],
                    ],
                ],
            ],
            'zh' => [
                [
                    'title' => '抵达 & 道顿堀之夜',
                    'summary' => '抵达关西机场 → 心斋桥酒店 → 道顿堀晚餐',
                    'spots' => [
                        ['time' => '15:00', 'type' => 'place', 'name' => '关西机场', 'description' => '购买JR关西周游券享受家庭折扣'],
                        ['time' => '18:00', 'type' => 'restaurant', 'name' => '道顿堀', 'cuisine' => '街头美食', 'menu' => '章鱼烧、大阪烧'],
                    ],
                ],
                [
                    'title' => '日本环球影城',
                    'summary' => '使用快速通行证畅玩日本环球影城',
                    'spots' => [
                        ['time' => '09:00', 'type' => 'place', 'name' => '日本环球影城', 'description' => '哈利波特、超级任天堂世界等', 'link' => klook_url_ml('691-universal-studios-japan-osaka', 'zh')],
                    ],
                ],
            ],
            'ja' => [
                [
                    'title' => '到着＆道頓堀の夜',
                    'summary' => '関西空港到着 → 心斎橋ホテル → 道頓堀で夕食',
                    'spots' => [
                        ['time' => '15:00', 'type' => 'place', 'name' => '関西空港', 'description' => 'JR関西パスで家族割引をゲット'],
                        ['time' => '18:00', 'type' => 'restaurant', 'name' => '道頓堀', 'cuisine' => 'B級グルメ', 'menu' => 'たこ焼き、お好み焼き'],
                    ],
                ],
                [
                    'title' => 'ユニバーサル・スタジオ・ジャパン',
                    'summary' => 'エクスプレスパスでUSJを満喫',
                    'spots' => [
                        ['time' => '09:00', 'type' => 'place', 'name' => 'ユニバーサル・スタジオ・ジャパン', 'description' => 'ハリーポッター、スーパーニンテンドーワールドなど', 'link' => klook_url_ml('691-universal-studios-japan-osaka', 'ja')],
                    ],
                ],
            ],
        ],
    ],
];

/**
 * 택소노미 번역 생성
 */
function create_taxonomy_translations($ko_term_id, $taxonomy, $translations) {
    global $destination_translations, $style_translations;

    $trans_data = ($taxonomy === 'destination') ? $destination_translations : $style_translations;
    $term = get_term($ko_term_id);

    if (!$term || is_wp_error($term) || !isset($trans_data[$term->name])) {
        return;
    }

    $term_trans = $trans_data[$term->name];
    $term_translations = ['ko' => $ko_term_id];

    foreach (['en', 'zh', 'ja'] as $lang) {
        if (!isset($term_trans[$lang])) continue;

        $translated_name = $term_trans[$lang];
        $existing = get_term_by('name', $translated_name, $taxonomy);

        if ($existing) {
            $term_translations[$lang] = $existing->term_id;
        } else {
            $new_term = wp_insert_term($translated_name, $taxonomy, [
                'slug' => sanitize_title($translated_name) . '-' . $lang,
            ]);
            if (!is_wp_error($new_term)) {
                pll_set_term_language($new_term['term_id'], $lang);
                $term_translations[$lang] = $new_term['term_id'];
            }
        }
    }

    // 번역 관계 설정
    pll_save_term_translations($term_translations);

    echo "  - {$term->name} 택소노미 번역 완료\n";
}

/**
 * 기존 한국어 일정의 번역 버전 생성
 */
function create_itinerary_translation($ko_post_id, $lang, $data) {
    // 원본 메타 데이터 복사
    $meta_keys = ['_ft_duration', '_ft_difficulty', '_ft_price_range', '_ft_best_season',
                  '_ft_highlights', '_ft_map_lat', '_ft_map_lng', '_ft_map_zoom', '_ft_gallery'];

    $meta_values = [];
    foreach ($meta_keys as $key) {
        $meta_values[$key] = get_post_meta($ko_post_id, $key, true);
    }

    // 번역된 포스트 생성
    $post_data = [
        'post_title'   => $data['title'],
        'post_content' => $data['content'],
        'post_excerpt' => $data['excerpt'],
        'post_status'  => 'publish',
        'post_type'    => 'travel_itinerary',
        'post_author'  => 1,
    ];

    $new_post_id = wp_insert_post($post_data);

    if (is_wp_error($new_post_id)) {
        echo "  ! 에러: {$data['title']} 생성 실패\n";
        return null;
    }

    // 메타 데이터 복사
    foreach ($meta_values as $key => $value) {
        if ($value) {
            update_post_meta($new_post_id, $key, $value);
        }
    }

    // 일정 데이터가 있으면 번역된 일정 사용
    if (!empty($data['days'])) {
        update_post_meta($new_post_id, '_ft_days', $data['days']);
    } else {
        // 원본 일정 복사
        $original_days = get_post_meta($ko_post_id, '_ft_days', true);
        if ($original_days) {
            update_post_meta($new_post_id, '_ft_days', $original_days);
        }
    }

    // 썸네일 복사
    $thumbnail_id = get_post_thumbnail_id($ko_post_id);
    if ($thumbnail_id) {
        set_post_thumbnail($new_post_id, $thumbnail_id);
    }

    // 언어 설정
    pll_set_post_language($new_post_id, $lang);

    echo "  + [{$lang}] {$data['title']} 생성 완료 (ID: {$new_post_id})\n";

    return $new_post_id;
}

/**
 * 메인 실행
 */
echo "\n=== Flavor Trip 다국어 콘텐츠 생성 ===\n\n";

// 1. 기존 택소노미 번역
echo "1. 택소노미 번역 중...\n";

$destinations = get_terms(['taxonomy' => 'destination', 'hide_empty' => false]);
foreach ($destinations as $dest) {
    // 한국어 항목만 처리 (Polylang 언어 확인)
    $term_lang = pll_get_term_language($dest->term_id);
    if ($term_lang === 'ko' || !$term_lang) {
        if (!$term_lang) pll_set_term_language($dest->term_id, 'ko');
        create_taxonomy_translations($dest->term_id, 'destination', $destination_translations);
    }
}

$styles = get_terms(['taxonomy' => 'travel_style', 'hide_empty' => false]);
foreach ($styles as $style) {
    $term_lang = pll_get_term_language($style->term_id);
    if ($term_lang === 'ko' || !$term_lang) {
        if (!$term_lang) pll_set_term_language($style->term_id, 'ko');
        create_taxonomy_translations($style->term_id, 'travel_style', $style_translations);
    }
}

// 2. 기존 여행 일정 번역
echo "\n2. 여행 일정 번역 중...\n";

$itineraries = get_posts([
    'post_type'      => 'travel_itinerary',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
]);

foreach ($itineraries as $itinerary) {
    $post_lang = pll_get_post_language($itinerary->ID);

    // 한국어 원본만 처리
    if ($post_lang !== 'ko' && $post_lang !== false) {
        continue;
    }

    // 한국어로 설정
    if (!$post_lang) {
        pll_set_post_language($itinerary->ID, 'ko');
    }

    echo "\n처리 중: {$itinerary->post_title}\n";

    // 이미 번역이 있는지 확인
    $existing_translations = pll_get_post_translations($itinerary->ID);

    // 미리 정의된 번역 데이터가 있는지 확인
    $predefined_key = null;
    foreach ($itinerary_translations as $key => $trans) {
        if (strpos(strtolower($itinerary->post_title), 'osaka') !== false ||
            strpos($itinerary->post_title, '오사카') !== false) {
            if ($key === 'osaka-family') {
                $predefined_key = $key;
                break;
            }
        }
    }

    $translations = ['ko' => $itinerary->ID];

    foreach (['en', 'zh', 'ja'] as $lang) {
        // 이미 번역이 있으면 스킵
        if (isset($existing_translations[$lang])) {
            echo "  - [{$lang}] 이미 존재 (ID: {$existing_translations[$lang]})\n";
            $translations[$lang] = $existing_translations[$lang];
            continue;
        }

        // 미리 정의된 번역 데이터 사용
        if ($predefined_key && isset($itinerary_translations[$predefined_key][$lang])) {
            $data = $itinerary_translations[$predefined_key][$lang];
            if (isset($itinerary_translations[$predefined_key]['days'][$lang])) {
                $data['days'] = $itinerary_translations[$predefined_key]['days'][$lang];
            }
        } else {
            // 기본 번역 (제목 + 언어 코드)
            $data = [
                'title' => $itinerary->post_title . " [{$lang}]",
                'excerpt' => $itinerary->post_excerpt,
                'content' => $itinerary->post_content,
                'days' => null,
            ];
        }

        $new_id = create_itinerary_translation($itinerary->ID, $lang, $data);
        if ($new_id) {
            $translations[$lang] = $new_id;

            // 택소노미 연결 (번역된 택소노미로)
            $dest_terms = wp_get_post_terms($itinerary->ID, 'destination', ['fields' => 'ids']);
            $style_terms = wp_get_post_terms($itinerary->ID, 'travel_style', ['fields' => 'ids']);

            foreach ($dest_terms as $term_id) {
                $term_trans = pll_get_term_translations($term_id);
                if (isset($term_trans[$lang])) {
                    wp_set_post_terms($new_id, [$term_trans[$lang]], 'destination', true);
                }
            }

            foreach ($style_terms as $term_id) {
                $term_trans = pll_get_term_translations($term_id);
                if (isset($term_trans[$lang])) {
                    wp_set_post_terms($new_id, [$term_trans[$lang]], 'travel_style', true);
                }
            }
        }
    }

    // 번역 관계 저장
    pll_save_post_translations($translations);
}

echo "\n=== 다국어 콘텐츠 생성 완료! ===\n";
echo "\n다음 단계:\n";
echo "1. WordPress 관리자에서 각 언어별 콘텐츠 확인\n";
echo "2. 자동 생성된 '[lang]' 접미사가 붙은 제목을 실제 번역으로 교체\n";
echo "3. 일정 상세 내용(days)도 각 언어로 번역\n";
echo "\n팁: Polylang Pro + DeepL API를 사용하면 자동 번역이 가능합니다.\n";
