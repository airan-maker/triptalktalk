<?php
/**
 * ?¬í–‰ ?¼ì • ?„ì¹´?´ë¸Œ ?œí”Œë¦? *
 * @package TripTalk
 */

get_header();
?>

<div class="container archive-layout">
    <div class="content-area">
        <?php get_template_part('template-parts/breadcrumbs'); ?>

        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e('?¬í–‰ ?¼ì •', 'flavor-trip'); ?></h1>
            <p class="page-description"><?php esc_html_e('?¤ì–‘???¬í–‰ ì½”ìŠ¤ë¥??ìƒ‰?˜ê³  ?˜ë§Œ???¬í–‰??ê³„íš?´ë³´?¸ìš”.', 'flavor-trip'); ?></p>
        </header>

        <div class="archive-filters">
            <?php
            $destinations = get_terms(['taxonomy' => 'destination', 'hide_empty' => true, 'parent' => 0]);
            $styles = get_terms(['taxonomy' => 'travel_style', 'hide_empty' => true]);
            $current_dest = get_query_var('destination');
            $current_style = get_query_var('travel_style');
            ?>
            <?php if (!is_wp_error($destinations) && $destinations) : ?>
                <div class="filter-group">
                    <span class="filter-label"><?php esc_html_e('?¬í–‰ì§€:', 'flavor-trip'); ?></span>
                    <a href="<?php echo esc_url(get_post_type_archive_link('travel_itinerary')); ?>" class="filter-tag <?php echo !$current_dest ? 'active' : ''; ?>"><?php esc_html_e('?„ì²´', 'flavor-trip'); ?></a>
                    <?php foreach ($destinations as $dest) : ?>
                        <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="filter-tag <?php echo $current_dest === $dest->slug ? 'active' : ''; ?>">
                            <?php echo esc_html($dest->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!is_wp_error($styles) && $styles) : ?>
                <div class="filter-group">
                    <span class="filter-label"><?php esc_html_e('?¤í???', 'flavor-trip'); ?></span>
                    <?php foreach ($styles as $style) : ?>
                        <a href="<?php echo esc_url(get_term_link($style)); ?>" class="filter-tag <?php echo $current_style === $style->slug ? 'active' : ''; ?>">
                            <?php echo esc_html($style->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

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
