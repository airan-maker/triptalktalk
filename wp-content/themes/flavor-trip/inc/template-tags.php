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
 * 현재 언어에 맞는 term만 반환 (Polylang 이중 필터)
 *
 * get_terms() + lang 파라미터 + pll_get_term_language() 수동 필터
 *
 * @param array $args get_terms() 인자
 * @return WP_Term[]|WP_Error
 */
function ft_get_terms_current_lang($args = []) {
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'ko';
    $args['lang'] = $current_lang;

    $terms = get_terms($args);

    if (is_wp_error($terms) || empty($terms)) {
        return $terms;
    }

    // pll_get_term_language()로 이중 필터
    if (function_exists('pll_get_term_language')) {
        $terms = array_values(array_filter($terms, function ($t) use ($current_lang) {
            return pll_get_term_language($t->term_id) === $current_lang;
        }));
    }

    return $terms;
}

/**
 * 번역된 택소노미 슬러그에서 원본(한국어) 슬러그 추출
 *
 * auto-translate.php가 생성하는 번역 슬러그 패턴: {원본}-{lang}
 * 예: tokyo-en, bangkok-ja, busan-zh-cn, new-york-en-au
 *
 * @param string $slug            택소노미 슬러그
 * @param array  $known_slugs     알려진 원본 슬러그 목록 (키)
 * @return string 매칭된 원본 슬러그 또는 원본 그대로
 */
function ft_resolve_destination_slug($slug, $known_slugs) {
    // 1) 정확히 매칭되면 바로 반환
    if (isset($known_slugs[$slug])) {
        return $slug;
    }

    // 2) 언어 접미사 제거 시도 (긴 것부터, 로마자 슬러그용)
    $lang_suffixes = ['-en-au', '-zh-cn', '-zh-hk', '-zh-tw', '-en', '-ja', '-fr', '-de'];
    foreach ($lang_suffixes as $suffix) {
        if (str_ends_with($slug, $suffix)) {
            $base = substr($slug, 0, -strlen($suffix));
            if (isset($known_slugs[$base])) {
                return $base;
            }
        }
    }

    // 3) _ft_ko_slug 메타 (CJK 언어 등 슬러그 역매핑 불가 시)
    $term = get_term_by('slug', $slug, 'destination');
    if ($term && !is_wp_error($term)) {
        $ko_slug = get_term_meta($term->term_id, '_ft_ko_slug', true);
        if ($ko_slug && isset($known_slugs[$ko_slug])) {
            return $ko_slug;
        }
    }

    // 4) Polylang 직접 조회 (제대로 링크된 경우)
    if ($term && function_exists('pll_get_term')) {
        $ko_term_id = pll_get_term($term->term_id, 'ko');
        if ($ko_term_id && $ko_term_id !== $term->term_id) {
            $ko_term = get_term($ko_term_id);
            if ($ko_term && !is_wp_error($ko_term) && isset($known_slugs[$ko_term->slug])) {
                return $ko_term->slug;
            }
        }
    }

    return $slug;
}

/**
 * 여행지 이미지 배열 (중앙 관리)
 *
 * @param string $size 'full' (w=1200) 또는 'card' (w=800)
 * @return array slug => URL
 */
function ft_get_destination_images($size = 'full') {
    $w = ($size === 'card') ? 800 : 1200;

    $base = [
        // 지역 (부모)
        'korea'          => 'photo-1517154421773-0529f29ea451',
        'japan'          => 'photo-1493976040374-85c8e12f0c0e',
        'east-asia'      => 'photo-1536599018102-9f803c140fc1',
        'southeast-asia' => 'photo-1552465011-b4e21bf6e79a',
        'europe'         => 'photo-1499856871958-5b9627545d1a',
        'north-america'  => 'photo-1485738422979-f5c462d49f74',
        'oceania'        => 'photo-1506973035872-a4ec16b8e8d9',
        // 한국
        'jeju'           => 'photo-1602934198239-ff2e47d124f8',
        'seoul'          => 'photo-1538485399081-7191377e8241',
        'busan'          => 'photo-1701172189149-450eecf09863',
        // 일본
        'tokyo'          => 'photo-1540959733332-eab4deabeeaf',
        'osaka'          => 'photo-1590559899731-a382839e5549',
        'fukuoka'        => 'photo-1576675784201-0e142b423952',
        'kyoto'          => 'photo-1493976040374-85c8e12f0c0e',
        'sapporo'        => 'photo-1519105467443-4779d0fb729d',
        'hiroshima'      => 'photo-1697605623014-c68d4b666420',
        'kanazawa'       => 'photo-1684695414445-685455eb85c5',
        'okinawa'        => 'photo-1590077428593-a55bb07c4665',
        // 동아시아
        'hongkong'       => 'photo-1536599018102-9f803c140fc1',
        'taipei'         => 'photo-1470004914212-05527e49370b',
        'macau'          => 'photo-1544892419-0d45a9eb8c24',
        // 동남아
        'bangkok'        => 'photo-1563492065599-3520f775eeed',
        'chiangmai'      => 'photo-1512553567410-96e6bf8d8b88',
        'singapore'      => 'photo-1525625293386-3f8f99389edd',
        'bali'           => 'photo-1537996194471-e657df975ab4',
        'danang'         => 'photo-1559592413-7cec4d0cae2b',
        'hanoi'          => 'photo-1583417319070-4a69db38a482',
        'hochiminh'      => 'photo-1583417319070-4a69db38a482',
        'cebu'           => 'photo-1505881502353-a1986add3762',
        'nhatrang'       => 'photo-1503188991764-408493f288b9',
        'vietnam'        => 'photo-1557750255-c76072572da4',
        'boracay'        => 'photo-1507525428034-b723cf961d3e',
        'phuket'         => 'photo-1589394815804-964ed0be2eb5',
        'kosamui'        => 'photo-1552465011-b4e21bf6e79a',
        'luangprabang'   => 'photo-1558431382-2bc7d0b53ca3',
        // 유럽
        'paris'          => 'photo-1502602898657-3e91760cbb34',
        'london'         => 'photo-1513635269975-59663e0ac1ad',
        'barcelona'      => 'photo-1583422409516-2895a77efded',
        'rome'           => 'photo-1552832230-c0197dd311b5',
        'switzerland'    => 'photo-1530122037265-a5f1f91d3b99',
        'prague'         => 'photo-1541849546-216549ae216d',
        'amsterdam'      => 'photo-1534351590666-13e3e96b5017',
        'vienna'         => 'photo-1516550893923-42d28e5677af',
        // 북미
        'new-york'       => 'photo-1496442226666-8d4d0e62e6e9',
        'los-angeles'    => 'photo-1534190760961-74e8c1c5c3da',
        'san-francisco'  => 'photo-1501594907352-04cda38ebc29',
        'las-vegas'      => 'photo-1605833556294-ea5c7a74f57d',
        'hawaii'         => 'photo-1507876466758-bc54f384809c',
        'vancouver'      => 'photo-1559511260-66a68e4c8b1b',
        // 오세아니아
        'sydney'         => 'photo-1506973035872-a4ec16b8e8d9',
        'melbourne'      => 'photo-1514395462725-fb4566210144',
        'gold-coast'     => 'photo-1506973035872-a4ec16b8e8d9',
        'new-zealand'    => 'photo-1507699622108-4be3abd695ad',
        // 기본
        'default'        => 'photo-1488646953014-85cb44e25828',
    ];

    $images = [];
    foreach ($base as $slug => $photo_id) {
        $images[$slug] = "https://images.unsplash.com/{$photo_id}?w={$w}&q=80";
    }
    return $images;
}

/**
 * 여행지 기반 폴백 이미지 URL
 *
 * @param int $post_id 게시물 ID
 * @return string|false 이미지 URL 또는 false
 */
function ft_get_destination_image($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();

    $destination_images = ft_get_destination_images('full');

    $destinations = get_the_terms($post_id, 'destination');
    if (!$destinations || is_wp_error($destinations)) {
        return $destination_images['default'];
    }

    $child_image = '';
    $parent_image = '';
    foreach ($destinations as $dest) {
        $resolved = ft_resolve_destination_slug($dest->slug, $destination_images);
        if (isset($destination_images[$resolved])) {
            if ($dest->parent > 0) {
                $child_image = $destination_images[$resolved];
            } else {
                $parent_image = $destination_images[$resolved];
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
