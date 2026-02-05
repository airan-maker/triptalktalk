<?php
/**
 * ì»¤ìŠ¤?€ ?¬ìŠ¤???€?? ?¬í–‰ ?¼ì •
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

add_action('init', function () {
    register_post_type('travel_itinerary', [
        'labels' => [
            'name'               => __('?¬í–‰ ?¼ì •', 'flavor-trip'),
            'singular_name'      => __('?¬í–‰ ?¼ì •', 'flavor-trip'),
            'add_new'            => __('???¼ì • ì¶”ê?', 'flavor-trip'),
            'add_new_item'       => __('???¬í–‰ ?¼ì • ì¶”ê?', 'flavor-trip'),
            'edit_item'          => __('?¬í–‰ ?¼ì • ?¸ì§‘', 'flavor-trip'),
            'new_item'           => __('???¬í–‰ ?¼ì •', 'flavor-trip'),
            'view_item'          => __('?¬í–‰ ?¼ì • ë³´ê¸°', 'flavor-trip'),
            'search_items'       => __('?¬í–‰ ?¼ì • ê²€??, 'flavor-trip'),
            'not_found'          => __('?¬í–‰ ?¼ì •???†ìŠµ?ˆë‹¤.', 'flavor-trip'),
            'not_found_in_trash' => __('?´ì??µì— ?¬í–‰ ?¼ì •???†ìŠµ?ˆë‹¤.', 'flavor-trip'),
            'all_items'          => __('ëª¨ë“  ?¼ì •', 'flavor-trip'),
            'menu_name'          => __('?¬í–‰ ?¼ì •', 'flavor-trip'),
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

// ?¼ë§ˆë§í¬ ?ë™ flush
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});
