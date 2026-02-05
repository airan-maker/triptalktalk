<?php
/**
 * ?�마 기본 ?�정
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

add_action('after_setup_theme', function () {
    // 번역 지??    load_theme_textdomain('flavor-trip', FT_DIR . '/languages');

    // ?�마 기능
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

    // 커스?� ?��?지 ?�기
    add_image_size('ft-card', 600, 400, true);
    add_image_size('ft-hero', 1920, 800, true);
    add_image_size('ft-gallery', 800, 600, true);
    add_image_size('ft-thumbnail-sm', 300, 200, true);

    // 메뉴 ?�록
    register_nav_menus([
        'primary'   => __('메인 메뉴', 'flavor-trip'),
        'footer'    => __('?�터 메뉴', 'flavor-trip'),
    ]);
});

// 발췌�?길이
add_filter('excerpt_length', function () {
    return 30;
}, 999);

add_filter('excerpt_more', function () {
    return '&hellip;';
});

// jQuery ?�론?�엔???�거 (관리자 ?�외)
add_action('wp_enqueue_scripts', function () {
    if (!is_admin()) {
        wp_deregister_script('jquery');
    }
}, 1);
