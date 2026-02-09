<?php
/**
 * Fix existing translations:
 * 1. Re-link taxonomy terms via Polylang (post→term API bug fix)
 * 2. Re-translate cuisine field (was skipped)
 * 3. Re-translate corrupted fields (batch separator mangling)
 *
 * Run: wp eval-file fix-translations.php --allow-root
 */

$LANG_MAP = array(
    'en'    => 'en',
    'en-au' => 'en',
    'zh-cn' => 'zh-CN',
    'zh-hk' => 'zh-TW',
    'zh-tw' => 'zh-TW',
    'ja'    => 'ja',
    'fr'    => 'fr',
    'de'    => 'de',
);

$DELAY_MS = 500000;

// ── Translation function (same as auto-translate.php) ──
function gt_translate_fix($text, $target, $source = 'ko') {
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

// ── Step 1: Fix taxonomy term Polylang links ──
echo "=== Step 1: Fix taxonomy term Polylang links ===\n\n";

$taxonomies = array('destination', 'travel_style');
$fixed_terms = 0;

foreach ($taxonomies as $taxonomy) {
    $ko_terms = get_terms(array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ));

    if (is_wp_error($ko_terms)) continue;

    // Group terms by their base slug (strip language suffix)
    $lang_suffixes = array('-en-au', '-zh-cn', '-zh-hk', '-zh-tw', '-en', '-ja', '-fr', '-de');

    // First, find Korean terms (no language suffix)
    $ko_term_map = array(); // slug => term
    $translated_terms = array(); // array of [term, base_slug, lang_suffix]

    foreach ($ko_terms as $term) {
        $term_lang = function_exists('pll_get_term_language') ? pll_get_term_language($term->term_id) : '';

        if ($term_lang === 'ko' || $term_lang === '') {
            $ko_term_map[$term->slug] = $term;
        } else {
            // Try to identify the base slug
            $slug = $term->slug;
            foreach ($lang_suffixes as $suffix) {
                if (substr($slug, -strlen($suffix)) === $suffix) {
                    $base = substr($slug, 0, -strlen($suffix));
                    $lang = ltrim($suffix, '-');
                    $translated_terms[] = array($term, $base, $lang, $term_lang);
                    break;
                }
            }
        }
    }

    echo "  {$taxonomy}: " . count($ko_term_map) . " Korean terms, " . count($translated_terms) . " translated terms\n";

    // Now link translated terms to their Korean originals
    foreach ($translated_terms as $item) {
        list($term, $base_slug, $lang_from_slug, $pll_lang) = $item;

        if (!isset($ko_term_map[$base_slug])) {
            echo "    WARNING: No Korean term for '{$base_slug}' (from {$term->slug})\n";
            continue;
        }

        $ko_term = $ko_term_map[$base_slug];

        // Check if already properly linked
        $existing_link = pll_get_term($ko_term->term_id, $pll_lang);
        if ($existing_link && $existing_link == $term->term_id) {
            continue; // Already correct
        }

        // Fix the link
        $existing = PLL()->model->term->get_translations($ko_term->term_id);
        $existing['ko'] = $ko_term->term_id;
        $existing[$pll_lang] = $term->term_id;
        PLL()->model->term->save_translations($ko_term->term_id, $existing);
        $fixed_terms++;
        echo "    FIXED: {$ko_term->slug} ({$taxonomy}) → {$term->slug} ({$pll_lang})\n";
    }
}

echo "\n  Fixed {$fixed_terms} term links\n\n";

// ── Step 2: Re-translate missing/corrupted fields ──
echo "=== Step 2: Re-translate missing/corrupted fields ===\n\n";

$ko_posts = get_posts(array(
    'post_type'   => 'travel_itinerary',
    'numberposts' => -1,
    'post_status' => 'publish',
));

$fixed_posts = 0;

foreach ($ko_posts as $ko_post) {
    $post_lang = pll_get_post_language($ko_post->ID);
    if ($post_lang && $post_lang !== 'ko') continue;

    $translations = PLL()->model->post->get_translations($ko_post->ID);
    $ko_days = get_post_meta($ko_post->ID, '_ft_days', true);

    if (empty($ko_days) || !is_array($ko_days)) continue;

    foreach ($LANG_MAP as $pll_slug => $gt_lang) {
        if (empty($translations[$pll_slug])) continue;
        $trans_post_id = $translations[$pll_slug];
        $trans_days = get_post_meta($trans_post_id, '_ft_days', true);

        if (empty($trans_days) || !is_array($trans_days)) continue;

        $needs_update = false;

        foreach ($trans_days as $di => $day) {
            if (empty($day['spots']) || !is_array($day['spots'])) continue;

            foreach ($day['spots'] as $si => $spot) {
                // Fix 1: Translate cuisine if still in Korean
                if (!empty($spot['cuisine']) && !empty($ko_days[$di]['spots'][$si]['cuisine'])) {
                    $ko_cuisine = $ko_days[$di]['spots'][$si]['cuisine'];
                    if ($spot['cuisine'] === $ko_cuisine) {
                        usleep($DELAY_MS);
                        $trans_days[$di]['spots'][$si]['cuisine'] = gt_translate_fix($ko_cuisine, $gt_lang);
                        $needs_update = true;
                    }
                }

                // Fix 2: Check if name is still in Korean (batch separator corruption)
                if (!empty($spot['name']) && !empty($ko_days[$di]['spots'][$si]['name'])) {
                    $ko_name = $ko_days[$di]['spots'][$si]['name'];
                    // If translated name equals Korean name, it wasn't translated
                    if ($spot['name'] === $ko_name) {
                        usleep($DELAY_MS);
                        $trans_days[$di]['spots'][$si]['name'] = gt_translate_fix($ko_name, $gt_lang);
                        $needs_update = true;
                    }
                }

                // Fix 3: Check description
                if (!empty($spot['description']) && !empty($ko_days[$di]['spots'][$si]['description'])) {
                    $ko_desc = $ko_days[$di]['spots'][$si]['description'];
                    if ($spot['description'] === $ko_desc) {
                        usleep($DELAY_MS);
                        $trans_days[$di]['spots'][$si]['description'] = gt_translate_fix($ko_desc, $gt_lang);
                        $needs_update = true;
                    }
                }

                // Fix 4: Check tip
                if (!empty($spot['tip']) && !empty($ko_days[$di]['spots'][$si]['tip'])) {
                    $ko_tip = $ko_days[$di]['spots'][$si]['tip'];
                    if ($spot['tip'] === $ko_tip) {
                        usleep($DELAY_MS);
                        $trans_days[$di]['spots'][$si]['tip'] = gt_translate_fix($ko_tip, $gt_lang);
                        $needs_update = true;
                    }
                }

                // Fix 5: Check menu
                if (!empty($spot['menu']) && !empty($ko_days[$di]['spots'][$si]['menu'])) {
                    $ko_menu = $ko_days[$di]['spots'][$si]['menu'];
                    if ($spot['menu'] === $ko_menu) {
                        usleep($DELAY_MS);
                        $trans_days[$di]['spots'][$si]['menu'] = gt_translate_fix($ko_menu, $gt_lang);
                        $needs_update = true;
                    }
                }

                // Fix 6: Check wait_tip
                if (!empty($spot['wait_tip']) && !empty($ko_days[$di]['spots'][$si]['wait_tip'])) {
                    $ko_wait = $ko_days[$di]['spots'][$si]['wait_tip'];
                    if ($spot['wait_tip'] === $ko_wait) {
                        usleep($DELAY_MS);
                        $trans_days[$di]['spots'][$si]['wait_tip'] = gt_translate_fix($ko_wait, $gt_lang);
                        $needs_update = true;
                    }
                }
            }

            // Fix day-level fields too
            if (!empty($day['title']) && !empty($ko_days[$di]['title'])) {
                if ($day['title'] === $ko_days[$di]['title']) {
                    usleep($DELAY_MS);
                    $trans_days[$di]['title'] = gt_translate_fix($ko_days[$di]['title'], $gt_lang);
                    $needs_update = true;
                }
            }
            if (!empty($day['summary']) && !empty($ko_days[$di]['summary'])) {
                if ($day['summary'] === $ko_days[$di]['summary']) {
                    usleep($DELAY_MS);
                    $trans_days[$di]['summary'] = gt_translate_fix($ko_days[$di]['summary'], $gt_lang);
                    $needs_update = true;
                }
            }
            if (!empty($day['tip']) && !empty($ko_days[$di]['tip'])) {
                if ($day['tip'] === $ko_days[$di]['tip']) {
                    usleep($DELAY_MS);
                    $trans_days[$di]['tip'] = gt_translate_fix($ko_days[$di]['tip'], $gt_lang);
                    $needs_update = true;
                }
            }
        }

        if ($needs_update) {
            update_post_meta($trans_post_id, '_ft_days', $trans_days);
            $fixed_posts++;
            echo "  FIXED: [{$pll_slug}] {$ko_post->post_title} (ID:{$trans_post_id})\n";
        }
    }
}

echo "\n  Fixed {$fixed_posts} post translations\n\n";
echo "=== Fix complete! ===\n";
