<?php
/**
 * 기본 ?�백 ?�플�? *
 * @package TripTalk
 */

get_header();
?>

<div class="container archive-layout">
    <div class="content-area">
        <?php if (have_posts()) : ?>
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('최신 글', 'flavor-trip'); ?></h1>
            </header>

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
