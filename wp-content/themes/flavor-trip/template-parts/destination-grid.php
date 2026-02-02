<?php
/**
 * 여행지 카테고리 그리드
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$destinations = get_terms([
    'taxonomy'   => 'destination',
    'hide_empty' => true,
    'parent'     => 0,
    'number'     => 6,
    'orderby'    => 'count',
    'order'      => 'DESC',
]);

if (is_wp_error($destinations) || empty($destinations)) {
    return;
}
?>

<section class="section section-destinations">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('인기 여행지', 'flavor-trip'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('어디로 떠나볼까요?', 'flavor-trip'); ?></p>

        <div class="destination-grid">
            <?php foreach ($destinations as $dest) :
                $image_id = get_term_meta($dest->term_id, 'ft_destination_image', true);
                $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'ft-card') : '';
            ?>
                <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="destination-card" <?php if ($image_url) : ?>style="background-image: url('<?php echo esc_url($image_url); ?>')"<?php endif; ?>>
                    <div class="destination-overlay"></div>
                    <div class="destination-info">
                        <h3 class="destination-name"><?php echo esc_html($dest->name); ?></h3>
                        <span class="destination-count"><?php printf(esc_html__('%d개의 일정', 'flavor-trip'), $dest->count); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
