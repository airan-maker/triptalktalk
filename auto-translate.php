<?php
/**
 * Auto-translate travel itineraries and blog posts using Google Translate API (free)
 * Run: wp eval-file auto-translate.php --allow-root
 *
 * Creates translated copies of all Korean travel_itinerary and post
 * and links them via Polylang.
 */

// ── Config ──
$LANG_MAP = array(
    'en'    => 'en',
    'zh-cn' => 'zh-CN',
    'ja'    => 'ja',
    'fr'    => 'fr',
    'de'    => 'de',
);

$DELAY_MS = 500000; // 0.5s between API calls

// ── Translation function ──
function gt_translate($text, $target, $source = 'ko') {
    if (empty(trim($text))) return $text;

    $url = 'https://translate.googleapis.com/translate_a/single?'
         . http_build_query(array(
             'client' => 'gtx',
             'sl'     => $source,
             'tl'     => $target,
             'dt'     => 't',
             'q'      => $text,
         ));

    $ctx = stream_context_create(array(
        'http' => array(
            'timeout'      => 10,
            'ignore_errors' => true,
            'header'       => "User-Agent: Mozilla/5.0\r\n",
        ),
    ));

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

// Translate multiple strings individually (avoids batch separator mangling)
function gt_translate_batch($strings, $target, $source = 'ko') {
    global $DELAY_MS;

    $results = $strings;
    foreach ($strings as $i => $s) {
        if (!empty(trim($s))) {
            usleep($DELAY_MS);
            $results[$i] = gt_translate($s, $target, $source);
        }
    }

    return $results;
}

// ── Translate a single post's meta ──
function translate_itinerary_meta($post_id, $gt_lang) {
    $meta_keys_to_translate = array(
        '_ft_destination_name',
        '_ft_best_season',
        '_ft_highlights',
        '_ft_duration',
    );

    // Simple meta fields
    $strings = array();
    foreach ($meta_keys_to_translate as $key) {
        $strings[$key] = get_post_meta($post_id, $key, true) ?: '';
    }

    $translated = gt_translate_batch(array_values($strings), $gt_lang);
    $meta = array();
    $i = 0;
    foreach ($meta_keys_to_translate as $key) {
        $meta[$key] = $translated[$i];
        $i++;
    }

    // Non-translated meta (copy as-is)
    foreach (array('_ft_price_range', '_ft_difficulty', '_ft_map_lat', '_ft_map_lng', '_ft_map_zoom', '_thumbnail_id', '_ft_gallery') as $key) {
        $val = get_post_meta($post_id, $key, true);
        if ($val !== '' && $val !== false) {
            $meta[$key] = $val;
        }
    }

    // Translate days/spots
    $days = get_post_meta($post_id, '_ft_days', true);
    if (!empty($days) && is_array($days)) {
        $translated_days = translate_days($days, $gt_lang);
        $meta['_ft_days'] = $translated_days;
    }

    return $meta;
}

function translate_days($days, $gt_lang) {
    global $DELAY_MS;
    $translated_days = array();

    foreach ($days as $day) {
        // Collect day-level strings
        $day_strings = array(
            $day['title'] ?? '',
            $day['summary'] ?? '',
            $day['tip'] ?? '',
        );
        usleep($DELAY_MS);
        $day_translated = gt_translate_batch($day_strings, $gt_lang);

        $new_day = $day;
        $new_day['title']   = $day_translated[0];
        $new_day['summary'] = $day_translated[1];
        $new_day['tip']     = $day_translated[2];

        // Translate spots
        if (!empty($day['spots']) && is_array($day['spots'])) {
            $new_spots = array();
            foreach ($day['spots'] as $spot) {
                $spot_strings = array(
                    $spot['name'] ?? '',
                    $spot['description'] ?? '',
                    $spot['tip'] ?? '',
                    $spot['menu'] ?? '',
                    $spot['wait_tip'] ?? '',
                    $spot['cuisine'] ?? '',
                );
                $spot_translated = gt_translate_batch($spot_strings, $gt_lang);

                $new_spot = $spot;
                $new_spot['name']        = $spot_translated[0];
                $new_spot['description'] = $spot_translated[1];
                $new_spot['tip']         = $spot_translated[2];
                if (!empty($spot['menu']))     $new_spot['menu']     = $spot_translated[3];
                if (!empty($spot['wait_tip'])) $new_spot['wait_tip'] = $spot_translated[4];
                if (!empty($spot['cuisine'])) $new_spot['cuisine']  = $spot_translated[5];

                // Don't translate: type, time, duration, lat, lng, link, price
                $new_spots[] = $new_spot;
            }
            $new_day['spots'] = $new_spots;
        }

        $translated_days[] = $new_day;
    }

    return $translated_days;
}

// ── 수동 번역 오버라이드 (Google Translate 오역 방지) ──
function ft_get_term_overrides() {
    return [
        '세부'     => ['en' => 'Cebu',         'zh-CN' => '宿务',         'ja' => 'セブ',             'fr' => 'Cebu',              'de' => 'Cebu'],
        '맛집'     => ['en' => 'Food Spots',    'zh-CN' => '美食店',       'ja' => 'グルメスポット',   'fr' => 'Bonnes adresses',   'de' => 'Gourmet-Spots'],
        '맛집투어' => ['en' => 'Food Tour',     'zh-CN' => '美食之旅',     'ja' => 'グルメツアー',     'fr' => 'Tour gastronomique','de' => 'Gourmet-Tour'],
        '발리'     => ['en' => 'Bali',          'zh-CN' => '巴厘岛',       'ja' => 'バリ',             'fr' => 'Bali',              'de' => 'Bali'],
        '가성비여행' => ['en' => 'Budget Travel','zh-CN' => '高性价比旅行', 'ja' => 'コスパ旅行',       'fr' => 'Voyage économique', 'de' => 'Budget-Reise'],
        '미식여행' => ['en' => 'Gourmet Travel', 'zh-CN' => '美食旅行',     'ja' => 'グルメ旅行',       'fr' => 'Voyage gastronomique','de' => 'Gourmet-Reise'],
        '힐링여행' => ['en' => 'Healing Travel', 'zh-CN' => '治愈之旅',     'ja' => '癒し旅',           'fr' => 'Voyage bien-être',  'de' => 'Wellness-Reise'],
        '문화탐방' => ['en' => 'Cultural Tour',  'zh-CN' => '文化之旅',     'ja' => '文化探訪',         'fr' => 'Tour culturel',     'de' => 'Kulturtour'],
        '문화체험' => ['en' => 'Cultural Experience','zh-CN' => '文化体验',  'ja' => '文化体験',         'fr' => 'Expérience culturelle','de' => 'Kulturerlebnis'],
    ];
}

function ft_translate_term_name($name, $gt_lang) {
    $overrides = ft_get_term_overrides();
    if (isset($overrides[$name][$gt_lang])) {
        return $overrides[$name][$gt_lang];
    }
    usleep(300000);
    return gt_translate($name, $gt_lang);
}

// ── Translate taxonomy terms (계층 구조 보존) ──
function translate_and_link_terms($post_id, $new_post_id, $taxonomy, $pll_slug, $gt_lang) {
    $terms = wp_get_post_terms($post_id, $taxonomy);
    if (empty($terms) || is_wp_error($terms)) return;

    foreach ($terms as $term) {
        $translated_term_id = get_or_create_translated_term($term, $taxonomy, $pll_slug, $gt_lang);
        if ($translated_term_id) {
            wp_set_post_terms($new_post_id, array($translated_term_id), $taxonomy, true);
        }
    }
}

/**
 * 번역된 term을 찾거나 생성 (재귀적으로 부모 먼저 처리)
 */
function get_or_create_translated_term($term, $taxonomy, $pll_slug, $gt_lang) {
    // 이미 번역이 있는지 확인
    $existing_id = pll_get_term($term->term_id, $pll_slug);
    if ($existing_id) return $existing_id;

    // 부모가 있으면 부모 먼저 번역 (재귀)
    $translated_parent = 0;
    if ($term->parent > 0) {
        $parent_term = get_term($term->parent, $taxonomy);
        if ($parent_term && !is_wp_error($parent_term)) {
            $translated_parent = get_or_create_translated_term($parent_term, $taxonomy, $pll_slug, $gt_lang);
        }
    }

    // 이름 번역 (수동 오버라이드 포함)
    $translated_name = ft_translate_term_name($term->name, $gt_lang);
    $translated_slug = sanitize_title($translated_name . '-' . $pll_slug);

    // term 생성 (부모 포함)
    $new_term = wp_insert_term($translated_name, $taxonomy, [
        'slug'   => $translated_slug,
        'parent' => $translated_parent ?: 0,
    ]);

    if (is_wp_error($new_term)) {
        // 같은 slug로 이미 존재할 수 있음
        $existing = get_term_by('slug', $translated_slug, $taxonomy);
        if ($existing) {
            // 부모 수정
            if ($translated_parent && $existing->parent != $translated_parent) {
                wp_update_term($existing->term_id, $taxonomy, ['parent' => $translated_parent]);
            }
            return $existing->term_id;
        }
        return 0;
    }

    $new_term_id = $new_term['term_id'];
    pll_set_term_language($new_term_id, $pll_slug);

    // Polylang 번역 그룹 링크
    $existing_group = PLL()->model->term->get_translations($term->term_id);
    $existing_group['ko'] = $term->term_id;
    $existing_group[$pll_slug] = $new_term_id;
    PLL()->model->term->save_translations($term->term_id, $existing_group);

    // 한국어 원본 슬러그 저장 (이미지 매핑용)
    update_term_meta($new_term_id, '_ft_ko_slug', $term->slug);

    return $new_term_id;
}

// ── Translate a single post (any post type) ──
function translate_single_post($post, $pll_slug, $gt_lang) {
    global $DELAY_MS;

    // Translate title
    $translated_title = gt_translate($post->post_title, $gt_lang);
    usleep($DELAY_MS);

    // Translate content (split long content into chunks for API limits)
    $translated_content = '';
    if (!empty($post->post_content)) {
        $translated_content = gt_translate_long_text($post->post_content, $gt_lang);
        usleep($DELAY_MS);
    }

    // Translate excerpt
    $translated_excerpt = '';
    if (!empty($post->post_excerpt)) {
        $translated_excerpt = gt_translate($post->post_excerpt, $gt_lang);
        usleep($DELAY_MS);
    }

    // Create translated post
    $new_post_id = wp_insert_post(array(
        'post_type'    => $post->post_type,
        'post_title'   => $translated_title,
        'post_content' => $translated_content,
        'post_excerpt' => $translated_excerpt,
        'post_status'  => 'publish',
        'post_author'  => $post->post_author,
    ));

    if (is_wp_error($new_post_id)) return false;

    // Set language
    pll_set_post_language($new_post_id, $pll_slug);

    // Link translation
    $translations = PLL()->model->post->get_translations($post->ID);
    $translations['ko'] = $post->ID;
    $translations[$pll_slug] = $new_post_id;
    PLL()->model->post->save_translations($post->ID, $translations);

    // Copy thumbnail
    $thumbnail_id = get_post_meta($post->ID, '_thumbnail_id', true);
    if ($thumbnail_id) {
        update_post_meta($new_post_id, '_thumbnail_id', $thumbnail_id);
    }

    // Post-type-specific handling
    if ($post->post_type === 'travel_itinerary') {
        // Translate itinerary meta
        $translated_meta = translate_itinerary_meta($post->ID, $gt_lang);
        foreach ($translated_meta as $key => $value) {
            update_post_meta($new_post_id, $key, $value);
        }
        // Translate and link custom taxonomies
        translate_and_link_terms($post->ID, $new_post_id, 'destination', $pll_slug, $gt_lang);
        translate_and_link_terms($post->ID, $new_post_id, 'travel_style', $pll_slug, $gt_lang);
    } else {
        // Blog post: translate and link categories and tags
        translate_and_link_terms($post->ID, $new_post_id, 'category', $pll_slug, $gt_lang);
        translate_and_link_terms($post->ID, $new_post_id, 'post_tag', $pll_slug, $gt_lang);
    }

    return array('id' => $new_post_id, 'title' => $translated_title);
}

// ── Translate long text (split by paragraphs to avoid API limits) ──
function gt_translate_long_text($text, $gt_lang) {
    global $DELAY_MS;

    // If short enough, translate directly
    if (mb_strlen($text) <= 4000) {
        return gt_translate($text, $gt_lang);
    }

    // Split by double newline (paragraph breaks)
    $paragraphs = preg_split('/(\n\s*\n)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $translated = '';

    foreach ($paragraphs as $para) {
        if (empty(trim($para)) || preg_match('/^\s+$/', $para)) {
            $translated .= $para;
            continue;
        }
        usleep($DELAY_MS);
        $translated .= gt_translate($para, $gt_lang);
    }

    return $translated;
}

// ── Main ──
echo "=== 자동 번역 시작 ===\n\n";

$post_types = array('travel_itinerary', 'post');

foreach ($post_types as $post_type) {
    $posts = get_posts(array(
        'post_type'   => $post_type,
        'numberposts' => -1,
        'post_status' => 'publish',
    ));

    // Filter to Korean only
    $ko_posts = array();
    foreach ($posts as $p) {
        $lang = pll_get_post_language($p->ID);
        if (!$lang || $lang === 'ko') $ko_posts[] = $p;
    }

    $type_label = ($post_type === 'travel_itinerary') ? '여행 일정' : '블로그 글';
    echo "━━ {$type_label} ({$post_type}) ━━\n";
    echo "한국어 원본: " . count($ko_posts) . "개 / 번역 대상: " . count($LANG_MAP) . "개 언어\n\n";

    $total = count($ko_posts) * count($LANG_MAP);
    $done = 0;

    foreach ($ko_posts as $post) {
        echo "── {$post->post_title} (ID:{$post->ID}) ──\n";

        $existing_translations = PLL()->model->post->get_translations($post->ID);

        foreach ($LANG_MAP as $pll_slug => $gt_lang) {
            $done++;

            if (!empty($existing_translations[$pll_slug])) {
                echo "  [{$done}/{$total}] {$pll_slug}: 이미 존재 (ID:{$existing_translations[$pll_slug]})\n";
                continue;
            }

            echo "  [{$done}/{$total}] {$pll_slug}: 번역 중... ";

            $result = translate_single_post($post, $pll_slug, $gt_lang);

            if ($result) {
                echo "완료 (ID:{$result['id']}) \"{$result['title']}\"\n";
            } else {
                echo "실패!\n";
            }
        }

        echo "\n";
    }
}

echo "=== 자동 번역 완료! ===\n";

$itinerary_count = wp_count_posts('travel_itinerary');
$post_count = wp_count_posts('post');
echo "총 여행 일정: {$itinerary_count->publish}개 (원본 + 번역)\n";
echo "총 블로그 글: {$post_count->publish}개 (원본 + 번역)\n";
