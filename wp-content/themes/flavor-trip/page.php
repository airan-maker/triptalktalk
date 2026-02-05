<?php
/**
 * ?¼ë°˜ ?˜ì´ì§€ ?œí”Œë¦? *
 * @package TripTalk
 */

get_header();
?>

<div class="container page-layout">
    <?php while (have_posts()) : the_post(); ?>
        <?php get_template_part('template-parts/breadcrumbs'); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php
get_footer();
