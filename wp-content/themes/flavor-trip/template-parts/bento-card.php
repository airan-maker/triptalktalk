<?php
/**
 * Bento Grid 카드 템플릿
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$post_id = get_the_ID();
$counter = get_query_var('bento_counter', 1);

// 여행지별 기본 이미지 (Unsplash)
$destination_images = [
    // 지역 (부모)
    'korea'          => 'https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=800&q=80',
    'japan'          => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&q=80',
    'east-asia'      => 'https://images.unsplash.com/photo-1536599018102-9f803c140fc1?w=800&q=80',
    'southeast-asia' => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=800&q=80',
    'europe'         => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=800&q=80',
    'north-america'  => 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?w=800&q=80',
    'oceania'        => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&q=80',
    // 한국
    'jeju'           => 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800&q=80',
    'seoul'          => 'https://images.unsplash.com/photo-1538485399081-7191377e8241?w=800&q=80',
    'busan'          => 'https://images.unsplash.com/photo-1701172189149-450eecf09863?w=800&q=80',
    // 일본
    'tokyo'          => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800&q=80',
    'osaka'          => 'https://images.unsplash.com/photo-1590559899731-a382839e5549?w=800&q=80',
    'fukuoka'        => 'https://images.unsplash.com/photo-1576675784201-0e142b423952?w=800&q=80',
    'kyoto'          => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&q=80',
    'sapporo'        => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&q=80',
    'okinawa'        => 'https://images.unsplash.com/photo-1590077428593-a55bb07c4665?w=800&q=80',
    // 동아시아
    'hongkong'       => 'https://images.unsplash.com/photo-1536599018102-9f803c140fc1?w=800&q=80',
    'taipei'         => 'https://images.unsplash.com/photo-1470004914212-05527e49370b?w=800&q=80',
    'macau'          => 'https://images.unsplash.com/photo-1544892419-0d45a9eb8c24?w=800&q=80',
    // 동남아
    'bangkok'        => 'https://images.unsplash.com/photo-1563492065599-3520f775eeed?w=800&q=80',
    'chiangmai'      => 'https://images.unsplash.com/photo-1512553567410-96e6bf8d8b88?w=800&q=80',
    'singapore'      => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&q=80',
    'bali'           => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=80',
    'danang'         => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?w=800&q=80',
    'hanoi'          => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=800&q=80',
    'hochiminh'      => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=800&q=80',
    'cebu'           => 'https://images.unsplash.com/photo-1505881502353-a1986add3762?w=800&q=80',
    'boracay'        => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80',
    'phuket'         => 'https://images.unsplash.com/photo-1589394815804-964ed0be2eb5?w=800&q=80',
    'kosamui'        => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=800&q=80',
    'luangprabang'   => 'https://images.unsplash.com/photo-1558431382-2bc7d0b53ca3?w=800&q=80',
    // 유럽
    'paris'          => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=80',
    'london'         => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&q=80',
    'barcelona'      => 'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=800&q=80',
    'rome'           => 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800&q=80',
    'switzerland'    => 'https://images.unsplash.com/photo-1530122037265-a5f1f91d3b99?w=800&q=80',
    'prague'         => 'https://images.unsplash.com/photo-1541849546-216549ae216d?w=800&q=80',
    'amsterdam'      => 'https://images.unsplash.com/photo-1534351590666-13e3e96b5017?w=800&q=80',
    'vienna'         => 'https://images.unsplash.com/photo-1516550893923-42d28e5677af?w=800&q=80',
    // 북미
    'new-york'       => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=80',
    'los-angeles'    => 'https://images.unsplash.com/photo-1534190760961-74e8c1c5c3da?w=800&q=80',
    'san-francisco'  => 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=800&q=80',
    'las-vegas'      => 'https://images.unsplash.com/photo-1605833556294-ea5c7a74f57d?w=800&q=80',
    'hawaii'         => 'https://images.unsplash.com/photo-1507876466758-bc54f384809c?w=800&q=80',
    'vancouver'      => 'https://images.unsplash.com/photo-1559511260-66a68e4c8b1b?w=800&q=80',
    // 오세아니아
    'sydney'         => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&q=80',
    'melbourne'      => 'https://images.unsplash.com/photo-1514395462725-fb4566210144?w=800&q=80',
    'gold-coast'     => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&q=80',
    'new-zealand'    => 'https://images.unsplash.com/photo-1507699622108-4be3abd695ad?w=800&q=80',
    // 기본
    'default'        => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&q=80',
];

// 이미지 URL 가져오기 (자식 여행지 우선)
$image_url = $destination_images['default'];
if (has_post_thumbnail($post_id)) {
    $image_url = get_the_post_thumbnail_url($post_id, 'large');
} else {
    $destinations = get_the_terms($post_id, 'destination');
    if ($destinations && !is_wp_error($destinations)) {
        $child_image = '';
        $parent_image = '';
        foreach ($destinations as $dest) {
            $resolved_slug = ft_resolve_destination_slug($dest->slug, $destination_images);
            if (isset($destination_images[$resolved_slug])) {
                if ($dest->parent > 0) {
                    $child_image = $destination_images[$resolved_slug];
                } else {
                    $parent_image = $destination_images[$resolved_slug];
                }
            }
        }
        $image_url = $child_image ?: $parent_image ?: $destination_images['default'];
    }
}

$duration = get_post_meta($post_id, '_ft_duration', true);
$price = get_post_meta($post_id, '_ft_price_range', true);
$difficulty = get_post_meta($post_id, '_ft_difficulty', true);
$dest_terms = get_the_terms($post_id, 'destination');

// 카드 사이즈 결정 (벤토 패턴)
$card_class = '';
if ($counter === 1) {
    $card_class = 'bento-card--featured';
} elseif ($counter === 4 || $counter === 9) {
    $card_class = 'bento-card--wide';
} elseif ($counter === 5 || $counter === 10) {
    $card_class = 'bento-card--tall';
}
?>

<article class="bento-card <?php echo esc_attr($card_class); ?>">
    <a href="<?php the_permalink(); ?>">
        <div class="bento-card-image">
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
        </div>
        <div class="bento-card-overlay"></div>

        <?php if ($duration) : ?>
            <span class="bento-card-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12,6 12,12 16,14"></polyline>
                </svg>
                <?php echo esc_html($duration); ?>
            </span>
        <?php endif; ?>

        <div class="bento-card-content">
            <div class="bento-card-tags">
                <?php if ($dest_terms && !is_wp_error($dest_terms)) : ?>
                    <?php foreach (array_slice($dest_terms, 0, 2) as $term) : ?>
                        <span class="bento-card-tag bento-card-tag--dest"><?php echo esc_html($term->name); ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <h2 class="bento-card-title"><?php the_title(); ?></h2>

            <p class="bento-card-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>

            <div class="bento-card-meta">
                <?php if ($difficulty) : ?>
                    <span><?php echo esc_html(ft_get_difficulty_label($difficulty)); ?></span>
                <?php endif; ?>
                <?php if ($price) : ?>
                    <span><?php echo esc_html(ft_get_price_label($price)); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </a>
</article>
