<?php
/**
 * 커스텀 택소노미: 여행지, 여행 스타일
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('init', function () {
    // 여행지 (계층형 - 카테고리 형태)
    register_taxonomy('destination', ['travel_itinerary'], [
        'labels' => [
            'name'              => __('여행지', 'flavor-trip'),
            'singular_name'     => __('여행지', 'flavor-trip'),
            'search_items'      => __('여행지 검색', 'flavor-trip'),
            'all_items'         => __('모든 여행지', 'flavor-trip'),
            'parent_item'       => __('상위 여행지', 'flavor-trip'),
            'parent_item_colon' => __('상위 여행지:', 'flavor-trip'),
            'edit_item'         => __('여행지 편집', 'flavor-trip'),
            'update_item'       => __('여행지 업데이트', 'flavor-trip'),
            'add_new_item'      => __('새 여행지 추가', 'flavor-trip'),
            'new_item_name'     => __('새 여행지 이름', 'flavor-trip'),
            'menu_name'         => __('여행지', 'flavor-trip'),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'destination', 'with_front' => false, 'hierarchical' => true],
    ]);

    // 여행 스타일 (비계층형 - 태그 형태)
    register_taxonomy('travel_style', ['travel_itinerary'], [
        'labels' => [
            'name'                       => __('여행 스타일', 'flavor-trip'),
            'singular_name'              => __('여행 스타일', 'flavor-trip'),
            'search_items'               => __('여행 스타일 검색', 'flavor-trip'),
            'popular_items'              => __('인기 여행 스타일', 'flavor-trip'),
            'all_items'                  => __('모든 여행 스타일', 'flavor-trip'),
            'edit_item'                  => __('여행 스타일 편집', 'flavor-trip'),
            'update_item'                => __('여행 스타일 업데이트', 'flavor-trip'),
            'add_new_item'               => __('새 여행 스타일 추가', 'flavor-trip'),
            'new_item_name'              => __('새 여행 스타일 이름', 'flavor-trip'),
            'separate_items_with_commas' => __('쉼표로 구분', 'flavor-trip'),
            'add_or_remove_items'        => __('여행 스타일 추가 또는 제거', 'flavor-trip'),
            'choose_from_most_used'      => __('자주 사용하는 스타일에서 선택', 'flavor-trip'),
            'menu_name'                  => __('여행 스타일', 'flavor-trip'),
        ],
        'hierarchical'      => false,
        'public'            => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'travel-style', 'with_front' => false],
    ]);
});
