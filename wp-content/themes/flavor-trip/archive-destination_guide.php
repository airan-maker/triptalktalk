<?php
/**
 * 도시 가이드 아카이브 템플릿
 *
 * @package Flavor_Trip
 */

get_header();
?>

<div class="guide-archive-hero">
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <h1><?php esc_html_e('도시 가이드', 'flavor-trip'); ?></h1>
        <p><?php esc_html_e('여행 스타일별 관광지/맛집/호텔 비교', 'flavor-trip'); ?></p>
    </div>
</div>

<div class="container">
    <?php if (have_posts()) : ?>
        <div class="posts-grid posts-grid--3">
            <?php while (have_posts()) : the_post();
                get_template_part('template-parts/guide-card');
            endwhile; ?>
        </div>
        <?php ft_pagination(); ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>
</div>

<?php
get_footer();
