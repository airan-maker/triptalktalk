<?php
/**
 * CSS/JS 로드 (조건부)
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
    // 메인 ?��???    wp_enqueue_style('flavor-trip-style', get_stylesheet_uri(), [], FT_VERSION);
    wp_enqueue_style('ft-main', FT_URI . '/assets/css/main.css', ['flavor-trip-style'], FT_VERSION);
    wp_enqueue_style('ft-responsive', FT_URI . '/assets/css/responsive.css', ['ft-main'], FT_VERSION);

    // 메인 JS
    wp_enqueue_script('ft-main', FT_URI . '/assets/js/main.js', [], FT_VERSION, true);

    // ?�행 ?�정 ?�세 ?�이지 ?�용
    if (is_singular('travel_itinerary')) {
        wp_enqueue_style('ft-itinerary', FT_URI . '/assets/css/itinerary.css', ['ft-main'], FT_VERSION);
        wp_enqueue_script('ft-gallery', FT_URI . '/assets/js/gallery.js', [], FT_VERSION, true);

        // 지??(좌표가 ?�는 경우�?
        $lat = get_post_meta(get_the_ID(), '_ft_map_lat', true);
        $lng = get_post_meta(get_the_ID(), '_ft_map_lng', true);
        if ($lat && $lng) {
            $kakao_key = get_theme_mod('ft_kakao_map_key');
            $google_key = get_theme_mod('ft_google_map_key');

            if ($kakao_key) {
                wp_enqueue_script('kakao-maps', 'https://dapi.kakao.com/v2/maps/sdk.js?appkey=' . esc_attr($kakao_key), [], null, true);
            } elseif ($google_key) {
                wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($google_key), [], null, true);
            }

            wp_enqueue_script('ft-map', FT_URI . '/assets/js/map.js', [], FT_VERSION, true);
            wp_localize_script('ft-map', 'ftMapConfig', [
                'provider' => $kakao_key ? 'kakao' : ($google_key ? 'google' : 'none'),
            ]);
        }
    }
});
