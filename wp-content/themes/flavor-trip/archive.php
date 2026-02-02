<?php
/**
 * 일반 아카이브 템플릿
 *
 * @package Flavor_Trip
 */

get_header();
?>

<div class="container archive-layout">
    <div class="content-area">
        <?php get_template_part('template-parts/breadcrumbs'); ?>

        <header class="page-header">
            <?php the_archive_title('<h1 class="page-title">', '</h1>'); ?>
            <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content'); ?>
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
