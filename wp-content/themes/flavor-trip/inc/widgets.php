<?php
/**
 * 위젯 영역 등록
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('메인 사이드바', 'flavor-trip'),
        'id'            => 'sidebar-main',
        'description'   => __('블로그 페이지 사이드바', 'flavor-trip'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => __('푸터 위젯 1', 'flavor-trip'),
        'id'            => 'footer-1',
        'description'   => __('푸터 첫 번째 영역', 'flavor-trip'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
});
