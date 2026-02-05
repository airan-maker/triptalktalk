<?php
/**
 * ?¬í–‰ì§€ë³??„ì¹´?´ë¸Œ ?œí”Œë¦? *
 * @package TripTalk
 */

get_header();

$term = get_queried_object();
?>

<div class="container archive-layout">
    <div class="content-area">
        <?php get_template_part('template-parts/breadcrumbs'); ?>

        <header class="page-header">
            <h1 class="page-title">
                <span class="term-label"><?php esc_html_e('?¬í–‰ì§€', 'flavor-trip'); ?></span>
                <?php echo esc_html($term->name); ?>
            </h1>
            <?php if ($term->description) : ?>
                <div class="archive-description"><?php echo wp_kses_post(wpautop($term->description)); ?></div>
            <?php endif; ?>
            <span class="post-count"><?php printf(esc_html__('%dê°œì˜ ?¼ì •', 'flavor-trip'), $term->count); ?></span>
        </header>

        <?php
        $children = get_terms(['taxonomy' => 'destination', 'parent' => $term->term_id, 'hide_empty' => true]);
        if (!is_wp_error($children) && $children) : ?>
            <div class="sub-destinations">
                <?php foreach ($children as $child) : ?>
                    <a href="<?php echo esc_url(get_term_link($child)); ?>" class="filter-tag"><?php echo esc_html($child->name); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (have_posts()) : ?>
            <div class="posts-grid posts-grid--3">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content', 'itinerary'); ?>
                <?php endwhile; ?>
            </div>
            <?php ft_pagination(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
