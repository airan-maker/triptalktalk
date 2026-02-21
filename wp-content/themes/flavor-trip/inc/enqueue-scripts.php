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
    if (is_post_type_archive('travel_itinerary') || is_post_type_archive('vlog_curation') || is_tax('destination') || is_tax('travel_style')) {
        wp_enqueue_style('ft-bento-grid', FT_URI . '/assets/css/bento-grid.css', ['ft-main'], FT_VERSION);
    }

    // 도시 가이드
    if (is_singular('destination_guide') || is_post_type_archive('destination_guide')) {
        wp_enqueue_style('ft-guide', FT_URI . '/assets/css/guide.css', ['ft-main'], FT_VERSION);
    }
    if (is_singular('destination_guide')) {
        wp_enqueue_script('ft-guide', FT_URI . '/assets/js/guide.js', [], FT_VERSION, true);

        // 구글맵 + 가이드 맵 JS
        $google_key = get_theme_mod('ft_google_map_key');
        $guide_map_deps = ['ft-guide'];
        if ($google_key) {
            wp_enqueue_script('google-maps-guide', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($google_key), [], null, true);
            $guide_map_deps[] = 'google-maps-guide';
        }
        wp_enqueue_script('ft-guide-map', FT_URI . '/assets/js/guide-map.js', $guide_map_deps, FT_VERSION, true);

        // 가이드 데이터를 JS에 전달
        $guide_data = get_post_meta(get_the_ID(), '_ft_guide_data', true);
        $map_items = [];
        if (!empty($guide_data)) {
            foreach (['places', 'restaurants', 'hotels'] as $type) {
                if (!empty($guide_data[$type])) {
                    foreach ($guide_data[$type] as $item) {
                        if (!empty($item['lat']) && !empty($item['lng'])) {
                            $item['_type'] = $type;
                            $map_items[] = $item;
                        }
                    }
                }
            }
        }

        $lang = function_exists('pll_current_language') ? pll_current_language() : 'ko';
        wp_localize_script('ft-guide-map', 'ftGuideMap', [
            'items'    => $map_items,
            'klookAid' => get_theme_mod('ft_klook_aid', '6yjZP2Ac'),
            'labels'   => [
                'must_do'      => __('꼭 해볼 것', 'flavor-trip'),
                'popular_menu' => __('인기 메뉴', 'flavor-trip'),
                'detail'       => __('상세 정보', 'flavor-trip'),
                'view_on_map'  => __('구글맵에서 보기', 'flavor-trip'),
                'book_ticket'  => __('예약/입장권 보기', 'flavor-trip'),
                'family'       => __('가족', 'flavor-trip'),
                'couple'       => __('커플', 'flavor-trip'),
                'solo'         => __('솔로', 'flavor-trip'),
                'friends'      => __('친구', 'flavor-trip'),
                'filial'       => __('효도', 'flavor-trip'),
            ],
        ]);
    }

    // 브이로그 큐레이션
    if (is_singular('vlog_curation') || is_post_type_archive('vlog_curation')) {
        wp_enqueue_style('ft-vlog', FT_URI . '/assets/css/vlog.css', ['ft-main'], FT_VERSION);
    }
    if (is_singular('vlog_curation')) {
        wp_enqueue_script('ft-vlog', FT_URI . '/assets/js/vlog.js', [], FT_VERSION, true);

        $vlog_spots = get_post_meta(get_the_ID(), '_ft_vlog_spots', true) ?: [];
        $has_vlog_coords = false;
        foreach ($vlog_spots as $s) {
            if (!empty($s['lat']) && !empty($s['lng'])) {
                $has_vlog_coords = true;
                break;
            }
        }

        if ($has_vlog_coords) {
            $google_key = get_theme_mod('ft_google_map_key');
            if ($google_key) {
                wp_enqueue_script('google-maps-vlog', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($google_key), [], null, true);
            }
        }

        wp_localize_script('ft-vlog', 'ftVlogData', [
            'youtubeId' => get_post_meta(get_the_ID(), '_ft_vlog_youtube_id', true),
            'spots'     => $vlog_spots,
            'labels'    => [
                'view_on_map' => __('구글맵에서 보기', 'flavor-trip'),
            ],
        ]);
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
    $defer_handles = ['ft-main', 'ft-gallery', 'ft-map', 'ft-guide', 'ft-guide-map', 'ft-vlog'];
    if (in_array($handle, $defer_handles, true)) {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 2);
