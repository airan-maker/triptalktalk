<?php
/**
 * 테마 기본 설정
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('after_setup_theme', function () {
    // 번역 지원
    load_theme_textdomain('flavor-trip', FT_DIR . '/languages');

    // 테마 기능
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // 커스텀 이미지 크기
    add_image_size('ft-card', 600, 400, true);
    add_image_size('ft-hero', 1920, 800, true);
    add_image_size('ft-gallery', 800, 600, true);
    add_image_size('ft-thumbnail-sm', 300, 200, true);

    // 메뉴 등록
    register_nav_menus([
        'primary'   => __('메인 메뉴', 'flavor-trip'),
        'footer'    => __('푸터 메뉴', 'flavor-trip'),
    ]);
});

// 발췌문 길이
add_filter('excerpt_length', function () {
    return 30;
}, 999);

add_filter('excerpt_more', function () {
    return '&hellip;';
});

// jQuery 프론트엔드 제거 (관리자 제외)
add_action('wp_enqueue_scripts', function () {
    if (!is_admin()) {
        wp_deregister_script('jquery');
    }
}, 1);

// Polylang: 브라우저 언어 자동 감지 활성화
add_filter('pll_option_browser', '__return_true');

// Polylang: 홈페이지 첫 방문 시 브라우저 언어로 리다이렉트
add_filter('pll_option_redirect_lang', '__return_true');

// robots.txt 커스터마이징
add_filter('robots_txt', function ($output, $public) {
    $site_url = home_url('/');
    $output  = "User-agent: *\n";
    $output .= "Allow: /\n";
    $output .= "Disallow: /wp-admin/\n";
    $output .= "Allow: /wp-admin/admin-ajax.php\n";
    $output .= "Disallow: /wp-includes/\n";
    $output .= "Disallow: /?s=\n";
    $output .= "Disallow: /search/\n\n";
    $output .= "# AI Crawlers\n";
    $output .= "User-agent: GPTBot\n";
    $output .= "Allow: /\n\n";
    $output .= "User-agent: ChatGPT-User\n";
    $output .= "Allow: /\n\n";
    $output .= "User-agent: Google-Extended\n";
    $output .= "Allow: /\n\n";
    $output .= "User-agent: PerplexityBot\n";
    $output .= "Allow: /\n\n";
    $output .= "User-agent: ClaudeBot\n";
    $output .= "Allow: /\n\n";
    $output .= "Sitemap: {$site_url}sitemap.xml\n";
    return $output;
}, 10, 2);

// 페이지네이션 SEO: rel=prev/next
add_action('wp_head', function () {
    if (!is_paged() && !is_archive()) return;

    global $wp_query;
    $paged = max(1, get_query_var('paged'));
    $max = $wp_query->max_num_pages;

    if ($paged > 1) {
        $prev_url = get_pagenum_link($paged - 1);
        echo '<link rel="prev" href="' . esc_url($prev_url) . '">' . "\n";
    }
    if ($paged < $max) {
        $next_url = get_pagenum_link($paged + 1);
        echo '<link rel="next" href="' . esc_url($next_url) . '">' . "\n";
    }
});
