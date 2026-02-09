<?php
/**
 * Fix Polylang term translation links and save _ft_ko_slug meta
 *
 * Run: wp eval-file fix-term-links.php --allow-root
 *
 * Strategy: Use correctly-linked POST translations to infer TERM links.
 * Post translations work (saved with correct API), but term translations
 * were originally saved with wrong API (post model instead of term model).
 *
 * For each Korean post → find its translated posts → compare their terms
 * → link corresponding terms via Polylang + save _ft_ko_slug meta.
 */

echo "=== Term 링크 수정 + _ft_ko_slug 메타 저장 ===\n\n";

$taxonomies = ['destination', 'travel_style', 'category', 'post_tag'];
$post_types = ['travel_itinerary', 'post'];
$fixed_links = 0;
$fixed_meta = 0;

// Build term mapping by using post translations as bridge
// Korean post → has Korean terms
// Translated post → has translated terms
// Post translation link is CORRECT → so we can infer term pairs

foreach ($post_types as $post_type) {
    $ko_posts = get_posts([
        'post_type'   => $post_type,
        'numberposts' => -1,
        'post_status' => 'publish',
    ]);

    echo "--- {$post_type} ---\n";

    foreach ($ko_posts as $ko_post) {
        $post_lang = pll_get_post_language($ko_post->ID);
        if ($post_lang && $post_lang !== 'ko') continue;

        $post_translations = PLL()->model->post->get_translations($ko_post->ID);
        if (count($post_translations) <= 1) continue;

        foreach ($taxonomies as $taxonomy) {
            $ko_terms = wp_get_post_terms($ko_post->ID, $taxonomy);
            if (empty($ko_terms) || is_wp_error($ko_terms)) continue;

            foreach ($post_translations as $lang => $trans_post_id) {
                if ($lang === 'ko') continue;

                $trans_terms = wp_get_post_terms($trans_post_id, $taxonomy);
                if (empty($trans_terms) || is_wp_error($trans_terms)) continue;

                // Match Korean terms to translated terms
                // (usually 1:1 since auto-translate creates same number of terms)
                foreach ($ko_terms as $ki => $ko_term) {
                    if (!isset($trans_terms[$ki])) continue;
                    $trans_term = $trans_terms[$ki];

                    // Skip if same term (shouldn't happen)
                    if ($ko_term->term_id === $trans_term->term_id) continue;

                    // 1) Save _ft_ko_slug meta
                    $existing_meta = get_term_meta($trans_term->term_id, '_ft_ko_slug', true);
                    if ($existing_meta !== $ko_term->slug) {
                        update_term_meta($trans_term->term_id, '_ft_ko_slug', $ko_term->slug);
                        $fixed_meta++;
                    }

                    // 2) Fix Polylang term link
                    $current_link = pll_get_term($ko_term->term_id, $lang);
                    if ($current_link && $current_link == $trans_term->term_id) {
                        continue; // Already correct
                    }

                    // Set correct language on translated term
                    $trans_lang = pll_get_term_language($trans_term->term_id);
                    if (!$trans_lang) {
                        pll_set_term_language($trans_term->term_id, $lang);
                    }

                    // Merge into translation group
                    $existing_group = PLL()->model->term->get_translations($ko_term->term_id);
                    $existing_group['ko'] = $ko_term->term_id;
                    $existing_group[$lang] = $trans_term->term_id;
                    PLL()->model->term->save_translations($ko_term->term_id, $existing_group);

                    $fixed_links++;
                    echo "  LINKED: {$ko_term->slug} ({$taxonomy}) → {$trans_term->slug} ({$lang})\n";
                }
            }
        }
    }
}

echo "\nFixed {$fixed_links} term links\n";
echo "Set {$fixed_meta} _ft_ko_slug meta entries\n";

// ── Part 2: 한국어 포스트 썸네일을 번역 포스트에 동기화 ──
echo "\n=== 썸네일 동기화 ===\n";
$synced_thumbs = 0;

foreach ($post_types as $post_type) {
    $ko_posts = get_posts([
        'post_type'   => $post_type,
        'numberposts' => -1,
        'post_status' => 'publish',
    ]);

    foreach ($ko_posts as $ko_post) {
        $post_lang = pll_get_post_language($ko_post->ID);
        if ($post_lang && $post_lang !== 'ko') continue;

        $thumb_id = get_post_meta($ko_post->ID, '_thumbnail_id', true);
        if (!$thumb_id) continue;

        $post_translations = PLL()->model->post->get_translations($ko_post->ID);
        foreach ($post_translations as $lang => $trans_post_id) {
            if ($lang === 'ko' || $trans_post_id == $ko_post->ID) continue;

            $trans_thumb = get_post_meta($trans_post_id, '_thumbnail_id', true);
            if ($trans_thumb != $thumb_id) {
                update_post_meta($trans_post_id, '_thumbnail_id', $thumb_id);
                $synced_thumbs++;
                echo "  THUMB: post {$ko_post->ID} → {$trans_post_id} ({$lang}): thumbnail {$thumb_id}\n";
            }
        }
    }
}

echo "\nSynced {$synced_thumbs} thumbnails\n";
echo "\n=== 완료! ===\n";
