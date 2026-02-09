<?php
/**
 * SEO: Open Graph, Twitter Card, 메타 설명, 캐노니컬, robots, hreflang
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

/**
 * SEO 메타 태그 출력
 */
function ft_seo_meta_tags() {
    $description = ft_get_meta_description();
    $canonical   = ft_get_canonical_url();
    $og_image    = ft_get_og_image();
    $og_title    = ft_get_og_title();
    $og_type     = is_single() || is_singular('travel_itinerary') ? 'article' : 'website';
    $site_name   = get_bloginfo('name');

    // 메타 설명
    if ($description) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }

    // 캐노니컬
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

    // og:locale - 동적 처리 (다국어 지원)
    $locale = get_locale();
    echo '<meta property="og:locale" content="' . esc_attr($locale) . '">' . "\n";

    // og:locale:alternate - 다른 언어 버전
    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(['raw' => 1]);
        if ($languages) {
            foreach ($languages as $lang) {
                if (!$lang['current_lang']) {
                    echo '<meta property="og:locale:alternate" content="' . esc_attr($lang['locale']) . '">' . "\n";
                }
            }
        }
    }

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

    // hreflang 태그 (다국어 SEO)
    ft_output_hreflang_tags();

    // 사이트 인증 메타
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
 * hreflang 태그 출력
 */
function ft_output_hreflang_tags() {
    if (!function_exists('pll_the_languages')) return;

    $languages = pll_the_languages(['raw' => 1]);
    if (empty($languages)) return;

    foreach ($languages as $lang) {
        if (!empty($lang['url'])) {
            $hreflang = $lang['slug'];
            // 특수 locale 매핑
            $hreflang_map = [
                'zh-cn' => 'zh-Hans',
            ];
            $hreflang = $hreflang_map[$hreflang] ?? $hreflang;
            echo '<link rel="alternate" hreflang="' . esc_attr($hreflang) . '" href="' . esc_url($lang['url']) . '">' . "\n";
        }
    }

    // x-default (기본 언어 = 한국어)
    foreach ($languages as $lang) {
        if ($lang['slug'] === 'ko' && !empty($lang['url'])) {
            echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($lang['url']) . '">' . "\n";
            break;
        }
    }
}

/**
 * 메타 설명 생성
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
        return __('다양한 여행 일정과 코스를 탐색하고 나만의 여행을 계획해보세요.', 'flavor-trip');
    }

    return '';
}

/**
 * OG 제목 생성
 */
function ft_get_og_title() {
    if (is_singular()) {
        return get_the_title();
    }

    if (is_tax() || is_category() || is_tag()) {
        return single_term_title('', false);
    }

    if (is_post_type_archive('travel_itinerary')) {
        return __('여행 일정', 'flavor-trip');
    }

    if (is_search()) {
        return sprintf(__('"%s" 검색 결과', 'flavor-trip'), get_search_query());
    }

    return get_bloginfo('name');
}

/**
 * 캐노니컬 URL
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
 * OG 이미지
 */
function ft_get_og_image() {
    if (is_singular() && has_post_thumbnail()) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'ft-hero');
        if ($img) return $img[0];
    }

    // 여행 일정: 여행지 기반 폴백 이미지
    if (is_singular('travel_itinerary') && function_exists('ft_get_destination_image')) {
        $fallback = ft_get_destination_image(get_the_ID());
        if ($fallback) return $fallback;
    }

    // 기본 OG 이미지 (커스터마이저)
    $default = get_theme_mod('ft_default_og_image');
    if ($default) return $default;

    return '';
}

// wp_head에서 기본 캐노니컬 제거 (중복 방지)
remove_action('wp_head', 'rel_canonical');
