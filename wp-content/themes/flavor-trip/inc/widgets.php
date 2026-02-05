<?php
/**
 * ?„ì ¯ ?ì—­ ?±ë¡
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('ë©”ì¸ ?¬ì´?œë°”', 'flavor-trip'),
        'id'            => 'sidebar-main',
        'description'   => __('ë¸”ë¡œê·??˜ì´ì§€ ?¬ì´?œë°”', 'flavor-trip'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => __('?¸í„° ?„ì ¯ 1', 'flavor-trip'),
        'id'            => 'footer-1',
        'description'   => __('?¸í„° ì²?ë²ˆì§¸ ?ì—­', 'flavor-trip'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
});
