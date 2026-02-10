<?php
/**
 * 빵크럼 로직 + BreadcrumbList JSON-LD 스키마
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

/**
 * 빵크럼 HTML + JSON-LD 스키마 출력
 */
function ft_breadcrumbs() {
    if (is_front_page()) return;

    $items = [];
    $items[] = ['name' => __('홈', 'flavor-trip'), 'url' => home_url('/')];

    if (is_singular('travel_itinerary')) {
        $items[] = ['name' => __('여행 일정', 'flavor-trip'), 'url' => get_post_type_archive_link('travel_itinerary')];

        $destinations = get_the_terms(get_the_ID(), 'destination');
        if ($destinations && !is_wp_error($destinations)) {
            $dest = $destinations[0];
            // 부모 여행지가 있으면 추가
            if ($dest->parent) {
                $parent = get_term($dest->parent, 'destination');
                if ($parent && !is_wp_error($parent)) {
                    $items[] = ['name' => $parent->name, 'url' => get_term_link($parent)];
                }
            }
            $items[] = ['name' => $dest->name, 'url' => get_term_link($dest)];
        }

        $items[] = ['name' => get_the_title()];

    } elseif (is_singular('post')) {
        $cats = get_the_category();
        if ($cats) {
            $items[] = ['name' => $cats[0]->name, 'url' => get_category_link($cats[0]->term_id)];
        }
        $items[] = ['name' => get_the_title()];

    } elseif (is_singular('page')) {
        // 부모 페이지
        $post = get_post();
        $ancestors = get_post_ancestors($post);
        foreach (array_reverse($ancestors) as $ancestor_id) {
            $items[] = ['name' => get_the_title($ancestor_id), 'url' => get_permalink($ancestor_id)];
        }
        $items[] = ['name' => get_the_title()];

    } elseif (is_singular('destination_guide')) {
        $items[] = ['name' => __('도시 가이드', 'flavor-trip'), 'url' => get_post_type_archive_link('destination_guide')];
        $items[] = ['name' => get_the_title()];

    } elseif (is_singular('vlog_curation')) {
        $items[] = ['name' => __('브이로그', 'flavor-trip'), 'url' => get_post_type_archive_link('vlog_curation')];
        $items[] = ['name' => get_the_title()];

    } elseif (is_post_type_archive('vlog_curation')) {
        $items[] = ['name' => __('브이로그', 'flavor-trip')];

    } elseif (is_post_type_archive('destination_guide')) {
        $items[] = ['name' => __('도시 가이드', 'flavor-trip')];

    } elseif (is_post_type_archive('travel_itinerary')) {
        $items[] = ['name' => __('여행 일정', 'flavor-trip')];

    } elseif (is_tax('destination')) {
        $items[] = ['name' => __('여행 일정', 'flavor-trip'), 'url' => get_post_type_archive_link('travel_itinerary')];
        $term = get_queried_object();
        if ($term->parent) {
            $parent = get_term($term->parent, 'destination');
            if ($parent && !is_wp_error($parent)) {
                $items[] = ['name' => $parent->name, 'url' => get_term_link($parent)];
            }
        }
        $items[] = ['name' => $term->name];

    } elseif (is_tax('travel_style')) {
        $items[] = ['name' => __('여행 일정', 'flavor-trip'), 'url' => get_post_type_archive_link('travel_itinerary')];
        $items[] = ['name' => single_term_title('', false)];

    } elseif (is_category()) {
        $items[] = ['name' => single_cat_title('', false)];

    } elseif (is_tag()) {
        $items[] = ['name' => single_tag_title('', false)];

    } elseif (is_search()) {
        $items[] = ['name' => sprintf(__('"%s" 검색 결과', 'flavor-trip'), get_search_query())];

    } elseif (is_archive()) {
        $items[] = ['name' => get_the_archive_title()];

    } elseif (is_404()) {
        $items[] = ['name' => '404'];
    }

    // HTML 출력
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('빵크럼 네비게이션', 'flavor-trip') . '">';
    $count = count($items);
    foreach ($items as $i => $item) {
        if ($i > 0) {
            echo '<span class="separator" aria-hidden="true">/</span>';
        }
        if (isset($item['url']) && $i < $count - 1) {
            echo '<a href="' . esc_url($item['url']) . '">' . esc_html($item['name']) . '</a>';
        } else {
            echo '<span class="current" aria-current="page">' . esc_html($item['name']) . '</span>';
        }
    }
    echo '</nav>';

    // JSON-LD BreadcrumbList 스키마
    $schema_items = [];
    foreach ($items as $i => $item) {
        $entry = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $item['name'],
        ];
        if (isset($item['url'])) {
            $entry['item'] = $item['url'];
        }
        $schema_items[] = $entry;
    }

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $schema_items,
    ];

    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    echo '</script>' . "\n";
}
