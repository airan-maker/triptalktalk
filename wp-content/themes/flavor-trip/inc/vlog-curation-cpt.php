<?php
/**
 * 커스텀 포스트 타입: 브이로그 큐레이션
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

// 아카이브 페이지에서 현재 언어만 표시
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if (!$query->is_post_type_archive('vlog_curation')) return;

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
    // CPT 등록
    register_post_type('vlog_curation', [
        'labels' => [
            'name'               => __('브이로그', 'flavor-trip'),
            'singular_name'      => __('브이로그', 'flavor-trip'),
            'add_new'            => __('새 브이로그 추가', 'flavor-trip'),
            'add_new_item'       => __('새 브이로그 추가', 'flavor-trip'),
            'edit_item'          => __('브이로그 편집', 'flavor-trip'),
            'new_item'           => __('새 브이로그', 'flavor-trip'),
            'view_item'          => __('브이로그 보기', 'flavor-trip'),
            'search_items'       => __('브이로그 검색', 'flavor-trip'),
            'not_found'          => __('브이로그가 없습니다.', 'flavor-trip'),
            'not_found_in_trash' => __('휴지통에 브이로그가 없습니다.', 'flavor-trip'),
            'all_items'          => __('모든 브이로그', 'flavor-trip'),
            'menu_name'          => __('브이로그', 'flavor-trip'),
        ],
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'vlogs', 'with_front' => false],
        'menu_icon'          => 'dashicons-video-alt3',
        'menu_position'      => 7,
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest'       => true,
        'taxonomies'         => ['destination'],
    ]);

    // 브이로그 카테고리 택소노미 (비계층형, 태그 형태)
    register_taxonomy('vlog_category', 'vlog_curation', [
        'labels' => [
            'name'          => __('브이로그 카테고리', 'flavor-trip'),
            'singular_name' => __('브이로그 카테고리', 'flavor-trip'),
            'search_items'  => __('카테고리 검색', 'flavor-trip'),
            'all_items'     => __('모든 카테고리', 'flavor-trip'),
            'edit_item'     => __('카테고리 편집', 'flavor-trip'),
            'add_new_item'  => __('새 카테고리 추가', 'flavor-trip'),
            'new_item_name' => __('새 카테고리 이름', 'flavor-trip'),
            'menu_name'     => __('브이로그 카테고리', 'flavor-trip'),
        ],
        'hierarchical' => false,
        'public'       => true,
        'rewrite'      => ['slug' => 'vlog-category'],
        'show_in_rest' => true,
    ]);
});
