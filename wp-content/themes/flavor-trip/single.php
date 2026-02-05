<?php
/**
 * 블로그 포스트 상세 페이지
 *
 * @package Flavor_Trip
 */

get_header();
?>

<div class="container single-layout">
    <div class="content-area">
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-parts/breadcrumbs'); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <div class="entry-categories">
                        <?php the_category(', '); ?>
                    </div>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <div class="entry-meta">
                        <span class="entry-author"><?php the_author(); ?></span>
                        <time class="entry-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php the_date(); ?></time>
                        <span class="entry-reading-time"><?php echo esc_html(ft_reading_time()); ?></span>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-thumbnail">
                        <?php the_post_thumbnail('ft-hero', ['loading' => 'eager']); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <footer class="entry-footer">
                    <?php the_tags('<div class="entry-tags">', '', '</div>'); ?>
                </footer>

                <nav class="post-navigation">
                    <?php
                    previous_post_link('<div class="nav-prev">%link</div>', '← %title');
                    next_post_link('<div class="nav-next">%link</div>', '%title →');
                    ?>
                </nav>

                <?php if (comments_open() || get_comments_number()) :
                    comments_template();
                endif; ?>
            </article>
        <?php endwhile; ?>
    </div>

    <?php get_sidebar(); ?>
</div>

<?php
get_footer();
