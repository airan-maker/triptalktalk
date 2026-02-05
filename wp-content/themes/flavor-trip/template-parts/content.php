<?php
/**
 * 블로그 포스트 카드
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>" class="card-image">
            <?php the_post_thumbnail('ft-card', ['loading' => 'lazy']); ?>
        </a>
    <?php endif; ?>

    <div class="card-body">
        <div class="card-meta">
            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
            <?php if (has_category()) : ?>
                <span class="card-category"><?php the_category(', '); ?></span>
            <?php endif; ?>
        </div>

        <h3 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <p class="card-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>

        <a href="<?php the_permalink(); ?>" class="card-link"><?php esc_html_e('더 보기 →', 'flavor-trip'); ?></a>
    </div>
</article>
