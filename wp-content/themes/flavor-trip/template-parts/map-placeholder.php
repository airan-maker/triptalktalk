<?php
/**
 * ì§€???ì—­
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

$lat  = get_query_var('ft_map_lat');
$lng  = get_query_var('ft_map_lng');
$zoom = get_query_var('ft_map_zoom', 12);

if (!$lat || !$lng) return;
?>

<section class="itinerary-map" id="itinerary-map-section">
    <h2 class="section-heading"><?php esc_html_e('?„ì¹˜', 'flavor-trip'); ?></h2>
    <div id="ft-map"
         class="map-container"
         data-lat="<?php echo esc_attr($lat); ?>"
         data-lng="<?php echo esc_attr($lng); ?>"
         data-zoom="<?php echo esc_attr($zoom); ?>"
         data-title="<?php echo esc_attr(get_the_title()); ?>">
        <noscript>
            <p><?php esc_html_e('ì§€?„ë? ë³´ë ¤ë©?JavaScriptë¥??œì„±?”í•˜?¸ìš”.', 'flavor-trip'); ?></p>
        </noscript>
    </div>
</section>
