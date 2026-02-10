<?php
/**
 * 수동 번역 스크립트 (WP-CLI)
 *
 * 아직 번역되지 않은 한국어 포스트를 찾아 일괄 번역합니다.
 * 이미 번역된 포스트는 건너뜁니다.
 *
 * 사용법:
 *   # 미번역 포스트 확인만 (dry-run)
 *   wp eval-file wp-content/themes/flavor-trip/translate-posts.php --allow-root
 *
 *   # 실제 번역 실행
 *   wp eval-file wp-content/themes/flavor-trip/translate-posts.php -- --run --allow-root
 *
 *   # 특정 포스트만 번역
 *   wp eval-file wp-content/themes/flavor-trip/translate-posts.php -- --run --id=123 --allow-root
 *
 * @package Flavor_Trip
 */

if (!defined('ABSPATH')) {
    exit('This script must be run via WP-CLI.');
}

if (!function_exists('PLL') || !PLL()) {
    WP_CLI::error('Polylang is not active.');
}

// ── 인자 파싱 ──
global $argv;
$args = $argv ?? [];
$do_run = in_array('--run', $args, true);
$target_id = 0;
foreach ($args as $a) {
    if (str_starts_with($a, '--id=')) {
        $target_id = (int) substr($a, 5);
    }
}

$post_types = ['travel_itinerary', 'destination_guide', 'vlog_curation', 'post'];

// ── 미번역 포스트 조회 ──
$query_args = [
    'post_type'      => $post_types,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'lang'           => 'ko',
];

if ($target_id) {
    $query_args['post__in'] = [$target_id];
}

$ko_posts = get_posts($query_args);

// 미번역 포스트만 필터
$untranslated = [];
foreach ($ko_posts as $p) {
    $lang = pll_get_post_language($p->ID);
    if (!$lang) {
        pll_set_post_language($p->ID, 'ko');
    }

    $existing = PLL()->model->post->get_translations($p->ID);
    $missing = [];
    foreach (FT_TRANSLATE_LANGS as $pll_slug => $gt_lang) {
        if (empty($existing[$pll_slug])) {
            $missing[] = $pll_slug;
        }
    }
    if (!empty($missing)) {
        $untranslated[] = ['post' => $p, 'missing' => $missing];
    }
}

if (empty($untranslated)) {
    WP_CLI::success('모든 포스트가 번역 완료되어 있습니다.');
    exit;
}

// ── 미번역 목록 출력 ──
WP_CLI::log('');
WP_CLI::log(sprintf('📋 미번역 포스트: %d개', count($untranslated)));
WP_CLI::log(str_repeat('─', 70));

foreach ($untranslated as $item) {
    $p = $item['post'];
    $missing_str = implode(', ', $item['missing']);
    WP_CLI::log(sprintf(
        '  ID:%-5d [%s] %s — 미번역: %s',
        $p->ID, $p->post_type, mb_strimwidth($p->post_title, 0, 40, '…'), $missing_str
    ));
}

WP_CLI::log('');

if (!$do_run) {
    WP_CLI::log('🔍 위는 미리보기입니다. 실제 번역하려면 --run 옵션을 추가하세요:');
    WP_CLI::log('   wp eval-file wp-content/themes/flavor-trip/translate-posts.php -- --run --allow-root');
    exit;
}

// ── 번역 실행 ──
WP_CLI::log('🚀 번역을 시작합니다...');
WP_CLI::log('');

$total = count($untranslated);
$success = 0;
$fail = 0;

foreach ($untranslated as $idx => $item) {
    $p = $item['post'];
    $num = $idx + 1;

    WP_CLI::log(sprintf('[%d/%d] %s (ID:%d)', $num, $total, $p->post_title, $p->ID));

    foreach ($item['missing'] as $pll_slug) {
        $gt_lang = FT_TRANSLATE_LANGS[$pll_slug];
        $new_id = ft_translate_post_to_lang($p, $pll_slug, $gt_lang);

        if ($new_id) {
            WP_CLI::log(sprintf('  ✅ %s → ID:%d', $pll_slug, $new_id));
            $success++;
        } else {
            WP_CLI::log(sprintf('  ❌ %s 실패', $pll_slug));
            $fail++;
        }
    }
    WP_CLI::log('');
}

// ── 캐시 정리 ──
wp_cache_flush();
if (function_exists('PLL')) {
    PLL()->model->clean_languages_cache();
}
clean_term_cache([], '');

WP_CLI::log('');
WP_CLI::success(sprintf('번역 완료! 성공: %d, 실패: %d', $success, $fail));
