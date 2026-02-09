<?php
/**
 * 여행 일정 카드
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$duration   = get_post_meta(get_the_ID(), '_ft_duration', true);
$price      = get_post_meta(get_the_ID(), '_ft_price_range', true);
$difficulty = get_post_meta(get_the_ID(), '_ft_difficulty', true);
$dest_name  = get_post_meta(get_the_ID(), '_ft_destination_name', true);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('card card-itinerary'); ?>>
    <?php
    $has_thumb = has_post_thumbnail();
    $fallback_img = '';
    if (!$has_thumb && function_exists('ft_get_destination_image')) {
        $fallback_img = ft_get_destination_image(get_the_ID());
    }
    if ($has_thumb || $fallback_img) : ?>
        <a href="<?php the_permalink(); ?>" class="card-image">
            <?php if ($has_thumb) : ?>
                <?php the_post_thumbnail('ft-card', ['loading' => 'lazy']); ?>
            <?php else : ?>
                <img src="<?php echo esc_url($fallback_img); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
            <?php endif; ?>
            <?php if ($duration) : ?>
                <span class="card-badge"><?php echo esc_html($duration); ?></span>
            <?php endif; ?>
        </a>
    <?php endif; ?>

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
            <?php if ($difficulty) : ?>
                <span class="card-meta-item"><?php echo esc_html(ft_get_difficulty_label($difficulty)); ?></span>
            <?php endif; ?>
            <?php if ($price) : ?>
                <span class="card-meta-item"><?php echo esc_html(ft_get_price_label($price)); ?></span>
            <?php endif; ?>
        </div>
    </div>
</article>
