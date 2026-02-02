<?php
/**
 * 지도 영역
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$lat  = get_query_var('ft_map_lat');
$lng  = get_query_var('ft_map_lng');
$zoom = get_query_var('ft_map_zoom', 12);

if (!$lat || !$lng) return;
?>

<section class="itinerary-map" id="itinerary-map-section">
    <h2 class="section-heading"><?php esc_html_e('위치', 'flavor-trip'); ?></h2>
    <div id="ft-map"
         class="map-container"
         data-lat="<?php echo esc_attr($lat); ?>"
         data-lng="<?php echo esc_attr($lng); ?>"
         data-zoom="<?php echo esc_attr($zoom); ?>"
         data-title="<?php echo esc_attr(get_the_title()); ?>">
        <noscript>
            <p><?php esc_html_e('지도를 보려면 JavaScript를 활성화하세요.', 'flavor-trip'); ?></p>
        </noscript>
    </div>
</section>
