<?php
/**
 * 여행지 카테고리 그리드
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

// 지역별 대표 이미지 (Unsplash)
$destination_images = [
    // 지역 (부모)
    'korea'          => 'https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=800&q=80',
    'japan'          => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&q=80',
    'east-asia'      => 'https://images.unsplash.com/photo-1536599018102-9f803c140fc1?w=800&q=80',
    'southeast-asia' => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=800&q=80',
    'europe'         => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=800&q=80',
    'north-america'  => 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?w=800&q=80',
    'oceania'        => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&q=80',
    // 도시
    'jeju'           => 'https://images.unsplash.com/photo-1579169326371-16b10fc39a99?w=800&q=80',
    'seoul'          => 'https://images.unsplash.com/photo-1538485399081-7191377e8241?w=800&q=80',
    'busan'          => 'https://images.unsplash.com/photo-1552751753-d82a5e9a0c98?w=800&q=80',
    'tokyo'          => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800&q=80',
    'osaka'          => 'https://images.unsplash.com/photo-1590559899731-a382839e5549?w=800&q=80',
    'kyoto'          => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&q=80',
    'fukuoka'        => 'https://images.unsplash.com/photo-1576675784201-0e142b423952?w=800&q=80',
    'hongkong'       => 'https://images.unsplash.com/photo-1536599018102-9f803c140fc1?w=800&q=80',
    'taipei'         => 'https://images.unsplash.com/photo-1470004914212-05527e49370b?w=800&q=80',
    'bangkok'        => 'https://images.unsplash.com/photo-1563492065599-3520f775eeed?w=800&q=80',
    'singapore'      => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&q=80',
    'bali'           => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=80',
    'danang'         => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?w=800&q=80',
    'paris'          => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=80',
    'london'         => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&q=80',
    'rome'           => 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800&q=80',
    'barcelona'      => 'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=800&q=80',
    'new-york'       => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=80',
    'los-angeles'    => 'https://images.unsplash.com/photo-1534190760961-74e8c1c5c3da?w=800&q=80',
    'hawaii'         => 'https://images.unsplash.com/photo-1507876466758-bc54f384809c?w=800&q=80',
    'sydney'         => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&q=80',
    'default'        => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&q=80',
];

$destinations = get_terms([
    'taxonomy'   => 'destination',
    'hide_empty' => true,
    'parent'     => 0,
    'number'     => 6,
    'orderby'    => 'count',
    'order'      => 'DESC',
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

                // 없으면 Unsplash 기본 이미지 사용
                if (!$image_url) {
                    $image_url = isset($destination_images[$dest->slug])
                        ? $destination_images[$dest->slug]
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
