<?php
/**
 * 헬퍼 함수 (템플릿 태그)
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

/**
 * 페이지네이션
 */
function ft_pagination($query = null) {
    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }

    if ($query->max_num_pages <= 1) return;

    $paged = max(1, get_query_var('paged'));

    echo '<nav class="pagination" aria-label="' . esc_attr__('페이지 네비게이션', 'flavor-trip') . '">';
    echo paginate_links([
        'total'     => $query->max_num_pages,
        'current'   => $paged,
        'mid_size'  => 2,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
    ]);
    echo '</nav>';
}

/**
 * 읽기 시간 계산
 */
function ft_reading_time($post = null) {
    $post = get_post($post);
    if (!$post) return '';

    $content = wp_strip_all_tags($post->post_content);
    // 한글 기준 분당 500자
    $char_count = mb_strlen($content, 'UTF-8');
    $minutes = max(1, ceil($char_count / 500));

    return sprintf(__('%d분 읽기', 'flavor-trip'), $minutes);
}

/**
 * 가격대 라벨
 */
function ft_get_price_label($price) {
    $labels = [
        'budget'   => __('가성비', 'flavor-trip'),
        'moderate' => __('보통', 'flavor-trip'),
        'premium'  => __('프리미엄', 'flavor-trip'),
        'luxury'   => __('럭셔리', 'flavor-trip'),
    ];
    return $labels[$price] ?? $price;
}

/**
 * 난이도 라벨
 */
function ft_get_difficulty_label($difficulty) {
    $labels = [
        'easy'     => __('쉬움', 'flavor-trip'),
        'moderate' => __('보통', 'flavor-trip'),
        'hard'     => __('어려움', 'flavor-trip'),
    ];
    return $labels[$difficulty] ?? $difficulty;
}

/**
 * 여행지 기반 폴백 이미지 URL
 *
 * @param int $post_id 게시물 ID
 * @return string|false 이미지 URL 또는 false
 */
function ft_get_destination_image($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();

    $destination_images = [
        'korea'          => 'https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=1200&q=80',
        'japan'          => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=1200&q=80',
        'east-asia'      => 'https://images.unsplash.com/photo-1536599018102-9f803c140fc1?w=1200&q=80',
        'southeast-asia' => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=1200&q=80',
        'europe'         => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=1200&q=80',
        'north-america'  => 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?w=1200&q=80',
        'oceania'        => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=1200&q=80',
        'jeju'           => 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=1200&q=80',
        'seoul'          => 'https://images.unsplash.com/photo-1538485399081-7191377e8241?w=1200&q=80',
        'busan'          => 'https://images.unsplash.com/photo-1701172189149-450eecf09863?w=1200&q=80',
        'tokyo'          => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=1200&q=80',
        'osaka'          => 'https://images.unsplash.com/photo-1590559899731-a382839e5549?w=1200&q=80',
        'fukuoka'        => 'https://images.unsplash.com/photo-1576675784201-0e142b423952?w=1200&q=80',
        'kyoto'          => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=1200&q=80',
        'paris'          => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=1200&q=80',
        'london'         => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=1200&q=80',
        'hawaii'         => 'https://images.unsplash.com/photo-1507876466758-bc54f384809c?w=1200&q=80',
        'sydney'         => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=1200&q=80',
        'bangkok'        => 'https://images.unsplash.com/photo-1563492065599-3520f775eeed?w=1200&q=80',
        'singapore'      => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=1200&q=80',
        'taipei'         => 'https://images.unsplash.com/photo-1470004914212-05527e49370b?w=1200&q=80',
        'hongkong'       => 'https://images.unsplash.com/photo-1536599018102-9f803c140fc1?w=1200&q=80',
        'new-york'       => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=1200&q=80',
        'cebu'           => 'https://images.unsplash.com/photo-1505881502353-a1986add3762?w=1200&q=80',
        'danang'         => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?w=1200&q=80',
        'bali'           => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1200&q=80',
        'default'        => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1200&q=80',
    ];

    $destinations = get_the_terms($post_id, 'destination');
    if (!$destinations || is_wp_error($destinations)) {
        return $destination_images['default'];
    }

    $child_image = '';
    $parent_image = '';
    foreach ($destinations as $dest) {
        $slug = $dest->slug;
        // Polylang: 번역된 슬러그 → 한국어 원본 슬러그
        if (!isset($destination_images[$slug]) && function_exists('pll_get_term')) {
            $ko_term_id = pll_get_term($dest->term_id, 'ko');
            if ($ko_term_id && $ko_term_id !== $dest->term_id) {
                $ko_term = get_term($ko_term_id);
                if ($ko_term && !is_wp_error($ko_term)) {
                    $slug = $ko_term->slug;
                }
            }
        }
        if (isset($destination_images[$slug])) {
            if ($dest->parent > 0) {
                $child_image = $destination_images[$slug];
            } else {
                $parent_image = $destination_images[$slug];
            }
        }
    }

    return $child_image ?: $parent_image ?: $destination_images['default'];
}

/**
 * Klook 제휴 링크 생성
 *
 * 커스터마이저에 설정된 AID를 자동으로 URL에 붙여줍니다.
 *
 * @param string $url   Klook 상품 URL
 * @param string $label 링크 텍스트
 * @return string       <a> 태그 HTML (AID 없으면 빈 문자열)
 */
function ft_klook_link($url, $label = '예약하기') {
    $aid = get_theme_mod('ft_klook_aid', '');
    if (empty($aid)) {
        return '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer nofollow sponsored">' . esc_html($label) . '</a>';
    }
    $url = add_query_arg('aid', $aid, $url);
    return '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer nofollow sponsored">' . esc_html($label) . '</a>';
}

/**
 * Klook 제휴 URL만 반환 (태그 없이)
 *
 * @param string $url Klook 상품 URL
 * @return string     AID가 붙은 URL
 */
function ft_klook_url($url) {
    $aid = get_theme_mod('ft_klook_aid', '');
    if (!empty($aid)) {
        $url = add_query_arg('aid', $aid, $url);
    }
    return esc_url($url);
}
