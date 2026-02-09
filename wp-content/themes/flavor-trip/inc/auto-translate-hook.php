<?php
/**
 * 자동 번역 훅: 한국어 글 발행 시 자동으로 5개 언어 번역 생성
 *
 * travel_itinerary 또는 post를 한국어로 발행하면
 * 즉시 en, zh-cn, ja, fr, de 번역본을 생성합니다.
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

// ── Config ──
define('FT_TRANSLATE_LANGS', [
    'en'    => 'en',
    'zh-cn' => 'zh-CN',
    'ja'    => 'ja',
    'fr'    => 'fr',
    'de'    => 'de',
]);

// ── Google Translate API (free) ──
function ft_gt_translate($text, $target, $source = 'ko') {
    if (empty(trim($text))) return $text;

    $url = 'https://translate.googleapis.com/translate_a/single?'
         . http_build_query([
             'client' => 'gtx',
             'sl'     => $source,
             'tl'     => $target,
             'dt'     => 't',
             'q'      => $text,
         ]);

    $response = @file_get_contents($url, false, stream_context_create([
        'http' => [
            'timeout'       => 10,
            'ignore_errors' => true,
            'header'        => "User-Agent: Mozilla/5.0\r\n",
        ],
    ]));

    if ($response === false) return $text;
    $result = json_decode($response, true);
    if (!$result || !isset($result[0])) return $text;

    $translated = '';
    foreach ($result[0] as $part) {
        if (isset($part[0])) $translated .= $part[0];
    }
    return $translated ?: $text;
}

function ft_gt_translate_long($text, $target) {
    if (mb_strlen($text) <= 4000) {
        return ft_gt_translate($text, $target);
    }
    $paragraphs = preg_split('/(\n\s*\n)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $translated = '';
    foreach ($paragraphs as $para) {
        if (empty(trim($para)) || preg_match('/^\s+$/', $para)) {
            $translated .= $para;
            continue;
        }
        usleep(300000);
        $translated .= ft_gt_translate($para, $target);
    }
    return $translated;
}

// ── Term name overrides ──
function ft_get_translate_overrides() {
    return [
        '세부'       => ['en' => 'Cebu',         'zh-CN' => '宿务',         'ja' => 'セブ',             'fr' => 'Cebu',              'de' => 'Cebu'],
        '맛집'       => ['en' => 'Food Spots',    'zh-CN' => '美食店',       'ja' => 'グルメスポット',   'fr' => 'Bonnes adresses',   'de' => 'Gourmet-Spots'],
        '맛집투어'   => ['en' => 'Food Tour',     'zh-CN' => '美食之旅',     'ja' => 'グルメツアー',     'fr' => 'Tour gastronomique','de' => 'Gourmet-Tour'],
        '발리'       => ['en' => 'Bali',          'zh-CN' => '巴厘岛',       'ja' => 'バリ',             'fr' => 'Bali',              'de' => 'Bali'],
        '가성비여행' => ['en' => 'Budget Travel',  'zh-CN' => '高性价比旅行', 'ja' => 'コスパ旅行',       'fr' => 'Voyage économique', 'de' => 'Budget-Reise'],
        '미식여행'   => ['en' => 'Gourmet Travel', 'zh-CN' => '美食旅行',     'ja' => 'グルメ旅行',       'fr' => 'Voyage gastronomique','de' => 'Gourmet-Reise'],
        '힐링여행'   => ['en' => 'Healing Travel', 'zh-CN' => '治愈之旅',     'ja' => '癒し旅',           'fr' => 'Voyage bien-être',  'de' => 'Wellness-Reise'],
        '문화탐방'   => ['en' => 'Cultural Tour',  'zh-CN' => '文化探访',     'ja' => '文化探訪',         'fr' => 'Tour culturel',     'de' => 'Kulturtour'],
        '문화체험'   => ['en' => 'Cultural Experience','zh-CN' => '文化体验',  'ja' => '文化体験',         'fr' => 'Expérience culturelle','de' => 'Kulturerlebnis'],
        '가족여행'   => ['en' => 'Family Travel',  'zh-CN' => '家庭旅行',     'ja' => '家族旅行',         'fr' => 'Voyage en famille',   'de' => 'Familienreise'],
        '커플여행'   => ['en' => 'Couple Travel',   'zh-CN' => '情侣旅行',     'ja' => 'カップル旅行',     'fr' => 'Voyage en couple',    'de' => 'Paarreise'],
        '나트랑'     => ['en' => 'Nha Trang',       'zh-CN' => '芽庄',         'ja' => 'ニャチャン',       'fr' => 'Nha Trang',           'de' => 'Nha Trang'],
        '히로시마'   => ['en' => 'Hiroshima',        'zh-CN' => '广岛',         'ja' => '広島',             'fr' => 'Hiroshima',           'de' => 'Hiroshima'],
        '가나자와'   => ['en' => 'Kanazawa',         'zh-CN' => '金泽',         'ja' => '金沢',             'fr' => 'Kanazawa',            'de' => 'Kanazawa'],
        '삿포로'     => ['en' => 'Sapporo',          'zh-CN' => '札幌',         'ja' => '札幌',             'fr' => 'Sapporo',             'de' => 'Sapporo'],
        '베트남'     => ['en' => 'Vietnam',          'zh-CN' => '越南',         'ja' => 'ベトナム',         'fr' => 'Vietnam',             'de' => 'Vietnam'],
    ];
}

function ft_translate_term($name, $gt_lang) {
    $overrides = ft_get_translate_overrides();
    if (isset($overrides[$name][$gt_lang])) {
        return $overrides[$name][$gt_lang];
    }
    return ft_gt_translate($name, $gt_lang);
}

// ── Get or create translated term (recursive parent handling) ──
function ft_get_or_create_term($term, $taxonomy, $pll_slug, $gt_lang) {
    $existing_id = pll_get_term($term->term_id, $pll_slug);
    if ($existing_id) return $existing_id;

    // Parent first
    $translated_parent = 0;
    if ($term->parent > 0) {
        $parent_term = get_term($term->parent, $taxonomy);
        if ($parent_term && !is_wp_error($parent_term)) {
            $translated_parent = ft_get_or_create_term($parent_term, $taxonomy, $pll_slug, $gt_lang);
        }
    }

    $translated_name = ft_translate_term($term->name, $gt_lang);
    usleep(300000);
    $translated_slug = sanitize_title($translated_name . '-' . $pll_slug);

    $new_term = wp_insert_term($translated_name, $taxonomy, [
        'slug'   => $translated_slug,
        'parent' => $translated_parent ?: 0,
    ]);

    if (is_wp_error($new_term)) {
        $existing = get_term_by('slug', $translated_slug, $taxonomy);
        if ($existing) {
            if ($translated_parent && $existing->parent != $translated_parent) {
                wp_update_term($existing->term_id, $taxonomy, ['parent' => $translated_parent]);
            }
            // Polylang 언어/연결 + ko_slug 메타 설정 (누락 방지)
            pll_set_term_language($existing->term_id, $pll_slug);
            $group = PLL()->model->term->get_translations($term->term_id);
            $group['ko'] = $term->term_id;
            $group[$pll_slug] = $existing->term_id;
            PLL()->model->term->save_translations($term->term_id, $group);
            update_term_meta($existing->term_id, '_ft_ko_slug', $term->slug);
            return $existing->term_id;
        }
        return 0;
    }

    $new_term_id = $new_term['term_id'];
    pll_set_term_language($new_term_id, $pll_slug);

    $group = PLL()->model->term->get_translations($term->term_id);
    $group['ko'] = $term->term_id;
    $group[$pll_slug] = $new_term_id;
    PLL()->model->term->save_translations($term->term_id, $group);

    update_term_meta($new_term_id, '_ft_ko_slug', $term->slug);

    return $new_term_id;
}

// ── Translate itinerary meta ──
function ft_translate_itinerary_meta($post_id, $gt_lang) {
    $meta = [];

    // Translate text meta
    foreach (['_ft_destination_name', '_ft_best_season', '_ft_highlights', '_ft_duration'] as $key) {
        $val = get_post_meta($post_id, $key, true);
        if ($val) {
            usleep(300000);
            $meta[$key] = ft_gt_translate($val, $gt_lang);
        }
    }

    // Copy as-is meta
    foreach (['_ft_price_range', '_ft_difficulty', '_ft_map_lat', '_ft_map_lng', '_ft_map_zoom', '_thumbnail_id', '_ft_gallery'] as $key) {
        $val = get_post_meta($post_id, $key, true);
        if ($val !== '' && $val !== false) {
            $meta[$key] = $val;
        }
    }

    // Translate days
    $days = get_post_meta($post_id, '_ft_days', true);
    if (!empty($days) && is_array($days)) {
        $meta['_ft_days'] = ft_translate_days($days, $gt_lang);
    }

    return $meta;
}

function ft_translate_days($days, $gt_lang) {
    $result = [];
    foreach ($days as $day) {
        $new_day = $day;
        foreach (['title', 'summary', 'tip'] as $field) {
            if (!empty($day[$field])) {
                usleep(300000);
                $new_day[$field] = ft_gt_translate($day[$field], $gt_lang);
            }
        }

        if (!empty($day['spots']) && is_array($day['spots'])) {
            $new_spots = [];
            foreach ($day['spots'] as $spot) {
                $new_spot = $spot;
                foreach (['name', 'description', 'tip', 'menu', 'wait_tip', 'cuisine'] as $f) {
                    if (!empty($spot[$f])) {
                        usleep(300000);
                        $new_spot[$f] = ft_gt_translate($spot[$f], $gt_lang);
                    }
                }
                $new_spots[] = $new_spot;
            }
            $new_day['spots'] = $new_spots;
        }
        $result[] = $new_day;
    }
    return $result;
}

// ── Translate guide meta ──
function ft_translate_guide_meta($post_id, $gt_lang) {
    $meta = [];

    // Text fields
    foreach (['_ft_guide_city', '_ft_guide_country', '_ft_guide_intro'] as $key) {
        $val = get_post_meta($post_id, $key, true);
        if ($val) {
            usleep(300000);
            $meta[$key] = ft_gt_translate($val, $gt_lang);
        }
    }

    // Thumbnail
    $thumb_id = get_post_meta($post_id, '_thumbnail_id', true);
    if ($thumb_id) {
        $meta['_thumbnail_id'] = $thumb_id;
    }

    // Guide data — translate names/areas/notes, copy ratings/prices
    $data = get_post_meta($post_id, '_ft_guide_data', true);
    if (!empty($data) && is_array($data)) {
        $translated_data = [];
        foreach (['places', 'restaurants', 'hotels'] as $section) {
            if (empty($data[$section])) continue;
            $translated_items = [];
            foreach ($data[$section] as $item) {
                $new_item = $item;
                // Translate text fields
                foreach (['name', 'area', 'note', 'cuisine', 'category'] as $f) {
                    if (!empty($item[$f])) {
                        usleep(300000);
                        $new_item[$f] = ft_gt_translate($item[$f], $gt_lang);
                    }
                }
                // grade/price/ratings are copied as-is
                $translated_items[] = $new_item;
            }
            $translated_data[$section] = $translated_items;
        }
        $meta['_ft_guide_data'] = $translated_data;
    }

    return $meta;
}

// ── Main: translate a single post to one language ──
function ft_translate_post_to_lang($post, $pll_slug, $gt_lang) {
    // Title
    $translated_title = ft_gt_translate($post->post_title, $gt_lang);
    usleep(300000);

    // Content
    $translated_content = '';
    if (!empty($post->post_content)) {
        $translated_content = ft_gt_translate_long($post->post_content, $gt_lang);
        usleep(300000);
    }

    // Excerpt
    $translated_excerpt = '';
    if (!empty($post->post_excerpt)) {
        $translated_excerpt = ft_gt_translate($post->post_excerpt, $gt_lang);
        usleep(300000);
    }

    // Create post
    $new_post_id = wp_insert_post([
        'post_type'    => $post->post_type,
        'post_title'   => $translated_title,
        'post_content' => $translated_content,
        'post_excerpt' => $translated_excerpt,
        'post_status'  => 'publish',
        'post_author'  => $post->post_author,
    ]);

    if (is_wp_error($new_post_id)) return false;

    // Language & link
    pll_set_post_language($new_post_id, $pll_slug);
    $translations = PLL()->model->post->get_translations($post->ID);
    $translations['ko'] = $post->ID;
    $translations[$pll_slug] = $new_post_id;
    PLL()->model->post->save_translations($post->ID, $translations);

    // Thumbnail
    $thumb_id = get_post_meta($post->ID, '_thumbnail_id', true);
    if ($thumb_id) {
        update_post_meta($new_post_id, '_thumbnail_id', $thumb_id);
    }

    // Post-type specific
    if ($post->post_type === 'travel_itinerary') {
        $meta = ft_translate_itinerary_meta($post->ID, $gt_lang);
        foreach ($meta as $key => $value) {
            update_post_meta($new_post_id, $key, $value);
        }
        foreach (['destination', 'travel_style'] as $tax) {
            $terms = wp_get_post_terms($post->ID, $tax);
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $trans_tid = ft_get_or_create_term($term, $tax, $pll_slug, $gt_lang);
                    if ($trans_tid) {
                        wp_set_post_terms($new_post_id, [$trans_tid], $tax, true);
                    }
                }
            }
        }
    } elseif ($post->post_type === 'destination_guide') {
        $guide_meta = ft_translate_guide_meta($post->ID, $gt_lang);
        foreach ($guide_meta as $key => $value) {
            update_post_meta($new_post_id, $key, $value);
        }
        $terms = wp_get_post_terms($post->ID, 'destination');
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $trans_tid = ft_get_or_create_term($term, 'destination', $pll_slug, $gt_lang);
                if ($trans_tid) {
                    wp_set_post_terms($new_post_id, [$trans_tid], 'destination', true);
                }
            }
        }
    } else {
        foreach (['category', 'post_tag'] as $tax) {
            $terms = wp_get_post_terms($post->ID, $tax);
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $trans_tid = ft_get_or_create_term($term, $tax, $pll_slug, $gt_lang);
                    if ($trans_tid) {
                        wp_set_post_terms($new_post_id, [$trans_tid], $tax, true);
                    }
                }
            }
        }
    }

    return $new_post_id;
}

// ── WordPress Hook: auto-translate on publish ──
add_action('transition_post_status', function ($new_status, $old_status, $post) {
    // Only trigger on publish
    if ($new_status !== 'publish') return;

    // Only for supported post types
    if (!in_array($post->post_type, ['travel_itinerary', 'post', 'destination_guide'], true)) return;

    // Only if Polylang is active
    if (!function_exists('pll_get_post_language') || !function_exists('pll_set_post_language')) return;

    // Only for Korean posts
    $lang = pll_get_post_language($post->ID);
    if ($lang && $lang !== 'ko') return;

    // Set as Korean if not set
    if (!$lang) {
        pll_set_post_language($post->ID, 'ko');
    }

    // Check if translations already exist
    $existing = PLL()->model->post->get_translations($post->ID);
    $needs_translation = false;
    foreach (FT_TRANSLATE_LANGS as $pll_slug => $gt_lang) {
        if (empty($existing[$pll_slug])) {
            $needs_translation = true;
            break;
        }
    }

    if (!$needs_translation) return;

    // Schedule async translation (wp-cron)
    if (!wp_next_scheduled('ft_async_translate_post', [$post->ID])) {
        wp_schedule_single_event(time() + 5, 'ft_async_translate_post', [$post->ID]);
    }
}, 10, 3);

// ── Async translation via WP-Cron ──
add_action('ft_async_translate_post', function ($post_id) {
    $post = get_post($post_id);
    if (!$post || $post->post_status !== 'publish') return;

    $lang = pll_get_post_language($post->ID);
    if ($lang && $lang !== 'ko') return;

    $existing = PLL()->model->post->get_translations($post->ID);

    foreach (FT_TRANSLATE_LANGS as $pll_slug => $gt_lang) {
        if (!empty($existing[$pll_slug])) continue;

        $new_id = ft_translate_post_to_lang($post, $pll_slug, $gt_lang);

        if ($new_id) {
            error_log("[FlavorTrip] Translated '{$post->post_title}' to {$pll_slug} (ID:{$new_id})");
        } else {
            error_log("[FlavorTrip] Failed to translate '{$post->post_title}' to {$pll_slug}");
        }
    }
});

// ── Admin notice for translation status ──
add_action('admin_notices', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'post') return;

    global $post;
    if (!$post || !in_array($post->post_type, ['travel_itinerary', 'post', 'destination_guide'], true)) return;

    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($post->ID) : '';
    if ($lang !== 'ko') return;

    $translations = function_exists('PLL') ? PLL()->model->post->get_translations($post->ID) : [];
    $missing = [];
    foreach (FT_TRANSLATE_LANGS as $pll_slug => $gt_lang) {
        if (empty($translations[$pll_slug])) {
            $missing[] = $pll_slug;
        }
    }

    if (empty($missing)) {
        echo '<div class="notice notice-success"><p>✅ 이 글은 ' . count(FT_TRANSLATE_LANGS) . '개 언어로 번역 완료되었습니다.</p></div>';
    } elseif ($post->post_status === 'publish') {
        echo '<div class="notice notice-info"><p>🔄 번역 대기 중: ' . implode(', ', $missing) . ' — 잠시 후 자동 생성됩니다.</p></div>';
    }
});

// ── 썸네일 변경 시 번역 포스트 자동 동기화 ──
add_action('updated_post_meta', 'ft_sync_thumbnail_to_translations', 10, 4);
add_action('added_post_meta', 'ft_sync_thumbnail_to_translations', 10, 4);

function ft_sync_thumbnail_to_translations($meta_id, $post_id, $meta_key, $meta_value) {
    if ($meta_key !== '_thumbnail_id') return;
    if (!function_exists('PLL')) return;

    $post_type = get_post_type($post_id);
    if (!in_array($post_type, ['travel_itinerary', 'post', 'destination_guide'], true)) return;

    $lang = pll_get_post_language($post_id);
    if ($lang !== 'ko') return;

    $translations = PLL()->model->post->get_translations($post_id);
    foreach ($translations as $lang_slug => $trans_id) {
        if ($trans_id != $post_id) {
            update_post_meta($trans_id, '_thumbnail_id', $meta_value);
        }
    }
}
