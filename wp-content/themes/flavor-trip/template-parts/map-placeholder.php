<?php
/**
 * 지도 영역 (spots 동선 + 단일 마커 호환)
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$lat   = get_query_var('ft_map_lat');
$lng   = get_query_var('ft_map_lng');
$zoom  = get_query_var('ft_map_zoom', 12);
$spots = get_query_var('ft_map_spots', []);

if (!$lat && !$lng && empty($spots)) return;
?>

<section class="itinerary-map" id="itinerary-map-section">
    <h2 class="section-heading"><?php echo !empty($spots) ? esc_html__('여행 동선', 'flavor-trip') : esc_html__('위치', 'flavor-trip'); ?></h2>
    <div id="ft-map"
         class="map-container"
         data-lat="<?php echo esc_attr($lat); ?>"
         data-lng="<?php echo esc_attr($lng); ?>"
         data-zoom="<?php echo esc_attr($zoom); ?>"
         data-title="<?php echo esc_attr(get_the_title()); ?>"
         <?php if (!empty($spots)) : ?>
         data-spots="<?php echo esc_attr(wp_json_encode($spots)); ?>"
         <?php endif; ?>>
        <noscript>
            <p><?php esc_html_e('지도를 보려면 JavaScript를 활성화하세요.', 'flavor-trip'); ?></p>
        </noscript>
    </div>
</section>
