<?php
/**
 * 커스텀 포스트 타입: 도시 가이드
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

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
