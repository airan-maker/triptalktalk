<?php
/**
 * 여행지 카테고리 그리드
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

// 지역별 대표 이미지 (중앙 관리)
$destination_images = ft_get_destination_images('card');

$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'ko';
$destinations = get_terms([
    'taxonomy'   => 'destination',
    'hide_empty' => true,
    'parent'     => 0,
    'number'     => 6,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'lang'       => $current_lang,
]);

if (is_wp_error($destinations) || empty($destinations)) {
    return;
}
?>

<section class="section section-destinations">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('인기 여행지', 'flavor-trip'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('어디로 떠나볼까요?', 'flavor-trip'); ?></p>

        <div class="destination-grid">
            <?php foreach ($destinations as $dest) :
                // 먼저 수동 설정된 이미지 확인
                $image_id = get_term_meta($dest->term_id, 'ft_destination_image', true);
                $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'ft-card') : '';

                // 없으면 Unsplash 기본 이미지 사용 (번역 슬러그 → 원본 슬러그 자동 변환)
                if (!$image_url) {
                    $resolved_slug = ft_resolve_destination_slug($dest->slug, $destination_images);
                    $image_url = isset($destination_images[$resolved_slug])
                        ? $destination_images[$resolved_slug]
                        : $destination_images['default'];
                }
            ?>
                <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="destination-card" style="background-image: url('<?php echo esc_url($image_url); ?>')">
                    <div class="destination-overlay"></div>
                    <div class="destination-info">
                        <h3 class="destination-name"><?php echo esc_html($dest->name); ?></h3>
                        <span class="destination-count"><?php printf(esc_html__('%d개의 일정', 'flavor-trip'), $dest->count); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
