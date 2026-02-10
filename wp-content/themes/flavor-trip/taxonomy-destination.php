<?php
/**
 * 여행지별 아카이브 템플릿 — Bento Grid
 *
 * @package Flavor_Trip
 */

get_header();

$term = get_queried_object();
$all_destinations = ft_get_terms_current_lang(['taxonomy' => 'destination', 'hide_empty' => true, 'parent' => 0]);
$styles = ft_get_terms_current_lang(['taxonomy' => 'travel_style', 'hide_empty' => true]);
$children = ft_get_terms_current_lang(['taxonomy' => 'destination', 'parent' => $term->term_id, 'hide_empty' => true]);
?>

<div class="archive-hero">
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="archive-hero-content">
            <h1><?php echo esc_html($term->name); ?> <?php esc_html_e('여행', 'flavor-trip'); ?></h1>
            <?php if ($term->description) : ?>
                <p><?php echo esc_html($term->description); ?></p>
            <?php else : ?>
                <p><?php printf(esc_html__('%s의 다양한 여행 코스를 탐색하고 완벽한 일정을 찾아보세요.', 'flavor-trip'), $term->name); ?></p>
            <?php endif; ?>
            <div class="hero-search">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" class="search-field" placeholder="<?php esc_attr_e('여행지, 맛집, 액티비티 검색...', 'flavor-trip'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                    <input type="hidden" name="post_type" value="travel_itinerary" />
                    <button type="submit" class="search-submit"><?php esc_html_e('검색', 'flavor-trip'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="bento-filters">
        <div class="filter-section">
            <span class="filter-section-label"><?php esc_html_e('여행지', 'flavor-trip'); ?></span>
            <a href="<?php echo esc_url(get_post_type_archive_link('travel_itinerary')); ?>" class="filter-pill">전체</a>
            <?php foreach ($all_destinations as $dest) : ?>
                <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="filter-pill <?php echo $term->slug === $dest->slug ? 'active' : ''; ?>">
                    <?php echo esc_html($dest->name); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!is_wp_error($children) && $children) : ?>
            <div class="filter-section">
                <span class="filter-section-label"><?php esc_html_e('세부 지역', 'flavor-trip'); ?></span>
                <?php foreach ($children as $child) : ?>
                    <a href="<?php echo esc_url(get_term_link($child)); ?>" class="filter-pill"><?php echo esc_html($child->name); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!is_wp_error($styles) && $styles) : ?>
            <div class="filter-section">
                <span class="filter-section-label"><?php esc_html_e('스타일', 'flavor-trip'); ?></span>
                <?php foreach ($styles as $style) : ?>
                    <a href="<?php echo esc_url(get_term_link($style)); ?>" class="filter-pill"><?php echo esc_html($style->name); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (have_posts()) : ?>
        <div class="bento-grid">
            <?php
            $counter = 0;
            while (have_posts()) : the_post();
                $counter++;
                set_query_var('bento_counter', $counter);
                get_template_part('template-parts/bento-card');
            endwhile;
            ?>
        </div>
        <?php ft_pagination(); ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>
</div>

<?php
get_footer();
