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

        <?php
        // 구글맵: 좌표가 있는 아이템이 하나라도 있으면 지도 표시
        $all_items = [];
        foreach ($places as $item) {
            if (!empty($item['lat']) && !empty($item['lng'])) {
                $item['_type'] = 'places';
                $all_items[] = $item;
            }
        }
        foreach ($restaurants as $item) {
            if (!empty($item['lat']) && !empty($item['lng'])) {
                $item['_type'] = 'restaurants';
                $all_items[] = $item;
            }
        }
        foreach ($hotels as $item) {
            if (!empty($item['lat']) && !empty($item['lng'])) {
                $item['_type'] = 'hotels';
                $all_items[] = $item;
            }
        }

        if (!empty($all_items)) : ?>
            <div id="ft-guide-map" class="guide-map-container"></div>
            <div class="guide-map-legend">
                <span class="legend-item legend-places"><span class="legend-dot"></span> <?php esc_html_e('관광지', 'flavor-trip'); ?></span>
                <span class="legend-item legend-restaurants"><span class="legend-dot"></span> <?php esc_html_e('식당', 'flavor-trip'); ?></span>
                <span class="legend-item legend-hotels"><span class="legend-dot"></span> <?php esc_html_e('호텔', 'flavor-trip'); ?></span>
            </div>
        <?php elseif (has_post_thumbnail()) : ?>
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
                <button class="guide-tab" data-tab="activities" role="tab" aria-selected="false">
                    <?php esc_html_e('액티비티', 'flavor-trip'); ?>
                    <span class="tab-count">🎫</span>
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

            <?php
            // ── 액티비티 탭 ──
            $klook_aid = get_theme_mod('ft_klook_aid', '6yjZP2Ac');
            $klook_base = 'https://www.klook.com/ko/search/result/?query=';
            $aid_param = $klook_aid ? '&aid=' . urlencode($klook_aid) : '';

            $activity_categories = [
                ['icon' => '🎫', 'label' => __('관광지 입장권', 'flavor-trip'),     'query' => $city . ' 입장권'],
                ['icon' => '🚌', 'label' => __('투어 & 데이트립', 'flavor-trip'),    'query' => $city . ' 투어'],
                ['icon' => '🍜', 'label' => __('맛집 & 푸드투어', 'flavor-trip'),    'query' => $city . ' 푸드투어'],
                ['icon' => '🎨', 'label' => __('체험 & 클래스', 'flavor-trip'),      'query' => $city . ' 체험'],
                ['icon' => '🚇', 'label' => __('교통 패스', 'flavor-trip'),          'query' => $city . ' 교통 패스'],
                ['icon' => '📶', 'label' => __('SIM카드 & 와이파이', 'flavor-trip'), 'query' => $city . ' 심카드 와이파이'],
                ['icon' => '🎢', 'label' => __('테마파크', 'flavor-trip'),           'query' => $city . ' 테마파크'],
                ['icon' => '💆', 'label' => __('스파 & 웰니스', 'flavor-trip'),      'query' => $city . ' 스파 마사지'],
            ];

            // 주요 관광지 (상위 6개)만 추천
            $top_places = array_slice($places, 0, 6);
            ?>
            <div id="panel-activities" class="guide-table-panel" role="tabpanel">
                <p class="guide-activity-intro">
                    <?php printf(
                        esc_html__('%s에서 즐길 수 있는 액티비티, 투어, 입장권을 Klook에서 찾아보세요.', 'flavor-trip'),
                        '<strong>' . esc_html($city) . '</strong>'
                    ); ?>
                </p>

                <?php $city_url = esc_url($klook_base . urlencode($city) . $aid_param); ?>
                <a href="<?php echo $city_url; ?>" target="_blank" rel="noopener noreferrer nofollow sponsored" class="guide-activity-city-link">
                    🔍 <?php printf(esc_html__('%s 전체 액티비티 검색', 'flavor-trip'), esc_html($city)); ?>
                </a>

                <h3 class="guide-activity-section-title"><?php esc_html_e('카테고리별 검색', 'flavor-trip'); ?></h3>
                <div class="guide-activity-grid">
                    <?php foreach ($activity_categories as $cat) :
                        $cat_url = esc_url($klook_base . urlencode($cat['query']) . $aid_param);
                    ?>
                        <a href="<?php echo $cat_url; ?>" target="_blank" rel="noopener noreferrer nofollow sponsored" class="guide-activity-card guide-activity-card--category">
                            <span class="activity-card__icon"><?php echo $cat['icon']; ?></span>
                            <span class="activity-card__name"><?php echo esc_html($cat['label']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <h3 class="guide-activity-section-title"><?php esc_html_e('인기 명소 액티비티', 'flavor-trip'); ?></h3>
                <div class="guide-activity-grid guide-activity-grid--places">
                    <?php foreach ($top_places as $item) :
                        $place_url = esc_url($klook_base . urlencode($item['name']) . $aid_param);
                    ?>
                        <a href="<?php echo $place_url; ?>" target="_blank" rel="noopener noreferrer nofollow sponsored" class="guide-activity-card guide-activity-card--place">
                            <span class="activity-card__name"><?php echo esc_html($item['name']); ?></span>
                            <span class="activity-card__meta"><?php echo esc_html(($item['area'] ?? '') . (!empty($item['category']) ? ' · ' . $item['category'] : '')); ?></span>
                            <span class="activity-card__cta"><?php esc_html_e('Klook에서 검색 →', 'flavor-trip'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
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
