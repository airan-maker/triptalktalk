<?php
/**
 * Schema.org JSON-LD 마크업
 * - TouristTrip (여행 일정)
 * - Article (블로그 포스트)
 * - WebSite + SearchAction (홈페이지)
 * - BreadcrumbList (빵크럼에서 처리)
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('wp_head', 'ft_output_schema_markup');

function ft_output_schema_markup() {
    $schemas = [];

    // WebSite 스키마 (항상 출력)
    $schemas[] = ft_schema_website();

    // 페이지별 스키마
    if (is_singular('travel_itinerary')) {
        $schemas[] = ft_schema_tourist_trip();
    } elseif (is_singular('post')) {
        $schemas[] = ft_schema_article();
    }

    // null 제거 및 출력
    $schemas = array_filter($schemas);
    foreach ($schemas as $schema) {
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n</script>\n";
    }
}

/**
 * WebSite 스키마
 */
function ft_schema_website() {
    return [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => get_bloginfo('name'),
        'url'      => home_url('/'),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'        => 'EntryPoint',
                'urlTemplate'  => home_url('/?s={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];
}

/**
 * TouristTrip 스키마 (여행 일정)
 */
function ft_schema_tourist_trip() {
    $post_id    = get_the_ID();
    $dest_name  = get_post_meta($post_id, '_ft_destination_name', true);
    $duration   = get_post_meta($post_id, '_ft_duration', true);
    $highlights = get_post_meta($post_id, '_ft_highlights', true);
    $days       = get_post_meta($post_id, '_ft_days', true) ?: [];
    $gallery    = get_post_meta($post_id, '_ft_gallery', true) ?: [];

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'TouristTrip',
        'name'        => get_the_title(),
        'description' => wp_strip_all_tags(get_the_excerpt() ?: wp_trim_words(get_the_content(), 50, '')),
        'url'         => get_permalink(),
    ];

    // 여행지
    if ($dest_name) {
        $schema['touristType'] = $dest_name;
        $lat = get_post_meta($post_id, '_ft_map_lat', true);
        $lng = get_post_meta($post_id, '_ft_map_lng', true);

        $place = [
            '@type' => 'Place',
            'name'  => $dest_name,
        ];
        if ($lat && $lng) {
            $place['geo'] = [
                '@type'     => 'GeoCoordinates',
                'latitude'  => (float) $lat,
                'longitude' => (float) $lng,
            ];
        }
        $schema['itinerary'] = $place;
    }

    // 일자별 일정
    if ($days) {
        $itinerary_items = [];
        foreach ($days as $i => $day) {
            if (!empty($day['title'])) {
                $itinerary_items[] = [
                    '@type'    => 'TouristAttraction',
                    'name'     => 'Day ' . ($i + 1) . ': ' . $day['title'],
                    'description' => $day['description'] ?? '',
                ];
            }
        }
        if ($itinerary_items) {
            $schema['subTrip'] = $itinerary_items;
        }
    }

    // 이미지
    $images = [];
    if (has_post_thumbnail()) {
        $images[] = get_the_post_thumbnail_url($post_id, 'full');
    }
    foreach (array_slice($gallery, 0, 5) as $img_id) {
        $url = wp_get_attachment_image_url($img_id, 'large');
        if ($url) $images[] = $url;
    }
    if ($images) {
        $schema['image'] = $images;
    }

    // 날짜
    $schema['datePublished'] = get_the_date('c');
    $schema['dateModified']  = get_the_modified_date('c');

    return $schema;
}

/**
 * Article 스키마 (블로그 포스트)
 */
function ft_schema_article() {
    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'Article',
        'headline'      => get_the_title(),
        'description'   => wp_strip_all_tags(get_the_excerpt()),
        'url'           => get_permalink(),
        'datePublished' => get_the_date('c'),
        'dateModified'  => get_the_modified_date('c'),
        'author'        => [
            '@type' => 'Person',
            'name'  => get_the_author(),
        ],
        'publisher'     => [
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
        ],
    ];

    if (has_post_thumbnail()) {
        $schema['image'] = get_the_post_thumbnail_url(get_the_ID(), 'full');
    }

    // wordCount
    $schema['wordCount'] = str_word_count(wp_strip_all_tags(get_the_content()));

    return $schema;
}
