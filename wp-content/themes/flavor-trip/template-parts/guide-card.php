<?php
/**
 * 도시 가이드 — 카드 (추천 여행 일정과 동일 스타일)
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$city    = get_post_meta(get_the_ID(), '_ft_guide_city', true);
$country = get_post_meta(get_the_ID(), '_ft_guide_country', true);
$intro   = get_post_meta(get_the_ID(), '_ft_guide_intro', true);
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

<article id="post-<?php the_ID(); ?>" <?php post_class('card card-guide'); ?>>
    <a href="<?php the_permalink(); ?>" class="card-image">
        <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
        <?php if ($country) : ?>
            <span class="card-badge"><?php echo esc_html($country); ?></span>
        <?php endif; ?>
    </a>

    <div class="card-body">
        <?php if ($city) : ?>
            <div class="card-tags">
                <span class="tag tag-sm"><?php echo esc_html($city); ?></span>
            </div>
        <?php endif; ?>

        <h3 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <?php if ($intro) : ?>
            <p class="card-excerpt"><?php echo esc_html(wp_trim_words($intro, 20)); ?></p>
        <?php endif; ?>

        <div class="card-footer">
            <?php if ($places_count) : ?>
                <span class="card-meta-item"><?php esc_html_e('관광지', 'flavor-trip'); ?> <?php echo $places_count; ?></span>
            <?php endif; ?>
            <?php if ($restaurants_count) : ?>
                <span class="card-meta-item"><?php esc_html_e('식당', 'flavor-trip'); ?> <?php echo $restaurants_count; ?></span>
            <?php endif; ?>
            <?php if ($hotels_count) : ?>
                <span class="card-meta-item"><?php esc_html_e('호텔', 'flavor-trip'); ?> <?php echo $hotels_count; ?></span>
            <?php endif; ?>
        </div>
    </div>
</article>
