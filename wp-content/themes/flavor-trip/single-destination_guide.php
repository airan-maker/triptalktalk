<?php
/**
 * 도시 가이드 상세 페이지
 *
 * @package Flavor_Trip
 */

get_header();

while (have_posts()) : the_post();
    $city    = get_post_meta(get_the_ID(), '_ft_guide_city', true);
    $country = get_post_meta(get_the_ID(), '_ft_guide_country', true);
    $intro   = get_post_meta(get_the_ID(), '_ft_guide_intro', true);
    $data    = get_post_meta(get_the_ID(), '_ft_guide_data', true);

    $places      = !empty($data['places']) ? $data['places'] : [];
    $restaurants = !empty($data['restaurants']) ? $data['restaurants'] : [];
    $hotels      = !empty($data['hotels']) ? $data['hotels'] : [];
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('guide-single'); ?>>
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
    </div>

    <div class="container">
        <header class="guide-header">
            <?php if ($country) : ?>
                <span class="guide-country-tag"><?php echo esc_html($country); ?></span>
            <?php endif; ?>

            <h1 class="guide-title"><?php the_title(); ?></h1>

            <?php if ($intro) : ?>
                <p class="guide-intro"><?php echo esc_html($intro); ?></p>
            <?php endif; ?>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <div class="itinerary-featured-image" style="margin-bottom:2rem;">
                <?php the_post_thumbnail('ft-hero', ['loading' => 'eager']); ?>
            </div>
        <?php else :
            $fallback_url = ft_get_destination_image(get_the_ID());
            if ($fallback_url) : ?>
                <div class="itinerary-featured-image" style="margin-bottom:2rem;">
                    <img src="<?php echo esc_url($fallback_url); ?>" alt="<?php the_title_attribute(); ?>" loading="eager">
                </div>
        <?php endif; endif; ?>

        <?php if (!empty($data)) : ?>
            <div class="guide-tabs" role="tablist">
                <button class="guide-tab active" data-tab="places" role="tab" aria-selected="true">
                    <?php esc_html_e('관광지', 'flavor-trip'); ?>
                    <span class="tab-count">(<?php echo count($places); ?>)</span>
                </button>
                <button class="guide-tab" data-tab="restaurants" role="tab" aria-selected="false">
                    <?php esc_html_e('식당', 'flavor-trip'); ?>
                    <span class="tab-count">(<?php echo count($restaurants); ?>)</span>
                </button>
                <button class="guide-tab" data-tab="hotels" role="tab" aria-selected="false">
                    <?php esc_html_e('호텔', 'flavor-trip'); ?>
                    <span class="tab-count">(<?php echo count($hotels); ?>)</span>
                </button>
            </div>

            <?php
            // ── 관광지 탭 ──
            ?>
            <div id="panel-places" class="guide-table-panel active" role="tabpanel">
                <?php
                set_query_var('ft_guide_tab', 'places');
                set_query_var('ft_guide_items', $places);
                set_query_var('ft_guide_columns', [
                    'category' => __('카테고리', 'flavor-trip'),
                ]);
                get_template_part('template-parts/guide-table');
                ?>
            </div>

            <?php
            // ── 식당 탭 ──
            ?>
            <div id="panel-restaurants" class="guide-table-panel" role="tabpanel">
                <?php
                set_query_var('ft_guide_tab', 'restaurants');
                set_query_var('ft_guide_items', $restaurants);
                set_query_var('ft_guide_columns', [
                    'cuisine' => __('음식', 'flavor-trip'),
                    'price'   => __('가격', 'flavor-trip'),
                ]);
                get_template_part('template-parts/guide-table');
                ?>
            </div>

            <?php
            // ── 호텔 탭 ──
            ?>
            <div id="panel-hotels" class="guide-table-panel" role="tabpanel">
                <?php
                set_query_var('ft_guide_tab', 'hotels');
                set_query_var('ft_guide_items', $hotels);
                set_query_var('ft_guide_columns', [
                    'grade' => __('등급', 'flavor-trip'),
                    'price' => __('가격', 'flavor-trip'),
                ]);
                get_template_part('template-parts/guide-table');
                ?>
            </div>
        <?php endif; ?>

        <nav class="post-navigation">
            <?php
            previous_post_link('<div class="nav-prev">%link</div>', '← %title');
            next_post_link('<div class="nav-next">%link</div>', '%title →');
            ?>
        </nav>
    </div>
</article>

<?php
endwhile;
get_footer();
