<?php
/**
 * WP-CLI import script for vlog_curation posts from JSONL.
 *
 * Features:
 *   - Creates/updates vlog_curation posts from JSONL
 *   - Assigns destination taxonomy (hierarchical: parent + child)
 *   - Assigns travel_style taxonomy
 *   - Sets Polylang language to 'ko'
 *   - Defensive title/excerpt cleaning (Gemini text residue)
 *
 * Usage (env vars):
 *   FT_IMPORT_FILE=/path/to/vlogs.jsonl FT_IMPORT_STATUS=draft FT_IMPORT_AUTHOR=1 \
 *     wp eval-file /path/to/import-vlogs.wpcli.php --allow-root
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "This script must run via wp eval-file.\n");
    exit(1);
}

function ft_import_arg_value($argv, $name, $default = null) {
    $prefix = "--{$name}=";
    foreach ($argv as $arg) {
        if (strpos($arg, $prefix) === 0) {
            return substr($arg, strlen($prefix));
        }
    }
    return $default;
}

function ft_import_parse_jsonl($file) {
    if (!file_exists($file)) {
        throw new RuntimeException("File not found: {$file}");
    }
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $rows = [];
    foreach ($lines as $idx => $line) {
        $data = json_decode($line, true);
        if (!is_array($data)) {
            throw new RuntimeException("Invalid JSON at line " . ($idx + 1));
        }
        $rows[] = $data;
    }
    return $rows;
}

function ft_import_youtube_id_from_url($url) {
    if (!$url) return null;
    $parts = wp_parse_url($url);
    if (!$parts || empty($parts['query'])) return null;
    parse_str($parts['query'], $q);
    return !empty($q['v']) ? sanitize_text_field($q['v']) : null;
}

function ft_import_find_post_by_youtube_id($youtube_id) {
    if (!$youtube_id) return 0;
    $posts = get_posts([
        'post_type'      => 'vlog_curation',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_ft_vlog_youtube_id',
        'meta_value'     => $youtube_id,
    ]);
    return !empty($posts[0]) ? (int) $posts[0] : 0;
}

/**
 * Clean title: strip Gemini residue, markdown, excessive whitespace.
 */
function ft_import_clean_title($title) {
    // Remove common Gemini UI text fragments
    $garbage = [
        'Ask about this video',
        'Summarize the video',
        'Recommend related content',
        'AI can make mistakes',
        'Made with Gemini',
        '자세히 설명해줘',
    ];
    foreach ($garbage as $g) {
        $title = str_replace($g, '', $title);
    }
    // Strip markdown bold
    $title = preg_replace('/\*\*/', '', $title);
    // Collapse whitespace
    $title = preg_replace('/\s+/', ' ', trim($title));
    return $title;
}

function ft_import_map_timeline($row) {
    $timeline = [];

    if (!empty($row['vlogDraft']['timeline']) && is_array($row['vlogDraft']['timeline'])) {
        foreach ($row['vlogDraft']['timeline'] as $item) {
            if (empty($item['title']) && empty($item['description'])) continue;
            $timeline[] = [
                'time'        => sanitize_text_field($item['time'] ?? ''),
                'title'       => sanitize_text_field($item['title'] ?? ''),
                'description' => sanitize_text_field($item['description'] ?? ''),
            ];
        }
    }

    if (empty($timeline) && !empty($row['timeline']) && is_array($row['timeline'])) {
        foreach ($row['timeline'] as $item) {
            $title = sanitize_text_field($item['place'] ?? '');
            $desc = sanitize_text_field($item['summary'] ?? '');
            if ($title === '' && $desc === '') continue;
            $timeline[] = [
                'time'        => '',
                'title'       => $title,
                'description' => $desc,
            ];
        }
    }

    return $timeline;
}

function ft_import_map_spots($row) {
    $spots = [];
    if (!empty($row['vlogDraft']['spots']) && is_array($row['vlogDraft']['spots'])) {
        foreach ($row['vlogDraft']['spots'] as $spot) {
            if (empty($spot['name'])) continue;
            $spots[] = [
                'name'        => sanitize_text_field($spot['name'] ?? ''),
                'lat'         => is_numeric($spot['lat'] ?? null) ? (float) $spot['lat'] : 0.0,
                'lng'         => is_numeric($spot['lng'] ?? null) ? (float) $spot['lng'] : 0.0,
                'description' => sanitize_text_field($spot['description'] ?? ''),
            ];
        }
    }

    if (empty($spots) && !empty($row['places']) && is_array($row['places'])) {
        foreach ($row['places'] as $place) {
            if (empty($place['name'])) continue;
            $desc = '';
            if (!empty($place['activities'][0])) {
                $desc = $place['activities'][0];
            } elseif (!empty($place['mustSee'][0])) {
                $desc = $place['mustSee'][0];
            }
            $spots[] = [
                'name'        => sanitize_text_field($place['name']),
                'lat'         => 0.0,
                'lng'         => 0.0,
                'description' => sanitize_text_field($desc),
            ];
        }
    }

    return $spots;
}

/**
 * Ensure a destination term exists, creating parent/child as needed.
 * Returns the term ID.
 */
function ft_import_ensure_destination_term($slug, $parent_slug = '') {
    $parent_id = 0;

    // Create parent term first if needed
    if ($parent_slug && $parent_slug !== $slug) {
        $parent_term = get_term_by('slug', $parent_slug, 'destination');
        if ($parent_term) {
            $parent_id = (int) $parent_term->term_id;
        } else {
            $result = wp_insert_term(ucfirst($parent_slug), 'destination', ['slug' => $parent_slug]);
            if (!is_wp_error($result)) {
                $parent_id = (int) $result['term_id'];
                fwrite(STDOUT, "  Created parent destination: {$parent_slug} (ID={$parent_id})\n");
            }
        }
    }

    $term = get_term_by('slug', $slug, 'destination');
    if ($term) {
        return (int) $term->term_id;
    }

    $args = ['slug' => $slug];
    if ($parent_id > 0) {
        $args['parent'] = $parent_id;
    }
    $result = wp_insert_term(ucfirst($slug), 'destination', $args);
    if (is_wp_error($result)) {
        if ($result->get_error_code() === 'term_exists') {
            return (int) $result->get_error_data('term_exists');
        }
        fwrite(STDERR, "  Failed to create destination '{$slug}': {$result->get_error_message()}\n");
        return 0;
    }
    fwrite(STDOUT, "  Created destination: {$slug} (ID={$result['term_id']})\n");
    return (int) $result['term_id'];
}

/**
 * Ensure a travel_style term exists.
 * Returns the term ID.
 */
function ft_import_ensure_style_term($name) {
    $term = get_term_by('name', $name, 'travel_style');
    if ($term) {
        return (int) $term->term_id;
    }
    $result = wp_insert_term($name, 'travel_style');
    if (is_wp_error($result)) {
        if ($result->get_error_code() === 'term_exists') {
            return (int) $result->get_error_data('term_exists');
        }
        fwrite(STDERR, "  Failed to create travel_style '{$name}': {$result->get_error_message()}\n");
        return 0;
    }
    fwrite(STDOUT, "  Created travel_style: {$name} (ID={$result['term_id']})\n");
    return (int) $result['term_id'];
}

/**
 * Map of child destination slugs to their parent country slugs.
 * Must match CITY_MAP in raw-to-jsonl.mjs.
 */
function ft_import_get_dest_parent_map() {
    return [
        'kyoto'      => 'japan',
        'tokyo'      => 'japan',
        'osaka'      => 'japan',
        'sapporo'    => 'japan',
        'fukuoka'    => 'japan',
        'hiroshima'  => 'japan',
        'kanazawa'   => 'japan',
        'nara'       => 'japan',
        'nagoya'     => 'japan',
        'yokohama'   => 'japan',
        'kobe'       => 'japan',
        'okinawa'    => 'japan',
        'mie'        => 'japan',
        'shizuoka'   => 'japan',
        'kawaguchiko' => 'japan',
        'fujisan'    => 'japan',
        'shiga'      => 'japan',
        'gujo'       => 'japan',
        'seoul'      => 'korea',
        'jeju'       => 'korea',
        'busan'      => 'korea',
        'bangkok'    => 'thailand',
        'nhatrang'   => 'vietnam',
        'danang'     => 'vietnam',
        'hoian'      => 'vietnam',
        'hochiminh'  => 'vietnam',
        'paris'      => 'france',
        'hawaii'     => 'usa',
        'taipei'     => 'taiwan',
        'shanghai'   => 'china',
    ];
}

/**
 * Assign destination terms to a post from the JSONL destination array.
 */
function ft_import_assign_destinations($post_id, $destinations) {
    if (empty($destinations) || !is_array($destinations)) return;

    $parent_map = ft_import_get_dest_parent_map();
    $term_ids = [];

    foreach ($destinations as $slug) {
        $parent_slug = $parent_map[$slug] ?? '';
        // Country-level slugs have no parent
        $term_id = ft_import_ensure_destination_term($slug, $parent_slug);
        if ($term_id > 0) {
            $term_ids[] = $term_id;
        }
    }

    if (!empty($term_ids)) {
        wp_set_post_terms($post_id, $term_ids, 'destination');
    }
}

/**
 * Assign travel_style terms to a post from the JSONL travelStyle array.
 */
function ft_import_assign_styles($post_id, $styles) {
    if (empty($styles) || !is_array($styles)) return;

    $term_ids = [];
    foreach ($styles as $name) {
        $term_id = ft_import_ensure_style_term($name);
        if ($term_id > 0) {
            $term_ids[] = $term_id;
        }
    }

    if (!empty($term_ids)) {
        wp_set_post_terms($post_id, $term_ids, 'travel_style');
    }
}

/**
 * Set Polylang language for a post (if Polylang is active).
 */
function ft_import_set_language($post_id, $lang = 'ko') {
    if (function_exists('pll_set_post_language')) {
        pll_set_post_language($post_id, $lang);
    }
}

// ── Main ────────────────────────────────────────────────────────────

$argv = $_SERVER['argv'] ?? [];
$file = getenv('FT_IMPORT_FILE') ?: ft_import_arg_value($argv, 'file');
$status = getenv('FT_IMPORT_STATUS') ?: ft_import_arg_value($argv, 'status', 'draft');
$author = (int) (getenv('FT_IMPORT_AUTHOR') ?: ft_import_arg_value($argv, 'author', 1));

if (!$file) {
    fwrite(STDERR, "Missing --file argument.\n");
    exit(1);
}

$rows = ft_import_parse_jsonl($file);
$created = 0;
$updated = 0;
$skipped = 0;

foreach ($rows as $row) {
    $source = is_array($row['source'] ?? null) ? $row['source'] : [];
    $video_url = $source['videoUrl'] ?? null;
    $youtube_id = $source['youtubeId'] ?? ft_import_youtube_id_from_url($video_url);
    $title = ft_import_clean_title(trim((string) ($row['vlogDraft']['title'] ?? $source['videoTitle'] ?? '')));

    if ($youtube_id === null || $youtube_id === '') {
        $skipped++;
        continue;
    }
    if ($title === '') {
        $title = "Vlog {$youtube_id}";
    }

    $excerpt = sanitize_textarea_field($row['vlogDraft']['excerpt'] ?? ($row['summary'] ?? ''));
    // Clean excerpt too
    $excerpt = ft_import_clean_title($excerpt);

    $post_id = ft_import_find_post_by_youtube_id($youtube_id);
    $post_data = [
        'post_type'    => 'vlog_curation',
        'post_status'  => $status,
        'post_title'   => wp_strip_all_tags($title),
        'post_excerpt' => $excerpt,
        'post_author'  => $author,
    ];

    if ($post_id > 0) {
        $post_data['ID'] = $post_id;
        $result = wp_update_post($post_data, true);
        if (is_wp_error($result)) {
            fwrite(STDERR, "Update failed for {$youtube_id}: {$result->get_error_message()}\n");
            continue;
        }
        $updated++;
    } else {
        $result = wp_insert_post($post_data, true);
        if (is_wp_error($result)) {
            fwrite(STDERR, "Create failed for {$youtube_id}: {$result->get_error_message()}\n");
            continue;
        }
        $post_id = (int) $result;
        $created++;
    }

    // Meta fields
    update_post_meta($post_id, '_ft_vlog_youtube_id', sanitize_text_field($youtube_id));
    update_post_meta($post_id, '_ft_vlog_channel_name', sanitize_text_field($source['channelName'] ?? ''));
    update_post_meta($post_id, '_ft_vlog_channel_url', esc_url_raw($source['channelUrl'] ?? ''));
    update_post_meta($post_id, '_ft_vlog_duration', sanitize_text_field($source['duration'] ?? ''));
    update_post_meta($post_id, '_ft_vlog_timeline', ft_import_map_timeline($row));
    update_post_meta($post_id, '_ft_vlog_spots', ft_import_map_spots($row));

    // Taxonomy assignments
    $destinations = $row['vlogDraft']['destination'] ?? [];
    $styles = $row['vlogDraft']['travelStyle'] ?? [];
    ft_import_assign_destinations($post_id, $destinations);
    ft_import_assign_styles($post_id, $styles);

    // Polylang language
    ft_import_set_language($post_id, 'ko');

    $dest_str = implode(',', $destinations);
    $style_str = implode(',', $styles);
    fwrite(STDOUT, "[{$youtube_id}] post_id={$post_id} dest=[{$dest_str}] style=[{$style_str}]\n");
}

fwrite(STDOUT, "\nImport done: created={$created}, updated={$updated}, skipped={$skipped}\n");
