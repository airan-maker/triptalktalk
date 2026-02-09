<?php
/**
 * CSS/JS 로드 (조건부) + 성능 최적화
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

// 리소스 힌트: preconnect, dns-prefetch
add_action('wp_head', function () {
    // Google Fonts
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    // Unsplash (이미지 CDN)
    echo '<link rel="dns-prefetch" href="https://images.unsplash.com">' . "\n";
    // Klook (제휴 링크)
    echo '<link rel="dns-prefetch" href="https://www.klook.com">' . "\n";
}, 1);

add_action('wp_enqueue_scripts', function () {
    // 메인 스타일
    wp_enqueue_style('flavor-trip-style', get_stylesheet_uri(), [], FT_VERSION);
    wp_enqueue_style('ft-main', FT_URI . '/assets/css/main.css', ['flavor-trip-style'], FT_VERSION);
    wp_enqueue_style('ft-responsive', FT_URI . '/assets/css/responsive.css', ['ft-main'], FT_VERSION);

    // 메인 JS
    wp_enqueue_script('ft-main', FT_URI . '/assets/js/main.js', [], FT_VERSION, true);

    // 여행 일정 아카이브 (벤토 그리드)
    if (is_post_type_archive('travel_itinerary') || is_tax('destination') || is_tax('travel_style')) {
        wp_enqueue_style('ft-bento-grid', FT_URI . '/assets/css/bento-grid.css', ['ft-main'], FT_VERSION);
    }

    // 여행 일정 상세 페이지 전용
    if (is_singular('travel_itinerary')) {
        wp_enqueue_style('ft-itinerary', FT_URI . '/assets/css/itinerary.css', ['ft-main'], FT_VERSION);
        wp_enqueue_script('ft-gallery', FT_URI . '/assets/js/gallery.js', [], FT_VERSION, true);

        // 지도 (단일 좌표 또는 spots 좌표가 있는 경우)
        $lat = get_post_meta(get_the_ID(), '_ft_map_lat', true);
        $lng = get_post_meta(get_the_ID(), '_ft_map_lng', true);

        // spots에서 좌표 존재 여부 확인
        $has_spot_coords = false;
        $days = get_post_meta(get_the_ID(), '_ft_days', true);
        if (is_array($days)) {
            foreach ($days as $d) {
                if (!empty($d['spots']) && is_array($d['spots'])) {
                    foreach ($d['spots'] as $s) {
                        if (!empty($s['lat']) && !empty($s['lng'])) {
                            $has_spot_coords = true;
                            break 2;
                        }
                    }
                }
            }
        }

        if ($lat && $lng || $has_spot_coords) {
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

// 스크립트에 defer 속성 추가 (성능 최적화)
add_filter('script_loader_tag', function ($tag, $handle) {
    // 외부 스크립트에는 적용하지 않음
    $defer_handles = ['ft-main', 'ft-gallery', 'ft-map'];
    if (in_array($handle, $defer_handles, true)) {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 2);
