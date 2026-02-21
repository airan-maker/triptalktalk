<?php
/**
 * 브이로그 큐레이션 아카이브 템플릿
 *
 * @package Flavor_Trip
 */

get_header();

$vlog_archive_url = get_post_type_archive_link('vlog_curation');
$destinations = ft_get_terms_current_lang(['taxonomy' => 'destination', 'hide_empty' => true, 'parent' => 0]);
$styles = ft_get_terms_current_lang(['taxonomy' => 'travel_style', 'hide_empty' => true]);
$current_dest = isset($_GET['vlog_dest']) ? sanitize_text_field($_GET['vlog_dest']) : '';
$current_style = isset($_GET['vlog_style']) ? sanitize_text_field($_GET['vlog_style']) : '';

// Filter query by taxonomy if parameter is set
if ($current_dest || $current_style) {
    $tax_query = ['relation' => 'AND'];
    if ($current_dest) {
        $tax_query[] = [
            'taxonomy' => 'destination',
            'field'    => 'slug',
            'terms'    => $current_dest,
        ];
    }
    if ($current_style) {
        $tax_query[] = [
            'taxonomy' => 'travel_style',
            'field'    => 'slug',
            'terms'    => $current_style,
        ];
    }
    global $wp_query;
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $wp_query = new WP_Query([
        'post_type'      => 'vlog_curation',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
        'paged'          => $paged,
        'tax_query'      => $tax_query,
    ]);
}
?>

<div class="archive-hero">
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="archive-hero-content">
            <h1><?php esc_html_e('브이로그', 'flavor-trip'); ?></h1>
            <p><?php esc_html_e('여행 크리에이터의 생생한 브이로그를 큐레이션합니다', 'flavor-trip'); ?></p>
        </div>
    </div>
</div>

<div class="container">
    <div class="bento-filters">
        <?php if (!is_wp_error($destinations) && $destinations) : ?>
            <div class="filter-section">
                <span class="filter-section-label"><?php esc_html_e('여행지', 'flavor-trip'); ?></span>
                <a href="<?php echo esc_url($vlog_archive_url); ?>" class="filter-pill <?php echo !$current_dest ? 'active' : ''; ?>">
                    <?php esc_html_e('전체', 'flavor-trip'); ?>
                </a>
                <?php foreach ($destinations as $dest) : ?>
                    <?php
                    $dest_url = add_query_arg('vlog_dest', $dest->slug, $vlog_archive_url);
                    if ($current_style) {
                        $dest_url = add_query_arg('vlog_style', $current_style, $dest_url);
                    }
                    ?>
                    <a href="<?php echo esc_url($dest_url); ?>" class="filter-pill <?php echo $current_dest === $dest->slug ? 'active' : ''; ?>">
                        <?php echo esc_html($dest->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!is_wp_error($styles) && $styles) : ?>
            <div class="filter-section">
                <span class="filter-section-label"><?php esc_html_e('스타일', 'flavor-trip'); ?></span>
                <?php foreach ($styles as $style) : ?>
                    <?php
                    $style_url = add_query_arg('vlog_style', $style->slug, $vlog_archive_url);
                    if ($current_dest) {
                        $style_url = add_query_arg('vlog_dest', $current_dest, $style_url);
                    }
                    ?>
                    <a href="<?php echo esc_url($style_url); ?>" class="filter-pill <?php echo $current_style === $style->slug ? 'active' : ''; ?>">
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
if ($current_dest || $current_style) {
    wp_reset_query();
}
get_footer();
