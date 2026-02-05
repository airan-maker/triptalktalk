<?php
/**
 * ë¹µí¬??ë¡œì§ + BreadcrumbList JSON-LD ?¤í‚¤ë§? *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

/**
 * ë¹µí¬??HTML + JSON-LD ?¤í‚¤ë§?ì¶œë ¥
 */
function ft_breadcrumbs() {
    if (is_front_page()) return;

    $items = [];
    $items[] = ['name' => __('??, 'flavor-trip'), 'url' => home_url('/')];

    if (is_singular('travel_itinerary')) {
        $items[] = ['name' => __('?¬í–‰ ?¼ì •', 'flavor-trip'), 'url' => get_post_type_archive_link('travel_itinerary')];

        $destinations = get_the_terms(get_the_ID(), 'destination');
        if ($destinations && !is_wp_error($destinations)) {
            $dest = $destinations[0];
            // ë¶€ëª??¬í–‰ì§€ê°€ ?ˆìœ¼ë©?ì¶”ê?
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
        // ë¶€ëª??˜ì´ì§€
        $post = get_post();
        $ancestors = get_post_ancestors($post);
        foreach (array_reverse($ancestors) as $ancestor_id) {
            $items[] = ['name' => get_the_title($ancestor_id), 'url' => get_permalink($ancestor_id)];
        }
        $items[] = ['name' => get_the_title()];

    } elseif (is_post_type_archive('travel_itinerary')) {
        $items[] = ['name' => __('?¬í–‰ ?¼ì •', 'flavor-trip')];

    } elseif (is_tax('destination')) {
        $items[] = ['name' => __('?¬í–‰ ?¼ì •', 'flavor-trip'), 'url' => get_post_type_archive_link('travel_itinerary')];
        $term = get_queried_object();
        if ($term->parent) {
            $parent = get_term($term->parent, 'destination');
            if ($parent && !is_wp_error($parent)) {
                $items[] = ['name' => $parent->name, 'url' => get_term_link($parent)];
            }
        }
        $items[] = ['name' => $term->name];

    } elseif (is_tax('travel_style')) {
        $items[] = ['name' => __('?¬í–‰ ?¼ì •', 'flavor-trip'), 'url' => get_post_type_archive_link('travel_itinerary')];
        $items[] = ['name' => single_term_title('', false)];

    } elseif (is_category()) {
        $items[] = ['name' => single_cat_title('', false)];

    } elseif (is_tag()) {
        $items[] = ['name' => single_tag_title('', false)];

    } elseif (is_search()) {
        $items[] = ['name' => sprintf(__('"%s" ê²€??ê²°ê³¼', 'flavor-trip'), get_search_query())];

    } elseif (is_archive()) {
        $items[] = ['name' => get_the_archive_title()];

    } elseif (is_404()) {
        $items[] = ['name' => '404'];
    }

    // HTML ì¶œë ¥
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('ë¹µí¬???¤ë¹„ê²Œì´??, 'flavor-trip') . '">';
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

    // JSON-LD BreadcrumbList ?¤í‚¤ë§?    $schema_items = [];
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
