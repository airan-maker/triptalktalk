<?php
/**
 * 커스텀 포스트 타입: 여행 일정
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('init', function () {
    register_post_type('travel_itinerary', [
        'labels' => [
            'name'               => __('여행 일정', 'flavor-trip'),
            'singular_name'      => __('여행 일정', 'flavor-trip'),
            'add_new'            => __('새 일정 추가', 'flavor-trip'),
            'add_new_item'       => __('새 여행 일정 추가', 'flavor-trip'),
            'edit_item'          => __('여행 일정 편집', 'flavor-trip'),
            'new_item'           => __('새 여행 일정', 'flavor-trip'),
            'view_item'          => __('여행 일정 보기', 'flavor-trip'),
            'search_items'       => __('여행 일정 검색', 'flavor-trip'),
            'not_found'          => __('여행 일정이 없습니다.', 'flavor-trip'),
            'not_found_in_trash' => __('휴지통에 여행 일정이 없습니다.', 'flavor-trip'),
            'all_items'          => __('모든 일정', 'flavor-trip'),
            'menu_name'          => __('여행 일정', 'flavor-trip'),
        ],
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'itinerary', 'with_front' => false],
        'menu_icon'          => 'dashicons-airplane',
        'menu_position'      => 5,
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions'],
        'show_in_rest'       => true,
        'taxonomies'         => ['destination', 'travel_style'],
    ]);
});

// 퍼마링크 자동 flush
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});
