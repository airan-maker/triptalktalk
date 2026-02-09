<?php
/**
 * Remove zh-tw and zh-hk languages and their translated posts/terms
 *
 * Run: wp eval-file cleanup-chinese.php --allow-root
 *
 * Keeps only zh-cn (Simplified Chinese) as the Chinese language.
 */

$langs_to_remove = ['zh-tw', 'zh-hk'];

echo "=== 중국어 정리: zh-tw, zh-hk 삭제 ===\n\n";

// ── Step 1: Delete translated posts ──
echo "--- Step 1: Delete translated posts ---\n";

$deleted_posts = 0;
foreach ($langs_to_remove as $lang_slug) {
    $posts = get_posts([
        'post_type'   => 'travel_itinerary',
        'numberposts' => -1,
        'post_status' => 'any',
        'lang'        => $lang_slug,
    ]);

    // Fallback: if Polylang 'lang' param doesn't work, filter manually
    if (empty($posts)) {
        $all_posts = get_posts([
            'post_type'   => 'travel_itinerary',
            'numberposts' => -1,
            'post_status' => 'any',
        ]);
        foreach ($all_posts as $p) {
            $post_lang = pll_get_post_language($p->ID);
            if ($post_lang === $lang_slug) {
                $posts[] = $p;
            }
        }
    }

    echo "  {$lang_slug}: " . count($posts) . " posts found\n";

    foreach ($posts as $post) {
        wp_delete_post($post->ID, true); // force delete (no trash)
        $deleted_posts++;
        echo "    Deleted: [{$lang_slug}] {$post->post_title} (ID:{$post->ID})\n";
    }
}

echo "  Total deleted: {$deleted_posts} posts\n\n";

// ── Step 2: Delete translated taxonomy terms ──
echo "--- Step 2: Delete translated taxonomy terms ---\n";

$deleted_terms = 0;
foreach (['destination', 'travel_style'] as $taxonomy) {
    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms)) continue;

    foreach ($terms as $term) {
        $term_lang = pll_get_term_language($term->term_id);
        if (in_array($term_lang, $langs_to_remove, true)) {
            wp_delete_term($term->term_id, $taxonomy);
            $deleted_terms++;
            echo "    Deleted: [{$term_lang}] {$term->name} ({$taxonomy}, ID:{$term->term_id})\n";
        }
    }
}

echo "  Total deleted: {$deleted_terms} terms\n\n";

// ── Step 3: Remove languages from Polylang ──
echo "--- Step 3: Remove languages from Polylang ---\n";

// Get all Polylang languages
$languages = PLL()->model->get_languages_list();
foreach ($languages as $language) {
    if (in_array($language->slug, $langs_to_remove, true)) {
        // Delete the language term from Polylang's internal taxonomy
        $result = wp_delete_term($language->term_id, 'language');
        if ($result && !is_wp_error($result)) {
            echo "  Removed language: {$language->name} ({$language->slug})\n";

            // Also delete from term_language taxonomy
            $tl_term = get_term_by('slug', $language->slug, 'term_language');
            if ($tl_term) {
                wp_delete_term($tl_term->term_id, 'term_language');
            }
            $pl_term = get_term_by('slug', $language->slug, 'post_language');
            if ($pl_term) {
                wp_delete_term($pl_term->term_id, 'post_language');
            }
        } else {
            echo "  WARNING: Could not remove {$language->slug}\n";
        }
    }
}

// Clean Polylang cache
if (method_exists(PLL()->model, 'clean_languages_cache')) {
    PLL()->model->clean_languages_cache();
}
delete_transient('pll_languages_list');

echo "\n=== Cleanup complete! ===\n";
echo "Remaining languages:\n";
$remaining = PLL()->model->get_languages_list();
foreach ($remaining as $lang) {
    echo "  - {$lang->name} ({$lang->slug})\n";
}
echo "\n";
