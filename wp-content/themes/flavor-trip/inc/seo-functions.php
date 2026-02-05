<?php
/**
 * SEO: Open Graph, Twitter Card, Î©îÌ? ?§Î™Ö, Ï∫êÎÖ∏?àÏª¨, robots
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

/**
 * SEO Î©îÌ? ?úÍ∑∏ Ï∂úÎ†•
 */
function ft_seo_meta_tags() {
    $description = ft_get_meta_description();
    $canonical   = ft_get_canonical_url();
    $og_image    = ft_get_og_image();
    $og_title    = ft_get_og_title();
    $og_type     = is_single() || is_singular('travel_itinerary') ? 'article' : 'website';
    $site_name   = get_bloginfo('name');

    // Î©îÌ? ?§Î™Ö
    if ($description) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }

    // Ï∫êÎÖ∏?àÏª¨
    if ($canonical) {
        echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    }

    // Robots
    if (is_search() || is_404()) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }

    // Open Graph
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($canonical ?: get_permalink()) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    echo '<meta property="og:locale" content="ko_KR">' . "\n";

    if ($description) {
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    }

    if ($og_image) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta property="og:image:width" content="1200">' . "\n";
        echo '<meta property="og:image:height" content="630">' . "\n";
    }

    if (is_single() || is_singular('travel_itinerary')) {
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c')) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c')) . '">' . "\n";
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($og_title) . '">' . "\n";

    if ($description) {
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    }
    if ($og_image) {
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
    }

    // ?¨Ïù¥???∏Ï¶ù Î©îÌ?
    $naver_verify = get_theme_mod('ft_naver_verify');
    $google_verify = get_theme_mod('ft_google_verify');

    if ($naver_verify) {
        echo '<meta name="naver-site-verification" content="' . esc_attr($naver_verify) . '">' . "\n";
    }
    if ($google_verify) {
        echo '<meta name="google-site-verification" content="' . esc_attr($google_verify) . '">' . "\n";
    }
}

/**
 * Î©îÌ? ?§Î™Ö ?ùÏÑ±
 */
function ft_get_meta_description() {
    if (is_singular()) {
        $post = get_post();
        if (has_excerpt($post)) {
            return wp_strip_all_tags(get_the_excerpt($post));
        }
        return wp_trim_words(wp_strip_all_tags($post->post_content), 30, '');
    }

    if (is_tax() || is_category() || is_tag()) {
        $desc = term_description();
        if ($desc) {
            return wp_strip_all_tags($desc);
        }
    }

    if (is_front_page()) {
        return get_bloginfo('description');
    }

    if (is_post_type_archive('travel_itinerary')) {
        return __('?§Ïñë???¨Ìñâ ?ºÏ†ïÍ≥?ÏΩîÏä§Î•??êÏÉâ?òÍ≥† ?òÎßå???¨Ìñâ??Í≥ÑÌöç?¥Î≥¥?∏Ïöî.', 'flavor-trip');
    }

    return '';
}

/**
 * OG ?úÎ™© ?ùÏÑ±
 */
function ft_get_og_title() {
    if (is_singular()) {
        return get_the_title();
    }

    if (is_tax() || is_category() || is_tag()) {
        return single_term_title('', false);
    }

    if (is_post_type_archive('travel_itinerary')) {
        return __('?¨Ìñâ ?ºÏ†ï', 'flavor-trip');
    }

    if (is_search()) {
        return sprintf(__('"%s" Í≤Ä??Í≤∞Í≥º', 'flavor-trip'), get_search_query());
    }

    return get_bloginfo('name');
}

/**
 * Ï∫êÎÖ∏?àÏª¨ URL
 */
function ft_get_canonical_url() {
    if (is_singular()) {
        return get_permalink();
    }

    if (is_front_page()) {
        return home_url('/');
    }

    if (is_tax() || is_category() || is_tag()) {
        return get_term_link(get_queried_object());
    }

    if (is_post_type_archive()) {
        return get_post_type_archive_link(get_post_type());
    }

    return '';
}

/**
 * OG ?¥Î?ÏßÄ
 */
function ft_get_og_image() {
    if (is_singular() && has_post_thumbnail()) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'ft-hero');
        if ($img) return $img[0];
    }

    // Í∏∞Î≥∏ OG ?¥Î?ÏßÄ (Ïª§Ïä§?∞Îßà?¥Ï?)
    $default = get_theme_mod('ft_default_og_image');
    if ($default) return $default;

    return '';
}

// wp_head?êÏÑú Í∏∞Î≥∏ Ï∫êÎÖ∏?àÏª¨ ?úÍ±∞ (Ï§ëÎ≥µ Î∞©Ï?)
remove_action('wp_head', 'rel_canonical');
