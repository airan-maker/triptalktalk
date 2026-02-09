<?php
/**
 * llms.txt - AI 크롤러용 사이트 소개 파일
 *
 * /llms.txt 요청 시 사이트 구조와 콘텐츠를 AI가 이해하기 쉬운 형태로 제공
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('init', function () {
    add_rewrite_rule('^llms\.txt$', 'index.php?ft_llms_txt=1', 'top');
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'ft_llms_txt';
    return $vars;
});

add_action('template_redirect', function () {
    if (!get_query_var('ft_llms_txt')) return;

    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: public, max-age=86400');

    $site_name = get_bloginfo('name');
    $site_desc = get_bloginfo('description');
    $site_url  = home_url('/');

    echo "# {$site_name}\n\n";
    echo "> {$site_desc}\n\n";

    echo "## About\n\n";
    echo "{$site_name} is a multilingual travel itinerary platform that provides curated travel plans for popular destinations across Asia, Europe, North America, and Oceania. Content is available in 9 languages: Korean (primary), English, English (AU), Simplified Chinese, Traditional Chinese (TW), Traditional Chinese (HK), Japanese, French, and German.\n\n";

    echo "## Content Types\n\n";
    echo "### Travel Itineraries (여행 일정)\n";
    echo "Detailed day-by-day travel plans with:\n";
    echo "- Daily schedules with specific spots/attractions\n";
    echo "- Restaurant recommendations with menus and prices\n";
    echo "- Practical tips for each location\n";
    echo "- Klook booking links for activities\n";
    echo "- Map coordinates for all spots\n";
    echo "- Budget categories (budget/moderate/premium/luxury)\n";
    echo "- Difficulty levels (easy/moderate/hard)\n\n";

    echo "## Available Destinations\n\n";

    // 인기 여행지 목록
    $destinations = get_terms([
        'taxonomy'   => 'destination',
        'hide_empty' => true,
        'parent'     => 0,
        'orderby'    => 'count',
        'order'      => 'DESC',
    ]);
    if ($destinations && !is_wp_error($destinations)) {
        foreach ($destinations as $dest) {
            $children = get_terms([
                'taxonomy'   => 'destination',
                'hide_empty' => true,
                'parent'     => $dest->term_id,
            ]);
            $child_names = $children && !is_wp_error($children)
                ? implode(', ', wp_list_pluck($children, 'name'))
                : '';

            echo "- **{$dest->name}** ({$dest->count} itineraries)";
            if ($child_names) echo ": {$child_names}";
            echo "\n";
        }
    }
    echo "\n";

    echo "## Travel Styles\n\n";
    $styles = get_terms([
        'taxonomy'   => 'travel_style',
        'hide_empty' => true,
        'orderby'    => 'count',
        'order'      => 'DESC',
    ]);
    if ($styles && !is_wp_error($styles)) {
        foreach ($styles as $style) {
            echo "- {$style->name} ({$style->count})\n";
        }
    }
    echo "\n";

    echo "## Sample Itineraries\n\n";
    $sample_posts = get_posts([
        'post_type'      => 'travel_itinerary',
        'posts_per_page' => 10,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    foreach ($sample_posts as $post) {
        $duration = get_post_meta($post->ID, '_ft_duration', true);
        $dest = get_post_meta($post->ID, '_ft_destination_name', true);
        echo "- [{$post->post_title}](" . get_permalink($post) . ")";
        if ($duration) echo " — {$duration}";
        if ($dest) echo " in {$dest}";
        echo "\n";
    }
    echo "\n";

    echo "## URLs\n\n";
    echo "- Homepage: {$site_url}\n";
    echo "- All Itineraries: " . get_post_type_archive_link('travel_itinerary') . "\n";
    echo "- Sitemap: {$site_url}sitemap.xml\n";
    echo "- RSS Feed: " . get_feed_link() . "\n";

    exit;
});
