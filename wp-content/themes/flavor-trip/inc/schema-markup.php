<?php
/**
 * Schema.org JSON-LD 마크업
 * - Organization (사이트 전체)
 * - WebSite + SearchAction (홈페이지)
 * - TouristTrip (여행 일정)
 * - Article (블로그 포스트)
 * - FAQPage (여행 일정 FAQ)
 * - BreadcrumbList (빵크럼에서 처리)
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('wp_head', 'ft_output_schema_markup');

function ft_output_schema_markup() {
    $schemas = [];

    // Organization 스키마 (항상 출력)
    $schemas[] = ft_schema_organization();

    // WebSite 스키마 (항상 출력)
    $schemas[] = ft_schema_website();

    // 페이지별 스키마
    if (is_singular('travel_itinerary')) {
        $schemas[] = ft_schema_tourist_trip();
        $faq = ft_schema_faq();
        if ($faq) $schemas[] = $faq;
    } elseif (is_singular('vlog_curation')) {
        $schemas[] = ft_schema_video_object();
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
 * Organization 스키마
 */
function ft_schema_organization() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        '@id'      => home_url('/#organization'),
        'name'     => get_bloginfo('name'),
        'url'      => home_url('/'),
        'description' => get_bloginfo('description'),
    ];

    // 로고
    $logo = get_theme_mod('custom_logo');
    if ($logo) {
        $logo_url = wp_get_attachment_image_url($logo, 'full');
        if ($logo_url) {
            $schema['logo'] = [
                '@type'  => 'ImageObject',
                'url'    => $logo_url,
            ];
        }
    }

    // 소셜 프로필
    $social = [];
    $instagram = get_theme_mod('ft_social_instagram');
    $youtube = get_theme_mod('ft_social_youtube');
    $blog = get_theme_mod('ft_social_blog');
    if ($instagram) $social[] = $instagram;
    if ($youtube) $social[] = $youtube;
    if ($blog) $social[] = $blog;
    if ($social) {
        $schema['sameAs'] = $social;
    }

    return $schema;
}

/**
 * WebSite 스키마
 */
function ft_schema_website() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        '@id'      => home_url('/#website'),
        'name'     => get_bloginfo('name'),
        'url'      => home_url('/'),
        'publisher' => ['@id' => home_url('/#organization')],
        'inLanguage' => get_locale(),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'        => 'EntryPoint',
                'urlTemplate'  => home_url('/?s={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    return $schema;
}

/**
 * TouristTrip 스키마 (여행 일정)
 */
function ft_schema_tourist_trip() {
    $post_id    = get_the_ID();
    $dest_name  = get_post_meta($post_id, '_ft_destination_name', true);
    $duration   = get_post_meta($post_id, '_ft_duration', true);
    $days       = get_post_meta($post_id, '_ft_days', true) ?: [];
    $gallery    = get_post_meta($post_id, '_ft_gallery', true) ?: [];

    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'TouristTrip',
        'name'          => get_the_title(),
        'description'   => wp_strip_all_tags(get_the_excerpt() ?: wp_trim_words(get_the_content(), 50, '')),
        'url'           => get_permalink(),
        'inLanguage'    => get_locale(),
        'datePublished' => get_the_date('c'),
        'dateModified'  => get_the_modified_date('c'),
        'provider'      => ['@id' => home_url('/#organization')],
    ];

    // 여행지 (touristType → proper TouristDestination)
    $destinations = get_the_terms($post_id, 'destination');
    if ($destinations && !is_wp_error($destinations)) {
        $dest_names = wp_list_pluck($destinations, 'name');
        if ($dest_names) {
            $schema['touristType'] = implode(', ', $dest_names);
        }
    }

    // 여행지 장소 정보
    if ($dest_name) {
        $lat = get_post_meta($post_id, '_ft_map_lat', true);
        $lng = get_post_meta($post_id, '_ft_map_lng', true);

        $place = [
            '@type' => 'TouristDestination',
            'name'  => $dest_name,
        ];
        if ($lat && $lng) {
            $place['geo'] = [
                '@type'     => 'GeoCoordinates',
                'latitude'  => (float) $lat,
                'longitude' => (float) $lng,
            ];
        }
        $schema['itinerary'] = [
            '@type'           => 'ItemList',
            'name'            => $dest_name,
            'itemListElement' => [],
        ];
    }

    // 일자별 일정 (subTrip → itinerary ItemList)
    if ($days) {
        $list_items = [];
        $position = 0;
        foreach ($days as $i => $day) {
            $day_title = $day['title'] ?? '';
            $day_summary = $day['summary'] ?? $day['description'] ?? '';

            if (!empty($day_title)) {
                $position++;
                $day_item = [
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'name'     => 'Day ' . ($i + 1) . ': ' . $day_title,
                ];
                if ($day_summary) {
                    $day_item['description'] = $day_summary;
                }

                // spots을 TouristAttraction으로
                if (!empty($day['spots']) && is_array($day['spots'])) {
                    $attractions = [];
                    foreach ($day['spots'] as $spot) {
                        $attraction = [
                            '@type' => 'TouristAttraction',
                            'name'  => $spot['name'] ?? '',
                        ];
                        if (!empty($spot['description'])) {
                            $attraction['description'] = $spot['description'];
                        }
                        if (!empty($spot['lat']) && !empty($spot['lng'])) {
                            $attraction['geo'] = [
                                '@type'     => 'GeoCoordinates',
                                'latitude'  => (float) $spot['lat'],
                                'longitude' => (float) $spot['lng'],
                            ];
                        }
                        $attractions[] = $attraction;
                    }
                    if ($attractions) {
                        $day_item['item'] = $attractions;
                    }
                }

                $list_items[] = $day_item;
            }
        }
        if ($list_items) {
            if (isset($schema['itinerary'])) {
                $schema['itinerary']['itemListElement'] = $list_items;
            } else {
                $schema['itinerary'] = [
                    '@type'           => 'ItemList',
                    'itemListElement' => $list_items,
                ];
            }
        }
    }

    // 이미지
    $images = [];
    if (has_post_thumbnail()) {
        $images[] = get_the_post_thumbnail_url($post_id, 'full');
    } elseif (function_exists('ft_get_destination_image')) {
        $fallback = ft_get_destination_image($post_id);
        if ($fallback) $images[] = $fallback;
    }
    foreach (array_slice($gallery, 0, 5) as $img_id) {
        $url = wp_get_attachment_image_url($img_id, 'large');
        if ($url) $images[] = $url;
    }
    if ($images) {
        $schema['image'] = $images;
    }

    // Speakable (음성 검색 대응)
    $schema['speakable'] = [
        '@type'    => 'SpeakableSpecification',
        'cssSelector' => ['.itinerary-title', '.itinerary-description', '.day-header-title'],
    ];

    return $schema;
}

/**
 * FAQPage 스키마 (여행 일정에서 자동 생성)
 */
function ft_schema_faq() {
    $post_id   = get_the_ID();
    $dest_name = get_post_meta($post_id, '_ft_destination_name', true);
    $duration  = get_post_meta($post_id, '_ft_duration', true);
    $price     = get_post_meta($post_id, '_ft_price_range', true);
    $days      = get_post_meta($post_id, '_ft_days', true) ?: [];
    $title     = get_the_title();

    if (!$dest_name && !$duration) return null;

    $faq_items = [];

    // Q1: 이 여행은 며칠인가요?
    if ($duration) {
        $faq_items[] = [
            '@type'          => 'Question',
            'name'           => $dest_name ? "{$dest_name} 여행은 며칠이 필요한가요?" : "이 여행은 며칠이 필요한가요?",
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => "{$title}은(는) {$duration} 일정입니다.",
            ],
        ];
    }

    // Q2: 예산은 어느 정도인가요?
    if ($price) {
        $price_labels = [
            'budget'   => '가성비 좋은 저예산',
            'moderate' => '보통 수준의',
            'premium'  => '프리미엄',
            'luxury'   => '럭셔리 고급',
        ];
        $price_text = $price_labels[$price] ?? $price;
        $faq_items[] = [
            '@type'          => 'Question',
            'name'           => $dest_name ? "{$dest_name} 여행 예산은 어느 정도인가요?" : "이 여행의 예산은 어느 정도인가요?",
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => "이 여행은 {$price_text} 수준의 예산으로 계획되었습니다.",
            ],
        ];
    }

    // Q3: 주요 방문지는 어디인가요?
    if ($days) {
        $all_spots = [];
        foreach ($days as $day) {
            if (!empty($day['spots']) && is_array($day['spots'])) {
                foreach ($day['spots'] as $spot) {
                    if (!empty($spot['name'])) {
                        $all_spots[] = $spot['name'];
                    }
                }
            }
        }
        if ($all_spots) {
            $spots_text = implode(', ', array_slice($all_spots, 0, 8));
            $faq_items[] = [
                '@type'          => 'Question',
                'name'           => $dest_name ? "{$dest_name}에서 어디를 방문하나요?" : "주요 방문지는 어디인가요?",
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => "주요 방문지로는 {$spots_text} 등이 있습니다.",
                ],
            ];
        }
    }

    if (empty($faq_items)) return null;

    return [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $faq_items,
    ];
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
        'inLanguage'    => get_locale(),
        'datePublished' => get_the_date('c'),
        'dateModified'  => get_the_modified_date('c'),
        'author'        => [
            '@type' => 'Person',
            'name'  => get_the_author(),
            'url'   => get_author_posts_url(get_the_author_meta('ID')),
        ],
        'publisher'     => ['@id' => home_url('/#organization')],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id'   => get_permalink(),
        ],
    ];

    if (has_post_thumbnail()) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        if ($img) {
            $schema['image'] = [
                '@type'  => 'ImageObject',
                'url'    => $img[0],
                'width'  => $img[1],
                'height' => $img[2],
            ];
        }
    }

    $content = wp_strip_all_tags(get_the_content());
    $schema['wordCount'] = mb_strlen($content, 'UTF-8');

    // Speakable
    $schema['speakable'] = [
        '@type'    => 'SpeakableSpecification',
        'cssSelector' => ['.entry-title', '.entry-content'],
    ];

    return $schema;
}

/**
 * VideoObject 스키마 (브이로그 큐레이션)
 */
function ft_schema_video_object() {
    $post_id    = get_the_ID();
    $youtube_id = get_post_meta($post_id, '_ft_vlog_youtube_id', true);
    $duration   = get_post_meta($post_id, '_ft_vlog_duration', true);
    $channel    = get_post_meta($post_id, '_ft_vlog_channel_name', true);

    if (!$youtube_id) return null;

    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'VideoObject',
        'name'          => get_the_title(),
        'description'   => wp_strip_all_tags(get_the_excerpt() ?: wp_trim_words(get_the_content(), 50, '')),
        'thumbnailUrl'  => 'https://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg',
        'uploadDate'    => get_the_date('c'),
        'embedUrl'      => 'https://www.youtube.com/embed/' . $youtube_id,
        'contentUrl'    => 'https://www.youtube.com/watch?v=' . $youtube_id,
        'url'           => get_permalink(),
        'publisher'     => ['@id' => home_url('/#organization')],
    ];

    // ISO 8601 duration 변환 (15:30 → PT15M30S)
    if ($duration) {
        $parts = explode(':', $duration);
        if (count($parts) === 2) {
            $schema['duration'] = sprintf('PT%dM%dS', intval($parts[0]), intval($parts[1]));
        } elseif (count($parts) === 3) {
            $schema['duration'] = sprintf('PT%dH%dM%dS', intval($parts[0]), intval($parts[1]), intval($parts[2]));
        }
    }

    if ($channel) {
        $channel_url = get_post_meta($post_id, '_ft_vlog_channel_url', true);
        $schema['creator'] = [
            '@type' => 'Person',
            'name'  => $channel,
        ];
        if ($channel_url) {
            $schema['creator']['url'] = $channel_url;
        }
    }

    return $schema;
}
