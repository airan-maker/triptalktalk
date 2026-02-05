<?php
/**
 * ?¬í¼ ?¨ìˆ˜ (?œí”Œë¦??œê·¸)
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

/**
 * ?˜ì´ì§€?¤ì´?? */
function ft_pagination($query = null) {
    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }

    if ($query->max_num_pages <= 1) return;

    $paged = max(1, get_query_var('paged'));

    echo '<nav class="pagination" aria-label="' . esc_attr__('?˜ì´ì§€ ?¤ë¹„ê²Œì´??, 'flavor-trip') . '">';
    echo paginate_links([
        'total'     => $query->max_num_pages,
        'current'   => $paged,
        'mid_size'  => 2,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
    ]);
    echo '</nav>';
}

/**
 * ?½ê¸° ?œê°„ ê³„ì‚°
 */
function ft_reading_time($post = null) {
    $post = get_post($post);
    if (!$post) return '';

    $content = wp_strip_all_tags($post->post_content);
    // ?œê? ê¸°ì? ë¶„ë‹¹ 500??    $char_count = mb_strlen($content, 'UTF-8');
    $minutes = max(1, ceil($char_count / 500));

    return sprintf(__('%dë¶??½ê¸°', 'flavor-trip'), $minutes);
}

/**
 * ê°€ê²©ë? ?¼ë²¨
 */
function ft_get_price_label($price) {
    $labels = [
        'budget'   => __('ê°€?±ë¹„', 'flavor-trip'),
        'moderate' => __('ë³´í†µ', 'flavor-trip'),
        'premium'  => __('?„ë¦¬ë¯¸ì—„', 'flavor-trip'),
        'luxury'   => __('??…”ë¦?, 'flavor-trip'),
    ];
    return $labels[$price] ?? $price;
}

/**
 * ?œì´???¼ë²¨
 */
function ft_get_difficulty_label($difficulty) {
    $labels = [
        'easy'     => __('?¬ì?', 'flavor-trip'),
        'moderate' => __('ë³´í†µ', 'flavor-trip'),
        'hard'     => __('?´ë ¤?€', 'flavor-trip'),
    ];
    return $labels[$difficulty] ?? $difficulty;
}
