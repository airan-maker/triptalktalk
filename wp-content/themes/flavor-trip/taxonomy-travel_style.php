<?php
/**
 * 여행 스타일별 아카이브 템플릿 — Bento Grid
 *
 * @package Flavor_Trip
 */

get_header();

$term = get_queried_object();
$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'ko';
$destinations = get_terms(['taxonomy' => 'destination', 'hide_empty' => true, 'parent' => 0, 'lang' => $current_lang]);
$all_styles = get_terms(['taxonomy' => 'travel_style', 'hide_empty' => true, 'lang' => $current_lang]);
?>

<div class="archive-hero">
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="archive-hero-content">
            <h1><?php echo esc_html($term->name); ?></h1>
            <?php if ($term->description) : ?>
                <p><?php echo esc_html($term->description); ?></p>
            <?php else : ?>
                <p><?php printf(esc_html__('%s 스타일의 여행 코스를 탐색하고 나만의 여행을 찾아보세요.', 'flavor-trip'), $term->name); ?></p>
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
        <?php if (!is_wp_error($destinations) && $destinations) : ?>
            <div class="filter-section">
                <span class="filter-section-label"><?php esc_html_e('여행지', 'flavor-trip'); ?></span>
                <a href="<?php echo esc_url(get_post_type_archive_link('travel_itinerary')); ?>" class="filter-pill">전체</a>
                <?php foreach ($destinations as $dest) : ?>
                    <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="filter-pill"><?php echo esc_html($dest->name); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="filter-section">
            <span class="filter-section-label"><?php esc_html_e('스타일', 'flavor-trip'); ?></span>
            <?php foreach ($all_styles as $style) : ?>
                <a href="<?php echo esc_url(get_term_link($style)); ?>" class="filter-pill <?php echo $term->slug === $style->slug ? 'active' : ''; ?>">
                    <?php echo esc_html($style->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
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
