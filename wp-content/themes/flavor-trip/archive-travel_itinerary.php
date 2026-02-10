<?php
/**
 * 여행 일정 아카이브 템플릿 — Bento Grid + Magazine Style
 *
 * @package Flavor_Trip
 */

get_header();

$destinations = ft_get_terms_current_lang(['taxonomy' => 'destination', 'hide_empty' => true, 'parent' => 0]);
$styles = ft_get_terms_current_lang(['taxonomy' => 'travel_style', 'hide_empty' => true]);
$current_dest = get_query_var('destination');
$current_style = get_query_var('travel_style');
?>

<div class="archive-hero">
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="archive-hero-content">
            <h1><?php esc_html_e('어디로 떠나볼까요?', 'flavor-trip'); ?></h1>
            <p><?php esc_html_e('전 세계 다양한 여행 코스를 탐색하고 나만의 완벽한 여행을 계획해보세요.', 'flavor-trip'); ?></p>
            <div class="hero-search">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" class="search-field" placeholder="<?php esc_attr_e('도시, 국가 또는 여행 스타일 검색...', 'flavor-trip'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
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
                <a href="<?php echo esc_url(get_post_type_archive_link('travel_itinerary')); ?>" class="filter-pill <?php echo !$current_dest ? 'active' : ''; ?>">
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
