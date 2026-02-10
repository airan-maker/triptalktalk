<?php
/**
 * 브이로그 큐레이션 아카이브 템플릿
 *
 * @package Flavor_Trip
 */

get_header();
?>

<div class="vlog-archive-hero">
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <h1><?php esc_html_e('브이로그', 'flavor-trip'); ?></h1>
        <p><?php esc_html_e('여행 크리에이터의 생생한 브이로그를 큐레이션합니다', 'flavor-trip'); ?></p>
    </div>
</div>

<div class="container">
    <?php
    $destinations = ft_get_terms_current_lang(['taxonomy' => 'destination', 'hide_empty' => true, 'parent' => 0]);
    $styles = ft_get_terms_current_lang(['taxonomy' => 'travel_style', 'hide_empty' => true]);
    $current_dest = get_query_var('destination');
    $current_style = get_query_var('travel_style');
    ?>

    <div class="bento-filters">
        <?php if (!is_wp_error($destinations) && $destinations) : ?>
            <div class="filter-section">
                <span class="filter-section-label"><?php esc_html_e('여행지', 'flavor-trip'); ?></span>
                <a href="<?php echo esc_url(get_post_type_archive_link('vlog_curation')); ?>" class="filter-pill <?php echo !$current_dest && !$current_style ? 'active' : ''; ?>">
                    <?php esc_html_e('전체', 'flavor-trip'); ?>
                </a>
                <?php foreach ($destinations as $dest) : ?>
                    <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="filter-pill <?php echo $current_dest === $dest->slug ? 'active' : ''; ?>">
                        <?php echo esc_html($dest->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!is_wp_error($styles) && $styles) : ?>
            <div class="filter-section">
                <span class="filter-section-label"><?php esc_html_e('스타일', 'flavor-trip'); ?></span>
                <?php foreach ($styles as $style) : ?>
                    <a href="<?php echo esc_url(get_term_link($style)); ?>" class="filter-pill <?php echo $current_style === $style->slug ? 'active' : ''; ?>">
                        <?php echo esc_html($style->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (have_posts()) : ?>
        <div class="posts-grid posts-grid--3">
            <?php while (have_posts()) : the_post();
                get_template_part('template-parts/vlog-card');
            endwhile; ?>
        </div>
        <?php ft_pagination(); ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>
</div>

<?php
get_footer();
