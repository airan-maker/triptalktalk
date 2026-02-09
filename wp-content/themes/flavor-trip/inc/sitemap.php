<?php
/**
 * XML Sitemap 생성
 *
 * /sitemap.xml → 사이트맵 인덱스
 * /sitemap-posts.xml → 블로그 글
 * /sitemap-itineraries.xml → 여행 일정
 * /sitemap-taxonomies.xml → 택소노미 (여행지, 여행 스타일)
 * /sitemap-pages.xml → 페이지
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

// Rewrite 규칙 등록
add_action('init', function () {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?ft_sitemap=index', 'top');
    add_rewrite_rule('^sitemap-([a-z]+)\.xml$', 'index.php?ft_sitemap=$matches[1]', 'top');
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'ft_sitemap';
    return $vars;
});

add_action('template_redirect', function () {
    $sitemap_type = get_query_var('ft_sitemap');
    if (!$sitemap_type) return;

    header('Content-Type: application/xml; charset=UTF-8');
    header('X-Robots-Tag: noindex');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

    switch ($sitemap_type) {
        case 'index':
            ft_sitemap_index();
            break;
        case 'itineraries':
            ft_sitemap_posts('travel_itinerary');
            break;
        case 'posts':
            ft_sitemap_posts('post');
            break;
        case 'pages':
            ft_sitemap_posts('page');
            break;
        case 'taxonomies':
            ft_sitemap_taxonomies();
            break;
        default:
            ft_sitemap_index();
    }
    exit;
});

/**
 * 사이트맵 인덱스
 */
function ft_sitemap_index() {
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $types = ['itineraries', 'posts', 'pages', 'taxonomies'];
    foreach ($types as $type) {
        echo '  <sitemap>' . "\n";
        echo '    <loc>' . esc_url(home_url("/sitemap-{$type}.xml")) . '</loc>' . "\n";
        echo '    <lastmod>' . esc_html(date('c')) . '</lastmod>' . "\n";
        echo '  </sitemap>' . "\n";
    }

    echo '</sitemapindex>' . "\n";
}

/**
 * 게시물 사이트맵
 */
function ft_sitemap_posts($post_type) {
    $posts = get_posts([
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => 1000,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ]);

    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
    echo ' xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

    // 홈페이지 (페이지 사이트맵에서만)
    if ($post_type === 'page') {
        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
        echo '    <changefreq>daily</changefreq>' . "\n";
        echo '    <priority>1.0</priority>' . "\n";
        echo '  </url>' . "\n";
    }

    // 아카이브 페이지 (여행 일정)
    if ($post_type === 'travel_itinerary') {
        $archive_url = get_post_type_archive_link('travel_itinerary');
        if ($archive_url) {
            echo '  <url>' . "\n";
            echo '    <loc>' . esc_url($archive_url) . '</loc>' . "\n";
            echo '    <changefreq>daily</changefreq>' . "\n";
            echo '    <priority>0.8</priority>' . "\n";
            echo '  </url>' . "\n";
        }
    }

    foreach ($posts as $post) {
        $priority = ($post_type === 'travel_itinerary') ? '0.8' : '0.6';
        $changefreq = ($post_type === 'travel_itinerary') ? 'weekly' : 'monthly';

        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url(get_permalink($post)) . '</loc>' . "\n";
        echo '    <lastmod>' . esc_html(get_the_modified_date('c', $post)) . '</lastmod>' . "\n";
        echo '    <changefreq>' . $changefreq . '</changefreq>' . "\n";
        echo '    <priority>' . $priority . '</priority>' . "\n";

        // hreflang alternate (Polylang)
        if (function_exists('pll_get_post_translations')) {
            $translations = pll_get_post_translations($post->ID);
            if (count($translations) > 1) {
                $hreflang_map = [
                    'zh-cn' => 'zh-Hans', 'en-au' => 'en-AU',
                ];
                foreach ($translations as $lang => $trans_id) {
                    $trans_url = get_permalink($trans_id);
                    $hreflang = $hreflang_map[$lang] ?? $lang;
                    echo '    <xhtml:link rel="alternate" hreflang="' . esc_attr($hreflang) . '" href="' . esc_url($trans_url) . '"/>' . "\n";
                }
            }
        }

        echo '  </url>' . "\n";
    }

    echo '</urlset>' . "\n";
}

/**
 * 택소노미 사이트맵
 */
function ft_sitemap_taxonomies() {
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach (['destination', 'travel_style'] as $taxonomy) {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ]);

        if (is_wp_error($terms)) continue;

        foreach ($terms as $term) {
            $url = get_term_link($term);
            if (is_wp_error($url)) continue;

            echo '  <url>' . "\n";
            echo '    <loc>' . esc_url($url) . '</loc>' . "\n";
            echo '    <changefreq>weekly</changefreq>' . "\n";
            echo '    <priority>0.6</priority>' . "\n";
            echo '  </url>' . "\n";
        }
    }

    echo '</urlset>' . "\n";
}
