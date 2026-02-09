<?php
/**
 * 프론트 페이지 — 도시 가이드 섹션
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'ko';
$guides = new WP_Query([
    'post_type'      => 'destination_guide',
    'posts_per_page' => 6,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'lang'           => $current_lang,
]);

if (!$guides->have_posts()) return;
?>

<section class="section section-guides">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('도시 가이드', 'flavor-trip'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('여행 스타일별 관광지/맛집/호텔 비교', 'flavor-trip'); ?></p>

        <div class="posts-grid posts-grid--3">
            <?php while ($guides->have_posts()) : $guides->the_post();
                get_template_part('template-parts/guide-card');
            endwhile;
            wp_reset_postdata(); ?>
        </div>

        <div class="section-cta">
            <a href="<?php echo esc_url(get_post_type_archive_link('destination_guide')); ?>" class="btn btn-outline">
                <?php esc_html_e('모든 가이드 보기 →', 'flavor-trip'); ?>
            </a>
        </div>
    </div>
</section>
