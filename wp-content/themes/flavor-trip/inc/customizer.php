<?php
/**
 * ì»¤ìŠ¤?°ë§ˆ?´ì?: ?Œì…œ ë§í¬, ì§€??API, SEO ?¤ì •
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // === ?¹ì…˜: SEO ?¤ì • ===
    $wp_customize->add_section('ft_seo', [
        'title'    => __('SEO ?¤ì •', 'flavor-trip'),
        'priority' => 30,
    ]);

    $wp_customize->add_setting('ft_google_verify', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('ft_google_verify', [
        'label'   => __('Google ?¬ì´???¸ì¦ ì½”ë“œ', 'flavor-trip'),
        'section' => 'ft_seo',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('ft_naver_verify', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('ft_naver_verify', [
        'label'   => __('Naver ?¬ì´???¸ì¦ ì½”ë“œ', 'flavor-trip'),
        'section' => 'ft_seo',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('ft_default_og_image', ['sanitize_callback' => 'esc_url_raw']);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'ft_default_og_image', [
        'label'   => __('ê¸°ë³¸ OG ?´ë?ì§€', 'flavor-trip'),
        'description' => __('ê°œë³„ ?€???´ë?ì§€ê°€ ?†ì„ ???¬ìš©?©ë‹ˆ?? (1200x630 ê¶Œì¥)', 'flavor-trip'),
        'section' => 'ft_seo',
    ]));

    // === ?¹ì…˜: ?Œì…œ ë¯¸ë””??===
    $wp_customize->add_section('ft_social', [
        'title'    => __('?Œì…œ ë¯¸ë””??, 'flavor-trip'),
        'priority' => 35,
    ]);

    $social_fields = [
        'ft_social_instagram' => 'Instagram URL',
        'ft_social_youtube'   => 'YouTube URL',
        'ft_social_blog'      => 'ë¸”ë¡œê·?URL',
    ];

    foreach ($social_fields as $id => $label) {
        $wp_customize->add_setting($id, ['sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control($id, [
            'label'   => $label,
            'section' => 'ft_social',
            'type'    => 'url',
        ]);
    }

    // === ?¹ì…˜: ì§€??API ===
    $wp_customize->add_section('ft_maps', [
        'title'    => __('ì§€???¤ì •', 'flavor-trip'),
        'priority' => 40,
    ]);

    $wp_customize->add_setting('ft_kakao_map_key', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('ft_kakao_map_key', [
        'label'       => __('ì¹´ì¹´?¤ë§µ JavaScript ????, 'flavor-trip'),
        'description' => __('ì¹´ì¹´??ê°œë°œ???¬ì´?¸ì—??ë°œê¸‰ë°›ì? JavaScript ??, 'flavor-trip'),
        'section'     => 'ft_maps',
        'type'        => 'text',
    ]);

    $wp_customize->add_setting('ft_google_map_key', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('ft_google_map_key', [
        'label'       => __('Google Maps API ??(?´ë°±)', 'flavor-trip'),
        'description' => __('ì¹´ì¹´?¤ë§µ ?¤ê? ?†ì„ ???¬ìš©?©ë‹ˆ??, 'flavor-trip'),
        'section'     => 'ft_maps',
        'type'        => 'text',
    ]);

    // === ?¹ì…˜: ?ˆì–´ë¡??¤ì • ===
    $wp_customize->add_section('ft_hero', [
        'title'    => __('???ˆì–´ë¡??¹ì…˜', 'flavor-trip'),
        'priority' => 45,
    ]);

    $wp_customize->add_setting('ft_hero_title', [
        'default'           => 'Traveler\'s Real Talk',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('ft_hero_title', [
        'label'   => __('?ˆì–´ë¡??œëª©', 'flavor-trip'),
        'section' => 'ft_hero',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('ft_hero_subtitle', [
        'default'           => '?¬í–‰ ?¼ì •???¤ë§ˆ?¸í•˜ê²? ?¬í–‰?ì˜ ì§„ì§œ ?´ì•¼ê¸°ë? ë§Œë‚˜ë³´ì„¸??',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('ft_hero_subtitle', [
        'label'   => __('?ˆì–´ë¡?ë¶€?œëª©', 'flavor-trip'),
        'section' => 'ft_hero',
        'type'    => 'textarea',
    ]);

    $wp_customize->add_setting('ft_hero_image', ['sanitize_callback' => 'esc_url_raw']);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'ft_hero_image', [
        'label'   => __('?ˆì–´ë¡?ë°°ê²½ ?´ë?ì§€', 'flavor-trip'),
        'section' => 'ft_hero',
    ]));
});
