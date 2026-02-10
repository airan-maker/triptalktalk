<?php
/**
 * Bento Grid 카드 템플릿
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$post_id = get_the_ID();
$counter = get_query_var('bento_counter', 1);

// 이미지 URL 가져오기 (썸네일 → 여행지 기반 폴백 → 한국어 원본 폴백)
$image_url = '';
if (has_post_thumbnail($post_id)) {
    $image_url = get_the_post_thumbnail_url($post_id, 'large');
}
if (!$image_url) {
    $image_url = ft_get_destination_image($post_id);
}

$duration = get_post_meta($post_id, '_ft_duration', true);
$price = get_post_meta($post_id, '_ft_price_range', true);
$difficulty = get_post_meta($post_id, '_ft_difficulty', true);
$dest_terms = get_the_terms($post_id, 'destination');

// 카드 사이즈 결정 (벤토 패턴)
$card_class = '';
if ($counter === 1) {
    $card_class = 'bento-card--featured';
} elseif ($counter === 4 || $counter === 9) {
    $card_class = 'bento-card--wide';
} elseif ($counter === 5 || $counter === 10) {
    $card_class = 'bento-card--tall';
}
?>

<article class="bento-card <?php echo esc_attr($card_class); ?>">
    <a href="<?php the_permalink(); ?>">
        <div class="bento-card-image">
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
        </div>
        <div class="bento-card-overlay"></div>

        <?php if ($duration) : ?>
            <span class="bento-card-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12,6 12,12 16,14"></polyline>
                </svg>
                <?php echo esc_html($duration); ?>
            </span>
        <?php endif; ?>

        <div class="bento-card-content">
            <div class="bento-card-tags">
                <?php if ($dest_terms && !is_wp_error($dest_terms)) : ?>
                    <?php foreach (array_slice($dest_terms, 0, 2) as $term) : ?>
                        <span class="bento-card-tag bento-card-tag--dest"><?php echo esc_html($term->name); ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <h2 class="bento-card-title"><?php the_title(); ?></h2>

            <p class="bento-card-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>

            <div class="bento-card-meta">
                <?php if ($difficulty) : ?>
                    <span><?php echo esc_html(ft_get_difficulty_label($difficulty)); ?></span>
                <?php endif; ?>
                <?php if ($price) : ?>
                    <span><?php echo esc_html(ft_get_price_label($price)); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </a>
</article>
