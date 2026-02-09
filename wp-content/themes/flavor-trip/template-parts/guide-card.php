<?php
/**
 * 도시 가이드 — 아카이브 카드
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$city    = get_post_meta(get_the_ID(), '_ft_guide_city', true);
$country = get_post_meta(get_the_ID(), '_ft_guide_country', true);
$data    = get_post_meta(get_the_ID(), '_ft_guide_data', true);

$places_count      = !empty($data['places']) ? count($data['places']) : 0;
$restaurants_count = !empty($data['restaurants']) ? count($data['restaurants']) : 0;
$hotels_count      = !empty($data['hotels']) ? count($data['hotels']) : 0;

// 이미지
$image_url = '';
if (has_post_thumbnail()) {
    $image_url = get_the_post_thumbnail_url(get_the_ID(), 'ft-card');
}
if (!$image_url) {
    $image_url = ft_get_destination_image(get_the_ID());
}
?>

<a href="<?php the_permalink(); ?>" class="guide-card">
    <div class="guide-card__image">
        <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
        <?php if ($country) : ?>
            <span class="guide-card__country"><?php echo esc_html($country); ?></span>
        <?php endif; ?>
    </div>
    <div class="guide-card__body">
        <h3 class="guide-card__title"><?php the_title(); ?></h3>
        <div class="guide-card__counts">
            <?php if ($places_count) : ?>
                <span><?php printf(esc_html__('%d곳', 'flavor-trip'), $places_count); ?> <?php esc_html_e('관광지', 'flavor-trip'); ?></span>
            <?php endif; ?>
            <?php if ($restaurants_count) : ?>
                <span><?php printf(esc_html__('%d곳', 'flavor-trip'), $restaurants_count); ?> <?php esc_html_e('식당', 'flavor-trip'); ?></span>
            <?php endif; ?>
            <?php if ($hotels_count) : ?>
                <span><?php printf(esc_html__('%d곳', 'flavor-trip'), $hotels_count); ?> <?php esc_html_e('호텔', 'flavor-trip'); ?></span>
            <?php endif; ?>
        </div>
    </div>
</a>
