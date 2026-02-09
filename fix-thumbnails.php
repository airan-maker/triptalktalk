<?php
/**
 * Copy featured images (thumbnails) from Korean posts to their translations
 *
 * Run: wp eval-file fix-thumbnails.php --allow-root
 *
 * Also copies _ft_gallery if missing.
 */

echo "=== 썸네일 복사: 한국어 → 번역 포스트 ===\n\n";

$ko_posts = get_posts([
    'post_type'   => 'travel_itinerary',
    'numberposts' => -1,
    'post_status' => 'publish',
]);

$fixed = 0;

foreach ($ko_posts as $ko_post) {
    $post_lang = pll_get_post_language($ko_post->ID);
    if ($post_lang && $post_lang !== 'ko') continue;

    $ko_thumbnail_id = get_post_meta($ko_post->ID, '_thumbnail_id', true);
    $ko_gallery      = get_post_meta($ko_post->ID, '_ft_gallery', true);
    $translations    = PLL()->model->post->get_translations($ko_post->ID);

    foreach ($translations as $lang => $trans_id) {
        if ($lang === 'ko' || $trans_id == $ko_post->ID) continue;

        $needs_update = false;

        // Fix thumbnail
        if ($ko_thumbnail_id) {
            $trans_thumbnail = get_post_meta($trans_id, '_thumbnail_id', true);
            if ($trans_thumbnail != $ko_thumbnail_id) {
                update_post_meta($trans_id, '_thumbnail_id', $ko_thumbnail_id);
                $needs_update = true;
            }
        }

        // Fix gallery
        if ($ko_gallery) {
            $trans_gallery = get_post_meta($trans_id, '_ft_gallery', true);
            if ($trans_gallery != $ko_gallery) {
                update_post_meta($trans_id, '_ft_gallery', $ko_gallery);
                $needs_update = true;
            }
        }

        if ($needs_update) {
            $fixed++;
            echo "  [{$lang}] {$ko_post->post_title} (ID:{$trans_id}) ← thumbnail from KO (ID:{$ko_post->ID})\n";
        }
    }
}

echo "\nFixed {$fixed} translated posts\n";
echo "=== 완료! ===\n";
