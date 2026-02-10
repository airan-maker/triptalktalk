<?php
/**
 * 프론트 페이지 — 추천 브이로그 섹션
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'ko';

$query_args = [
    'post_type'      => 'vlog_curation',
    'posts_per_page' => 3,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'lang'           => $current_lang,
];

if (taxonomy_exists('language')) {
    $query_args['tax_query'] = [[
        'taxonomy' => 'language',
        'field'    => 'slug',
        'terms'    => $current_lang,
    ]];
}

$vlogs = new WP_Query($query_args);

if (!$vlogs->have_posts()) return;
?>

<section class="section section-vlogs">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('추천 브이로그', 'flavor-trip'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('여행 크리에이터의 생생한 브이로그를 큐레이션합니다', 'flavor-trip'); ?></p>

        <div class="posts-grid posts-grid--3">
            <?php while ($vlogs->have_posts()) : $vlogs->the_post();
                get_template_part('template-parts/vlog-card');
            endwhile;
            wp_reset_postdata(); ?>
        </div>

        <div class="section-cta">
            <a href="<?php echo esc_url(get_post_type_archive_link('vlog_curation')); ?>" class="btn btn-outline">
                <?php esc_html_e('모든 브이로그 보기 →', 'flavor-trip'); ?>
            </a>
        </div>
    </div>
</section>
