<?php
/**
 * ì»¤ìŠ¤?€ ?ì†Œ?¸ë?: ?¬í–‰ì§€, ?¬í–‰ ?¤í??? *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

add_action('init', function () {
    // ?¬í–‰ì§€ (ê³„ì¸µ??- ì¹´í…Œê³ ë¦¬ ?•íƒœ)
    register_taxonomy('destination', ['travel_itinerary'], [
        'labels' => [
            'name'              => __('?¬í–‰ì§€', 'flavor-trip'),
            'singular_name'     => __('?¬í–‰ì§€', 'flavor-trip'),
            'search_items'      => __('?¬í–‰ì§€ ê²€??, 'flavor-trip'),
            'all_items'         => __('ëª¨ë“  ?¬í–‰ì§€', 'flavor-trip'),
            'parent_item'       => __('?ìœ„ ?¬í–‰ì§€', 'flavor-trip'),
            'parent_item_colon' => __('?ìœ„ ?¬í–‰ì§€:', 'flavor-trip'),
            'edit_item'         => __('?¬í–‰ì§€ ?¸ì§‘', 'flavor-trip'),
            'update_item'       => __('?¬í–‰ì§€ ?…ë°?´íŠ¸', 'flavor-trip'),
            'add_new_item'      => __('???¬í–‰ì§€ ì¶”ê?', 'flavor-trip'),
            'new_item_name'     => __('???¬í–‰ì§€ ?´ë¦„', 'flavor-trip'),
            'menu_name'         => __('?¬í–‰ì§€', 'flavor-trip'),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'destination', 'with_front' => false, 'hierarchical' => true],
    ]);

    // ?¬í–‰ ?¤í???(ë¹„ê³„ì¸µí˜• - ?œê·¸ ?•íƒœ)
    register_taxonomy('travel_style', ['travel_itinerary'], [
        'labels' => [
            'name'                       => __('?¬í–‰ ?¤í???, 'flavor-trip'),
            'singular_name'              => __('?¬í–‰ ?¤í???, 'flavor-trip'),
            'search_items'               => __('?¬í–‰ ?¤í???ê²€??, 'flavor-trip'),
            'popular_items'              => __('?¸ê¸° ?¬í–‰ ?¤í???, 'flavor-trip'),
            'all_items'                  => __('ëª¨ë“  ?¬í–‰ ?¤í???, 'flavor-trip'),
            'edit_item'                  => __('?¬í–‰ ?¤í????¸ì§‘', 'flavor-trip'),
            'update_item'                => __('?¬í–‰ ?¤í????…ë°?´íŠ¸', 'flavor-trip'),
            'add_new_item'               => __('???¬í–‰ ?¤í???ì¶”ê?', 'flavor-trip'),
            'new_item_name'              => __('???¬í–‰ ?¤í????´ë¦„', 'flavor-trip'),
            'separate_items_with_commas' => __('?¼í‘œë¡?êµ¬ë¶„', 'flavor-trip'),
            'add_or_remove_items'        => __('?¬í–‰ ?¤í???ì¶”ê? ?ëŠ” ?œê±°', 'flavor-trip'),
            'choose_from_most_used'      => __('?ì£¼ ?¬ìš©?˜ëŠ” ?¤í??¼ì—??? íƒ', 'flavor-trip'),
            'menu_name'                  => __('?¬í–‰ ?¤í???, 'flavor-trip'),
        ],
        'hierarchical'      => false,
        'public'            => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'travel-style', 'with_front' => false],
    ]);
});
