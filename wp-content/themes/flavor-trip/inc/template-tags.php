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
