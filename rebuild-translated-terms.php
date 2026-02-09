<?php
/**
 * Rebuild all translated taxonomy terms with correct hierarchy and translations
 *
 * Run: wp eval-file rebuild-translated-terms.php --allow-root
 *
 * What this does:
 * 1. Deletes ALL non-Korean destination and travel_style terms
 * 2. Re-creates them from Korean originals with:
 *    - Correct parent-child hierarchy
 *    - Manual translation overrides for problematic terms
 *    - Proper Polylang links
 *    - _ft_ko_slug meta for image mapping
 * 3. Re-assigns translated terms to translated posts
 */

// ── Config ──
$LANG_MAP = [
    'en'    => 'en',
    'zh-cn' => 'zh-CN',
    'ja'    => 'ja',
    'fr'    => 'fr',
    'de'    => 'de',
];

$DELAY_MS = 500000;

// ── Translation function ──
function gt_translate_rebuild($text, $target, $source = 'ko') {
    if (empty(trim($text))) return $text;

    $url = 'https://translate.googleapis.com/translate_a/single?'
         . http_build_query([
             'client' => 'gtx',
             'sl'     => $source,
             'tl'     => $target,
             'dt'     => 't',
             'q'      => $text,
         ]);

    $ctx = stream_context_create([
        'http' => [
            'timeout'       => 10,
            'ignore_errors' => true,
            'header'        => "User-Agent: Mozilla/5.0\r\n",
        ],
    ]);

    $response = @file_get_contents($url, false, $ctx);
    if ($response === false) return $text;

    $result = json_decode($response, true);
    if (!$result || !isset($result[0])) return $text;

    $translated = '';
    foreach ($result[0] as $part) {
        if (isset($part[0])) $translated .= $part[0];
    }
    return $translated ?: $text;
}

// ── Manual overrides for problematic translations ──
$OVERRIDES = [
    // destination
    '세부'       => ['en' => 'Cebu',         'zh-CN' => '宿务',         'ja' => 'セブ',             'fr' => 'Cebu',              'de' => 'Cebu'],
    '발리'       => ['en' => 'Bali',          'zh-CN' => '巴厘岛',       'ja' => 'バリ',             'fr' => 'Bali',              'de' => 'Bali'],
    '동아시아'   => ['en' => 'East Asia',     'zh-CN' => '东亚',         'ja' => '東アジア',         'fr' => 'Asie de l\'Est',    'de' => 'Ostasien'],
    '동남아시아' => ['en' => 'Southeast Asia', 'zh-CN' => '东南亚',       'ja' => '東南アジア',       'fr' => 'Asie du Sud-Est',   'de' => 'Südostasien'],
    '오세아니아' => ['en' => 'Oceania',       'zh-CN' => '大洋洲',       'ja' => 'オセアニア',       'fr' => 'Océanie',           'de' => 'Ozeanien'],
    '북미'       => ['en' => 'North America', 'zh-CN' => '北美',         'ja' => '北米',             'fr' => 'Amérique du Nord',  'de' => 'Nordamerika'],
    '유럽'       => ['en' => 'Europe',        'zh-CN' => '欧洲',         'ja' => 'ヨーロッパ',       'fr' => 'Europe',            'de' => 'Europa'],
    '일본'       => ['en' => 'Japan',         'zh-CN' => '日本',         'ja' => '日本',             'fr' => 'Japon',             'de' => 'Japan'],
    '한국'       => ['en' => 'Korea',         'zh-CN' => '韩国',         'ja' => '韓国',             'fr' => 'Corée',             'de' => 'Korea'],
    '도쿄'       => ['en' => 'Tokyo',         'zh-CN' => '东京',         'ja' => '東京',             'fr' => 'Tokyo',             'de' => 'Tokio'],
    '오사카'     => ['en' => 'Osaka',         'zh-CN' => '大阪',         'ja' => '大阪',             'fr' => 'Osaka',             'de' => 'Osaka'],
    '교토'       => ['en' => 'Kyoto',         'zh-CN' => '京都',         'ja' => '京都',             'fr' => 'Kyoto',             'de' => 'Kyoto'],
    '후쿠오카'   => ['en' => 'Fukuoka',       'zh-CN' => '福冈',         'ja' => '福岡',             'fr' => 'Fukuoka',           'de' => 'Fukuoka'],
    '서울'       => ['en' => 'Seoul',         'zh-CN' => '首尔',         'ja' => 'ソウル',           'fr' => 'Séoul',             'de' => 'Seoul'],
    '부산'       => ['en' => 'Busan',         'zh-CN' => '釜山',         'ja' => '釜山',             'fr' => 'Busan',             'de' => 'Busan'],
    '제주'       => ['en' => 'Jeju',          'zh-CN' => '济州岛',       'ja' => '済州島',           'fr' => 'Jeju',              'de' => 'Jeju'],
    '다낭'       => ['en' => 'Da Nang',       'zh-CN' => '岘港',         'ja' => 'ダナン',           'fr' => 'Da Nang',           'de' => 'Da Nang'],
    '방콕'       => ['en' => 'Bangkok',       'zh-CN' => '曼谷',         'ja' => 'バンコク',         'fr' => 'Bangkok',           'de' => 'Bangkok'],
    '싱가포르'   => ['en' => 'Singapore',     'zh-CN' => '新加坡',       'ja' => 'シンガポール',     'fr' => 'Singapour',         'de' => 'Singapur'],
    '타이베이'   => ['en' => 'Taipei',        'zh-CN' => '台北',         'ja' => '台北',             'fr' => 'Taipei',            'de' => 'Taipeh'],
    '홍콩'       => ['en' => 'Hong Kong',     'zh-CN' => '香港',         'ja' => '香港',             'fr' => 'Hong Kong',         'de' => 'Hongkong'],
    '시드니'     => ['en' => 'Sydney',        'zh-CN' => '悉尼',         'ja' => 'シドニー',         'fr' => 'Sydney',            'de' => 'Sydney'],
    '파리'       => ['en' => 'Paris',         'zh-CN' => '巴黎',         'ja' => 'パリ',             'fr' => 'Paris',             'de' => 'Paris'],
    '런던'       => ['en' => 'London',        'zh-CN' => '伦敦',         'ja' => 'ロンドン',         'fr' => 'Londres',           'de' => 'London'],
    '로마'       => ['en' => 'Rome',          'zh-CN' => '罗马',         'ja' => 'ローマ',           'fr' => 'Rome',              'de' => 'Rom'],
    '바르셀로나' => ['en' => 'Barcelona',     'zh-CN' => '巴塞罗那',     'ja' => 'バルセロナ',       'fr' => 'Barcelone',         'de' => 'Barcelona'],
    '뉴욕'       => ['en' => 'New York',      'zh-CN' => '纽约',         'ja' => 'ニューヨーク',     'fr' => 'New York',          'de' => 'New York'],
    '로스앤젤레스' => ['en' => 'Los Angeles',  'zh-CN' => '洛杉矶',       'ja' => 'ロサンゼルス',     'fr' => 'Los Angeles',       'de' => 'Los Angeles'],
    '하와이'     => ['en' => 'Hawaii',        'zh-CN' => '夏威夷',       'ja' => 'ハワイ',           'fr' => 'Hawaï',             'de' => 'Hawaii'],
    // travel_style
    '가성비여행' => ['en' => 'Budget Travel',    'zh-CN' => '高性价比旅行', 'ja' => 'コスパ旅行',       'fr' => 'Voyage économique',   'de' => 'Budget-Reise'],
    '가족여행'   => ['en' => 'Family Travel',    'zh-CN' => '家庭旅行',     'ja' => '家族旅行',         'fr' => 'Voyage en famille',   'de' => 'Familienreise'],
    '맛집'       => ['en' => 'Food Spots',       'zh-CN' => '美食店',       'ja' => 'グルメスポット',   'fr' => 'Bonnes adresses',     'de' => 'Gourmet-Spots'],
    '맛집투어'   => ['en' => 'Food Tour',        'zh-CN' => '美食之旅',     'ja' => 'グルメツアー',     'fr' => 'Tour gastronomique',  'de' => 'Gourmet-Tour'],
    '문화체험'   => ['en' => 'Cultural Experience','zh-CN' => '文化体验',    'ja' => '文化体験',         'fr' => 'Expérience culturelle','de' => 'Kulturerlebnis'],
    '문화탐방'   => ['en' => 'Cultural Tour',    'zh-CN' => '文化探访',     'ja' => '文化探訪',         'fr' => 'Tour culturel',       'de' => 'Kulturtour'],
    '미식여행'   => ['en' => 'Gourmet Travel',   'zh-CN' => '美食旅行',     'ja' => 'グルメ旅行',       'fr' => 'Voyage gastronomique','de' => 'Gourmet-Reise'],
    '배낭여행'   => ['en' => 'Backpacking',      'zh-CN' => '背包旅行',     'ja' => 'バックパック旅行', 'fr' => 'Voyage sac à dos',    'de' => 'Rucksackreise'],
    '커플여행'   => ['en' => 'Couple Travel',    'zh-CN' => '情侣旅行',     'ja' => 'カップル旅行',     'fr' => 'Voyage en couple',    'de' => 'Paarreise'],
    '힐링여행'   => ['en' => 'Healing Travel',   'zh-CN' => '治愈之旅',     'ja' => '癒し旅',           'fr' => 'Voyage bien-être',    'de' => 'Wellness-Reise'],
    '도시여행'   => ['en' => 'City Travel',      'zh-CN' => '城市旅行',     'ja' => '都市旅行',         'fr' => 'Voyage urbain',       'de' => 'Städtereise'],
];

function translate_name($name, $gt_lang) {
    global $OVERRIDES, $DELAY_MS;
    if (isset($OVERRIDES[$name][$gt_lang])) {
        return $OVERRIDES[$name][$gt_lang];
    }
    usleep($DELAY_MS);
    return gt_translate_rebuild($name, $gt_lang);
}

// ═══════════════════════════════════════════
echo "=== 번역 택소노미 재구축 ===\n\n";

$taxonomies = ['destination', 'travel_style'];

// ── Step 1: Delete all non-Korean terms ──
echo "--- Step 1: 기존 번역 term 삭제 ---\n";
$deleted = 0;

foreach ($taxonomies as $taxonomy) {
    $all_terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ]);
    if (is_wp_error($all_terms)) continue;

    foreach ($all_terms as $term) {
        $lang = pll_get_term_language($term->term_id);
        if ($lang && $lang !== 'ko') {
            wp_delete_term($term->term_id, $taxonomy);
            $deleted++;
        }
    }
}

echo "  삭제: {$deleted}개 term\n\n";

// ── Step 2: Collect Korean terms (parents first, then children) ──
echo "--- Step 2: 한국어 term 구조 수집 ---\n";

$ko_term_map = []; // taxonomy => [term_id => term]

foreach ($taxonomies as $taxonomy) {
    $ko_term_map[$taxonomy] = [];

    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ]);
    if (is_wp_error($terms)) continue;

    // Filter Korean terms only
    foreach ($terms as $term) {
        $lang = pll_get_term_language($term->term_id);
        if (!$lang || $lang === 'ko') {
            $ko_term_map[$taxonomy][$term->term_id] = $term;
        }
    }

    // Sort: parents first (parent=0), then children
    uasort($ko_term_map[$taxonomy], function ($a, $b) {
        return $a->parent - $b->parent;
    });

    $count = count($ko_term_map[$taxonomy]);
    echo "  {$taxonomy}: {$count}개 한국어 term\n";
    foreach ($ko_term_map[$taxonomy] as $t) {
        $parent_label = $t->parent > 0 ? " (parent:{$t->parent})" : '';
        echo "    - {$t->name} [{$t->slug}]{$parent_label}\n";
    }
}

echo "\n";

// ── Step 3: Create translated terms with hierarchy ──
echo "--- Step 3: 번역 term 생성 (계층 구조 포함) ---\n";

// Map: ko_term_id => [lang => translated_term_id]
$translation_map = [];
$created = 0;

foreach ($taxonomies as $taxonomy) {
    foreach ($ko_term_map[$taxonomy] as $ko_term_id => $ko_term) {
        $translation_map[$ko_term_id] = ['ko' => $ko_term_id];

        foreach ($LANG_MAP as $pll_slug => $gt_lang) {
            // Translate name
            $translated_name = translate_name($ko_term->name, $gt_lang);
            $translated_slug = sanitize_title($translated_name . '-' . $pll_slug);

            // Find translated parent
            $translated_parent = 0;
            if ($ko_term->parent > 0 && isset($translation_map[$ko_term->parent][$pll_slug])) {
                $translated_parent = $translation_map[$ko_term->parent][$pll_slug];
            }

            // Create term
            $new_term = wp_insert_term($translated_name, $taxonomy, [
                'slug'   => $translated_slug,
                'parent' => $translated_parent,
            ]);

            if (is_wp_error($new_term)) {
                // Try with unique slug
                $translated_slug .= '-' . wp_generate_password(4, false, false);
                $new_term = wp_insert_term($translated_name, $taxonomy, [
                    'slug'   => $translated_slug,
                    'parent' => $translated_parent,
                ]);
            }

            if (is_wp_error($new_term)) {
                echo "  ERROR: {$ko_term->name} → {$pll_slug}: {$new_term->get_error_message()}\n";
                continue;
            }

            $new_term_id = $new_term['term_id'];

            // Set language
            pll_set_term_language($new_term_id, $pll_slug);

            // Save Korean slug meta
            update_term_meta($new_term_id, '_ft_ko_slug', $ko_term->slug);

            $translation_map[$ko_term_id][$pll_slug] = $new_term_id;
            $created++;

            $parent_str = $translated_parent ? " (parent:{$translated_parent})" : '';
            echo "  [{$pll_slug}] {$ko_term->name} → {$translated_name}{$parent_str}\n";
        }

        // Link all translations via Polylang
        PLL()->model->term->save_translations($ko_term_id, $translation_map[$ko_term_id]);
    }
}

echo "\n  생성: {$created}개 term\n\n";

// ── Step 4: Re-assign translated terms to translated posts ──
echo "--- Step 4: 번역 포스트에 term 재할당 ---\n";

$post_types = ['travel_itinerary', 'post'];
$reassigned = 0;

foreach ($post_types as $post_type) {
    $ko_posts = get_posts([
        'post_type'   => $post_type,
        'numberposts' => -1,
        'post_status' => 'publish',
    ]);

    foreach ($ko_posts as $ko_post) {
        $post_lang = pll_get_post_language($ko_post->ID);
        if ($post_lang && $post_lang !== 'ko') continue;

        $post_translations = PLL()->model->post->get_translations($ko_post->ID);

        foreach ($taxonomies as $taxonomy) {
            $ko_terms = wp_get_post_terms($ko_post->ID, $taxonomy);
            if (empty($ko_terms) || is_wp_error($ko_terms)) continue;

            foreach ($post_translations as $lang => $trans_post_id) {
                if ($lang === 'ko') continue;

                $term_ids_to_set = [];
                foreach ($ko_terms as $ko_term) {
                    if (isset($translation_map[$ko_term->term_id][$lang])) {
                        $term_ids_to_set[] = $translation_map[$ko_term->term_id][$lang];
                    }
                }

                if (!empty($term_ids_to_set)) {
                    wp_set_object_terms($trans_post_id, $term_ids_to_set, $taxonomy);
                    $reassigned++;
                }
            }
        }
    }
}

echo "  재할당: {$reassigned}건\n\n";

echo "=== 재구축 완료! ===\n";
echo "삭제: {$deleted} / 생성: {$created} / 재할당: {$reassigned}\n";
