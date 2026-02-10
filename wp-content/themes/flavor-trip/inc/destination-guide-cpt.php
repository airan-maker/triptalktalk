<?php
/**
 * 커스텀 포스트 타입: 도시 가이드
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

// 아카이브 페이지에서 현재 언어만 표시
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if (!$query->is_post_type_archive('destination_guide')) return;

    if (function_exists('pll_current_language')) {
        $lang = pll_current_language();
        $query->set('lang', $lang);

        if (taxonomy_exists('language')) {
            $query->set('tax_query', [[
                'taxonomy' => 'language',
                'field'    => 'slug',
                'terms'    => $lang,
            ]]);
        }
    }
});

add_action('init', function () {
    register_post_type('destination_guide', [
        'labels' => [
            'name'               => __('도시 가이드', 'flavor-trip'),
            'singular_name'      => __('도시 가이드', 'flavor-trip'),
            'add_new'            => __('새 가이드 추가', 'flavor-trip'),
            'add_new_item'       => __('새 도시 가이드 추가', 'flavor-trip'),
            'edit_item'          => __('도시 가이드 편집', 'flavor-trip'),
            'new_item'           => __('새 도시 가이드', 'flavor-trip'),
            'view_item'          => __('도시 가이드 보기', 'flavor-trip'),
            'search_items'       => __('도시 가이드 검색', 'flavor-trip'),
            'not_found'          => __('도시 가이드가 없습니다.', 'flavor-trip'),
            'not_found_in_trash' => __('휴지통에 도시 가이드가 없습니다.', 'flavor-trip'),
            'all_items'          => __('모든 가이드', 'flavor-trip'),
            'menu_name'          => __('도시 가이드', 'flavor-trip'),
        ],
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'city-guide', 'with_front' => false],
        'menu_icon'          => 'dashicons-location-alt',
        'menu_position'      => 6,
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest'       => true,
        'taxonomies'         => ['destination'],
    ]);
});
