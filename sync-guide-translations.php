<?php
/**
 * 도시 가이드 번역 포스트에 좌표(lat/lng) 및 신규 필드 동기화
 *
 * 한국어 원본의 _ft_guide_data에서 lat, lng, detail, must_do, popular_menu, klook_url을
 * 번역 포스트에 복사합니다. (텍스트 필드는 번역 후 복사)
 *
 * Usage: wp eval-file sync-guide-translations.php --allow-root
 */

if (!defined('ABSPATH')) {
    echo "Run via WP-CLI: wp eval-file sync-guide-translations.php --allow-root\n";
    exit;
}

if (!function_exists('PLL')) {
    echo "Polylang not active.\n";
    exit;
}

// Google Translate helper
function _sync_gt($text, $target) {
    if (empty(trim($text))) return $text;
    $url = 'https://translate.googleapis.com/translate_a/single?'
         . http_build_query(['client'=>'gtx','sl'=>'ko','tl'=>$target,'dt'=>'t','q'=>$text]);
    $resp = @file_get_contents($url, false, stream_context_create([
        'http' => ['timeout'=>10,'ignore_errors'=>true,'header'=>"User-Agent: Mozilla/5.0\r\n"],
    ]));
    if (!$resp) return $text;
    $r = json_decode($resp, true);
    if (!$r || !isset($r[0])) return $text;
    $out = '';
    foreach ($r[0] as $p) { if (isset($p[0])) $out .= $p[0]; }
    return $out ?: $text;
}

$langs = ['en'=>'en','zh-cn'=>'zh-CN','ja'=>'ja','fr'=>'fr','de'=>'de'];

$guides = get_posts([
    'post_type' => 'destination_guide',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'lang' => 'ko',
]);

if (empty($guides)) {
    // Fallback: get all and filter by language
    $guides = get_posts([
        'post_type' => 'destination_guide',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);
    $guides = array_filter($guides, function($p) {
        return pll_get_post_language($p->ID) === 'ko';
    });
}

echo "Found " . count($guides) . " Korean guides.\n";

foreach ($guides as $guide) {
    $ko_data = get_post_meta($guide->ID, '_ft_guide_data', true);
    if (empty($ko_data)) {
        echo "  Skip {$guide->post_title} — no data.\n";
        continue;
    }

    echo "\n=== {$guide->post_title} ===\n";

    $translations = PLL()->model->post->get_translations($guide->ID);

    foreach ($langs as $pll_slug => $gt_lang) {
        if (empty($translations[$pll_slug])) {
            echo "  [{$pll_slug}] No translation found.\n";
            continue;
        }

        $trans_id = $translations[$pll_slug];
        $trans_data = get_post_meta($trans_id, '_ft_guide_data', true);

        if (empty($trans_data)) {
            echo "  [{$pll_slug}] No guide data, copying full data with translation...\n";
            // Full translate
            $translated_data = [];
            foreach (['places','restaurants','hotels'] as $section) {
                if (empty($ko_data[$section])) continue;
                $items = [];
                foreach ($ko_data[$section] as $item) {
                    $new = $item;
                    foreach (['name','area','note','cuisine','category','detail','must_do','popular_menu'] as $f) {
                        if (!empty($item[$f])) {
                            usleep(300000);
                            $new[$f] = _sync_gt($item[$f], $gt_lang);
                        }
                    }
                    $items[] = $new;
                }
                $translated_data[$section] = $items;
            }
            update_post_meta($trans_id, '_ft_guide_data', $translated_data);

            // Also sync other meta
            foreach (['_ft_guide_city','_ft_guide_country','_ft_guide_intro'] as $mk) {
                $val = get_post_meta($guide->ID, $mk, true);
                if ($val) {
                    usleep(300000);
                    update_post_meta($trans_id, $mk, _sync_gt($val, $gt_lang));
                }
            }
            echo "  [{$pll_slug}] Full data translated and saved.\n";
            continue;
        }

        // Merge missing fields from Korean data
        $updated = false;
        foreach (['places','restaurants','hotels'] as $section) {
            if (empty($ko_data[$section]) || empty($trans_data[$section])) continue;
            $ko_items = $ko_data[$section];
            $tr_items = &$trans_data[$section];

            for ($i = 0; $i < count($ko_items) && $i < count($tr_items); $i++) {
                // Copy lat/lng (always from Korean)
                if (!empty($ko_items[$i]['lat'])) {
                    $tr_items[$i]['lat'] = $ko_items[$i]['lat'];
                    $tr_items[$i]['lng'] = $ko_items[$i]['lng'];
                    $updated = true;
                }

                // Copy klook_url
                if (!empty($ko_items[$i]['klook_url']) && empty($tr_items[$i]['klook_url'])) {
                    $tr_items[$i]['klook_url'] = $ko_items[$i]['klook_url'];
                    $updated = true;
                }

                // Translate and add missing text fields
                foreach (['detail','must_do','popular_menu'] as $f) {
                    if (!empty($ko_items[$i][$f]) && empty($tr_items[$i][$f])) {
                        usleep(300000);
                        $tr_items[$i][$f] = _sync_gt($ko_items[$i][$f], $gt_lang);
                        $updated = true;
                    }
                }
            }
        }

        if ($updated) {
            update_post_meta($trans_id, '_ft_guide_data', $trans_data);
            echo "  [{$pll_slug}] Synced lat/lng + translated new fields.\n";
        } else {
            echo "  [{$pll_slug}] Already up to date.\n";
        }
    }
}

echo "\nDone!\n";
