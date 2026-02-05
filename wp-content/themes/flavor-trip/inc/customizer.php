<?php
/**
 * 커스터마이저: 소셜 링크, 지도 API, SEO 설정
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // === 섹션: SEO 설정 ===
    $wp_customize->add_section('ft_seo', [
        'title'    => __('SEO 설정', 'flavor-trip'),
        'priority' => 30,
    ]);

    $wp_customize->add_setting('ft_google_verify', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('ft_google_verify', [
        'label'   => __('Google 사이트 인증 코드', 'flavor-trip'),
        'section' => 'ft_seo',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('ft_naver_verify', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('ft_naver_verify', [
        'label'   => __('Naver 사이트 인증 코드', 'flavor-trip'),
        'section' => 'ft_seo',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('ft_default_og_image', ['sanitize_callback' => 'esc_url_raw']);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'ft_default_og_image', [
        'label'   => __('기본 OG 이미지', 'flavor-trip'),
        'description' => __('개별 대표 이미지가 없을 때 사용됩니다. (1200x630 권장)', 'flavor-trip'),
        'section' => 'ft_seo',
    ]));

    // === 섹션: 소셜 미디어 ===
    $wp_customize->add_section('ft_social', [
        'title'    => __('소셜 미디어', 'flavor-trip'),
        'priority' => 35,
    ]);

    $social_fields = [
        'ft_social_instagram' => 'Instagram URL',
        'ft_social_youtube'   => 'YouTube URL',
        'ft_social_blog'      => '블로그 URL',
    ];

    foreach ($social_fields as $id => $label) {
        $wp_customize->add_setting($id, ['sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control($id, [
            'label'   => $label,
            'section' => 'ft_social',
            'type'    => 'url',
        ]);
    }

    // === 섹션: 지도 API ===
    $wp_customize->add_section('ft_maps', [
        'title'    => __('지도 설정', 'flavor-trip'),
        'priority' => 40,
    ]);

    $wp_customize->add_setting('ft_kakao_map_key', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('ft_kakao_map_key', [
        'label'       => __('카카오맵 JavaScript 앱 키', 'flavor-trip'),
        'description' => __('카카오 개발자 사이트에서 발급받은 JavaScript 키', 'flavor-trip'),
        'section'     => 'ft_maps',
        'type'        => 'text',
    ]);

    $wp_customize->add_setting('ft_google_map_key', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('ft_google_map_key', [
        'label'       => __('Google Maps API 키 (폴백)', 'flavor-trip'),
        'description' => __('카카오맵 키가 없을 때 사용됩니다', 'flavor-trip'),
        'section'     => 'ft_maps',
        'type'        => 'text',
    ]);

    // === 섹션: 제휴 마케팅 ===
    $wp_customize->add_section('ft_affiliate', [
        'title'       => __('제휴 마케팅', 'flavor-trip'),
        'description' => __('제휴 링크에 사용할 ID를 설정합니다.', 'flavor-trip'),
        'priority'    => 42,
    ]);

    $wp_customize->add_setting('ft_klook_aid', ['sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('ft_klook_aid', [
        'label'       => __('Klook AID (Affiliate ID)', 'flavor-trip'),
        'description' => __('Klook 제휴 대시보드에서 확인한 AID 값', 'flavor-trip'),
        'section'     => 'ft_affiliate',
        'type'        => 'text',
    ]);

    // === 섹션: 히어로 설정 ===
    $wp_customize->add_section('ft_hero', [
        'title'    => __('홈 히어로 섹션', 'flavor-trip'),
        'priority' => 45,
    ]);

    $wp_customize->add_setting('ft_hero_title', [
        'default'           => '맛있는 여행의 시작',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('ft_hero_title', [
        'label'   => __('히어로 제목', 'flavor-trip'),
        'section' => 'ft_hero',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('ft_hero_subtitle', [
        'default'           => '특별한 여행 일정을 만나보세요. 전문가가 설계한 코스로 잊지 못할 여행을 떠나세요.',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('ft_hero_subtitle', [
        'label'   => __('히어로 부제목', 'flavor-trip'),
        'section' => 'ft_hero',
        'type'    => 'textarea',
    ]);

    $wp_customize->add_setting('ft_hero_image', ['sanitize_callback' => 'esc_url_raw']);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'ft_hero_image', [
        'label'   => __('히어로 배경 이미지', 'flavor-trip'),
        'section' => 'ft_hero',
    ]));
});
