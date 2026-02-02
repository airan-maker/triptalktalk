<?php
/**
 * 검색 결과 템플릿
 *
 * @package Flavor_Trip
 */

get_header();
?>

<div class="container archive-layout">
    <div class="content-area">
        <?php get_template_part('template-parts/breadcrumbs'); ?>

        <header class="page-header">
            <h1 class="page-title">
                <?php printf(esc_html__('"%s" 검색 결과', 'flavor-trip'), get_search_query()); ?>
            </h1>
            <span class="post-count"><?php printf(esc_html__('%d개의 결과', 'flavor-trip'), $wp_query->found_posts); ?></span>
        </header>

        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    if (get_post_type() === 'travel_itinerary') {
                        get_template_part('template-parts/content', 'itinerary');
                    } else {
                        get_template_part('template-parts/content');
                    }
                    ?>
                <?php endwhile; ?>
            </div>
            <?php ft_pagination(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>

    <?php get_sidebar(); ?>
</div>

<?php
get_footer();
