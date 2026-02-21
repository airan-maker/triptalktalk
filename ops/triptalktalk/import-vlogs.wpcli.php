<?php
/**
 * WP-CLI import script for vlog_curation posts from JSONL.
 *
 * Usage:
 *   wp eval-file ops/triptalktalk/import-vlogs.wpcli.php -- \
 *     --file=/var/www/triptalktalk/shared/import/vlogs.jsonl \
 *     --status=draft \
 *     --author=1
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "This script must run via wp eval-file.\n");
    exit(1);
}

function arg_value($argv, $name, $default = null) {
    $prefix = "--{$name}=";
    foreach ($argv as $arg) {
        if (strpos($arg, $prefix) === 0) {
            return substr($arg, strlen($prefix));
        }
    }
    return $default;
}

function parse_jsonl_file($file) {
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

function youtube_id_from_url($url) {
    if (!$url) return null;
    $parts = wp_parse_url($url);
    if (!$parts || empty($parts['query'])) return null;
    parse_str($parts['query'], $q);
    return !empty($q['v']) ? sanitize_text_field($q['v']) : null;
}

function find_post_by_youtube_id($youtube_id) {
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

function map_timeline($row) {
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

function map_spots($row) {
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

$argv = $_SERVER['argv'] ?? [];
$file = arg_value($argv, 'file');
$status = arg_value($argv, 'status', 'draft');
$author = (int) arg_value($argv, 'author', 1);

if (!$file) {
    fwrite(STDERR, "Missing --file argument.\n");
    exit(1);
}

$rows = parse_jsonl_file($file);
$created = 0;
$updated = 0;
$skipped = 0;

foreach ($rows as $row) {
    $source = is_array($row['source'] ?? null) ? $row['source'] : [];
    $video_url = $source['videoUrl'] ?? null;
    $youtube_id = $source['youtubeId'] ?? youtube_id_from_url($video_url);
    $title = trim((string) ($row['vlogDraft']['title'] ?? $source['videoTitle'] ?? ''));

    if ($youtube_id === null || $youtube_id === '') {
        $skipped++;
        continue;
    }
    if ($title === '') {
        $title = "Vlog {$youtube_id}";
    }

    $post_id = find_post_by_youtube_id($youtube_id);
    $post_data = [
        'post_type'    => 'vlog_curation',
        'post_status'  => $status,
        'post_title'   => wp_strip_all_tags($title),
        'post_excerpt' => sanitize_textarea_field($row['vlogDraft']['excerpt'] ?? ($row['summary'] ?? '')),
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

    update_post_meta($post_id, '_ft_vlog_youtube_id', sanitize_text_field($youtube_id));
    update_post_meta($post_id, '_ft_vlog_channel_name', sanitize_text_field($source['channelName'] ?? ''));
    update_post_meta($post_id, '_ft_vlog_channel_url', esc_url_raw($source['channelUrl'] ?? ''));
    update_post_meta($post_id, '_ft_vlog_duration', sanitize_text_field($source['duration'] ?? ''));
    update_post_meta($post_id, '_ft_vlog_timeline', map_timeline($row));
    update_post_meta($post_id, '_ft_vlog_spots', map_spots($row));
}

fwrite(STDOUT, "Import done: created={$created}, updated={$updated}, skipped={$skipped}\n");
