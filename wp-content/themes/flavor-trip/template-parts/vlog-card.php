<?php
/**
 * 브이로그 큐레이션 — 카드
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$youtube_id   = get_post_meta(get_the_ID(), '_ft_vlog_youtube_id', true);
$channel_name = get_post_meta(get_the_ID(), '_ft_vlog_channel_name', true);
$duration     = get_post_meta(get_the_ID(), '_ft_vlog_duration', true);
$spots        = get_post_meta(get_the_ID(), '_ft_vlog_spots', true) ?: [];
$spot_count   = count($spots);

// 이미지: 포스트 썸네일 → 유튜브 썸네일 → 폴백
$image_url = '';
if (has_post_thumbnail()) {
    $image_url = get_the_post_thumbnail_url(get_the_ID(), 'ft-card');
} elseif ($youtube_id) {
    $image_url = 'https://img.youtube.com/vi/' . esc_attr($youtube_id) . '/mqdefault.jpg';
}
if (!$image_url && function_exists('ft_get_destination_image')) {
    $image_url = ft_get_destination_image(get_the_ID());
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('card card-vlog'); ?>>
    <a href="<?php the_permalink(); ?>" class="card-image">
        <?php if ($image_url) : ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
        <?php endif; ?>
        <span class="vlog-card-play">&#9654;</span>
        <?php if ($duration) : ?>
            <span class="vlog-duration-badge"><?php echo esc_html($duration); ?></span>
        <?php endif; ?>
    </a>

    <div class="card-body">
        <?php
        $destinations = get_the_terms(get_the_ID(), 'destination');
        if ($destinations && !is_wp_error($destinations)) : ?>
            <div class="card-tags">
                <?php foreach (array_slice($destinations, 0, 2) as $dest) : ?>
                    <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="tag tag-sm"><?php echo esc_html($dest->name); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h3 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <p class="card-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>

        <div class="card-footer">
            <?php if ($channel_name) : ?>
                <span class="card-channel"><?php echo esc_html($channel_name); ?></span>
            <?php endif; ?>
            <?php if ($spot_count) : ?>
                <span class="card-meta-item"><?php printf(esc_html__('%d곳', 'flavor-trip'), $spot_count); ?></span>
            <?php endif; ?>
        </div>
    </div>
</article>
