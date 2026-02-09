<?php
/**
 * Auto-translate travel itineraries using Google Translate API (free)
 * Run: wp eval-file auto-translate.php --allow-root
 *
 * Creates translated copies of all Korean travel_itinerary posts
 * and links them via Polylang.
 */

// ── Config ──
$LANG_MAP = array(
    'en'    => 'en',
    'en-au' => 'en',
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
    foreach (array('_ft_price_range', '_ft_difficulty', '_ft_map_lat', '_ft_map_lng', '_ft_map_zoom') as $key) {
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

// ── Translate taxonomy terms ──
function translate_and_link_terms($post_id, $new_post_id, $taxonomy, $pll_slug, $gt_lang) {
    $terms = wp_get_post_terms($post_id, $taxonomy);
    if (empty($terms) || is_wp_error($terms)) return;

    foreach ($terms as $term) {
        $term_lang = pll_get_term_language($term->term_id);

        // Check if this term already has a translation
        $translated_term_id = pll_get_term($term->term_id, $pll_slug);

        if ($translated_term_id) {
            wp_set_post_terms($new_post_id, array($translated_term_id), $taxonomy, true);
        } else {
            // Create translated term
            $translated_name = gt_translate($term->name, $gt_lang);
            usleep(300000);
            $translated_slug = sanitize_title($translated_name . '-' . $pll_slug);

            $new_term = wp_insert_term($translated_name, $taxonomy, array(
                'slug' => $translated_slug,
            ));

            if (!is_wp_error($new_term)) {
                $new_term_id = $new_term['term_id'];
                pll_set_term_language($new_term_id, $pll_slug);
                // 기존 번역 그룹 가져와서 병합
                $existing = PLL()->model->term->get_translations($term->term_id);
                $existing['ko'] = $term->term_id;
                $existing[$pll_slug] = $new_term_id;
                PLL()->model->term->save_translations($term->term_id, $existing);
                wp_set_post_terms($new_post_id, array($new_term_id), $taxonomy, true);
            }
        }
    }
}

// ── Main ──
echo "=== 자동 번역 시작 ===\n\n";

$posts = get_posts(array(
    'post_type'   => 'travel_itinerary',
    'numberposts' => -1,
    'post_status' => 'publish',
));

echo "한국어 원본: " . count($posts) . "개\n";
echo "번역 대상: " . count($LANG_MAP) . "개 언어\n\n";

$total = count($posts) * count($LANG_MAP);
$done = 0;

foreach ($posts as $post) {
    $post_lang = pll_get_post_language($post->ID);
    if ($post_lang && $post_lang !== 'ko') continue;

    echo "── {$post->post_title} (ID:{$post->ID}) ──\n";

    // Get existing translations for this post
    $existing_translations = PLL()->model->post->get_translations($post->ID);

    foreach ($LANG_MAP as $pll_slug => $gt_lang) {
        $done++;

        // Skip if translation already exists
        if (!empty($existing_translations[$pll_slug])) {
            echo "  [{$done}/{$total}] {$pll_slug}: 이미 존재 (ID:{$existing_translations[$pll_slug]})\n";
            continue;
        }

        echo "  [{$done}/{$total}] {$pll_slug}: 번역 중... ";

        // Translate title
        $translated_title = gt_translate($post->post_title, $gt_lang);
        usleep($DELAY_MS);

        // Translate content
        $translated_content = '';
        if (!empty($post->post_content)) {
            $translated_content = gt_translate($post->post_content, $gt_lang);
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
            'post_type'    => 'travel_itinerary',
            'post_title'   => $translated_title,
            'post_content' => $translated_content,
            'post_excerpt' => $translated_excerpt,
            'post_status'  => 'publish',
            'post_author'  => $post->post_author,
        ));

        if (is_wp_error($new_post_id)) {
            echo "실패!\n";
            continue;
        }

        // Set language
        pll_set_post_language($new_post_id, $pll_slug);

        // Link translation
        $translations = PLL()->model->post->get_translations($post->ID);
        $translations['ko'] = $post->ID;
        $translations[$pll_slug] = $new_post_id;
        PLL()->model->post->save_translations($post->ID, $translations);

        // Translate meta
        $translated_meta = translate_itinerary_meta($post->ID, $gt_lang);
        foreach ($translated_meta as $key => $value) {
            update_post_meta($new_post_id, $key, $value);
        }

        // Translate and link taxonomy terms
        translate_and_link_terms($post->ID, $new_post_id, 'destination', $pll_slug, $gt_lang);
        translate_and_link_terms($post->ID, $new_post_id, 'travel_style', $pll_slug, $gt_lang);

        echo "완료 (ID:{$new_post_id}) \"{$translated_title}\"\n";
    }

    echo "\n";
}

echo "=== 자동 번역 완료! ===\n";

// Summary
$total_posts = wp_count_posts('travel_itinerary');
echo "총 여행 일정: {$total_posts->publish}개 (원본 + 번역)\n";
