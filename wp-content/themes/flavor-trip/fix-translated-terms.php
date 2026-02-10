<?php
/**
 * 번역된 택소노미 정리 스크립트 (일회성)
 *
 * 문제:
 * 1. 동일 언어에 같은 이름의 term이 여러 개 존재 (auto-translate 반복 실행)
 * 2. 번역된 자식 term의 parent=0 (부모-자식 관계 누락)
 * 3. _ft_ko_slug 메타 누락
 *
 * 실행:
 * docker-compose exec wordpress wp eval-file \
 *   /var/www/html/wp-content/themes/flavor-trip/fix-translated-terms.php --allow-root
 *
 * @package Flavor_Trip
 */

if (!defined('ABSPATH')) {
    // WP-CLI eval-file에서 실행 시 ABSPATH 정의되어 있음
    exit('This script must be run via WP-CLI.');
}

if (!function_exists('PLL') || !PLL()) {
    WP_CLI::error('Polylang is not active.');
}

$taxonomies = ['destination', 'travel_style'];
$languages = ['en', 'zh-cn', 'ja', 'fr', 'de'];

$stats = [
    'duplicates_merged' => 0,
    'parents_fixed'     => 0,
    'ko_slug_set'       => 0,
    'polylang_linked'   => 0,
];

// ═══════════════════════════════════════════════════════
// 1. 중복 term 병합 (같은 이름 + 같은 taxonomy + 같은 언어)
// ═══════════════════════════════════════════════════════
WP_CLI::log('');
WP_CLI::log('═══ Step 1: Merge duplicate terms ═══');

foreach ($taxonomies as $taxonomy) {
    foreach ($languages as $lang) {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'lang'       => $lang,
        ]);

        if (is_wp_error($terms) || empty($terms)) continue;

        // pll_get_term_language 이중 필터
        $terms = array_filter($terms, function ($t) use ($lang) {
            return pll_get_term_language($t->term_id) === $lang;
        });

        // 이름별 그룹화
        $by_name = [];
        foreach ($terms as $t) {
            $key = mb_strtolower($t->name, 'UTF-8');
            $by_name[$key][] = $t;
        }

        foreach ($by_name as $name => $group) {
            if (count($group) < 2) continue;

            // canonical = Polylang 번역 링크가 있는 것 우선, 없으면 가장 많은 포스트, 없으면 가장 낮은 ID
            usort($group, function ($a, $b) {
                $a_linked = pll_get_term($a->term_id, 'ko') ? 1 : 0;
                $b_linked = pll_get_term($b->term_id, 'ko') ? 1 : 0;
                if ($a_linked !== $b_linked) return $b_linked - $a_linked;
                if ($a->count !== $b->count) return $b->count - $a->count;
                return $a->term_id - $b->term_id;
            });

            $canonical = array_shift($group);
            $duplicates = $group;

            foreach ($duplicates as $dup) {
                // 중복 term에 연결된 포스트를 canonical로 이동
                $posts = get_objects_in_term($dup->term_id, $taxonomy);
                if (!is_wp_error($posts) && !empty($posts)) {
                    foreach ($posts as $pid) {
                        wp_remove_object_terms($pid, $dup->term_id, $taxonomy);
                        wp_set_object_terms($pid, [$canonical->term_id], $taxonomy, true);
                    }
                }

                // 중복 term의 Polylang 링크가 있다면 canonical로 이전
                $ko_id = pll_get_term($dup->term_id, 'ko');
                if ($ko_id && !pll_get_term($canonical->term_id, 'ko')) {
                    $group_trans = PLL()->model->term->get_translations($ko_id);
                    $group_trans[$lang] = $canonical->term_id;
                    PLL()->model->term->save_translations($ko_id, $group_trans);
                    pll_set_term_language($canonical->term_id, $lang);
                }

                // _ft_ko_slug 메타 이전
                $ko_slug = get_term_meta($dup->term_id, '_ft_ko_slug', true);
                if ($ko_slug && !get_term_meta($canonical->term_id, '_ft_ko_slug', true)) {
                    update_term_meta($canonical->term_id, '_ft_ko_slug', $ko_slug);
                }

                // 중복 term 삭제
                wp_delete_term($dup->term_id, $taxonomy);
                $stats['duplicates_merged']++;
                WP_CLI::log("  Merged [{$lang}] {$taxonomy}: \"{$dup->name}\" (ID:{$dup->term_id}) → \"{$canonical->name}\" (ID:{$canonical->term_id})");
            }
        }
    }
}

// ═══════════════════════════════════════════════════════
// 2. 부모-자식 관계 복원
// ═══════════════════════════════════════════════════════
WP_CLI::log('');
WP_CLI::log('═══ Step 2: Fix parent-child relationships ═══');

foreach ($taxonomies as $taxonomy) {
    if (!is_taxonomy_hierarchical($taxonomy)) continue;

    foreach ($languages as $lang) {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'lang'       => $lang,
        ]);

        if (is_wp_error($terms) || empty($terms)) continue;

        $terms = array_filter($terms, function ($t) use ($lang) {
            return pll_get_term_language($t->term_id) === $lang;
        });

        foreach ($terms as $t) {
            // parent=0인 번역 term만 처리
            if ($t->parent > 0) continue;

            // 한국어 원본 term 찾기
            $ko_term_id = pll_get_term($t->term_id, 'ko');
            if (!$ko_term_id || $ko_term_id === $t->term_id) continue;

            $ko_term = get_term($ko_term_id, $taxonomy);
            if (!$ko_term || is_wp_error($ko_term) || $ko_term->parent === 0) continue;

            // 한국어 부모의 번역본 찾기
            $translated_parent_id = pll_get_term($ko_term->parent, $lang);
            if (!$translated_parent_id) continue;

            // 부모 설정
            wp_update_term($t->term_id, $taxonomy, ['parent' => $translated_parent_id]);
            $stats['parents_fixed']++;
            WP_CLI::log("  Fixed [{$lang}] {$taxonomy}: \"{$t->name}\" (ID:{$t->term_id}) → parent:{$translated_parent_id}");
        }
    }
}

// ═══════════════════════════════════════════════════════
// 3. _ft_ko_slug 메타 설정 (누락된 것만)
// ═══════════════════════════════════════════════════════
WP_CLI::log('');
WP_CLI::log('═══ Step 3: Set missing _ft_ko_slug meta ═══');

foreach ($taxonomies as $taxonomy) {
    foreach ($languages as $lang) {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'lang'       => $lang,
        ]);

        if (is_wp_error($terms) || empty($terms)) continue;

        $terms = array_filter($terms, function ($t) use ($lang) {
            return pll_get_term_language($t->term_id) === $lang;
        });

        foreach ($terms as $t) {
            $existing_ko_slug = get_term_meta($t->term_id, '_ft_ko_slug', true);
            if ($existing_ko_slug) continue;

            // Polylang 링크로 한국어 원본 찾기
            $ko_term_id = pll_get_term($t->term_id, 'ko');
            if (!$ko_term_id || $ko_term_id === $t->term_id) continue;

            $ko_term = get_term($ko_term_id, $taxonomy);
            if (!$ko_term || is_wp_error($ko_term)) continue;

            update_term_meta($t->term_id, '_ft_ko_slug', $ko_term->slug);
            $stats['ko_slug_set']++;
            WP_CLI::log("  Set [{$lang}] {$taxonomy}: \"{$t->name}\" (ID:{$t->term_id}) → _ft_ko_slug=\"{$ko_term->slug}\"");
        }
    }
}

// ═══════════════════════════════════════════════════════
// 4. Polylang 링크 누락 복원 (포스트 번역 관계에서 추론)
// ═══════════════════════════════════════════════════════
WP_CLI::log('');
WP_CLI::log('═══ Step 4: Restore missing Polylang term links via post translations ═══');

// 한국어 포스트 조회
$ko_posts = get_posts([
    'post_type'      => ['travel_itinerary', 'destination_guide', 'vlog_curation'],
    'posts_per_page' => -1,
    'lang'           => 'ko',
    'post_status'    => 'publish',
]);

foreach ($ko_posts as $ko_post) {
    $post_translations = PLL()->model->post->get_translations($ko_post->ID);

    // 한국어 포스트의 terms
    foreach ($taxonomies as $taxonomy) {
        $ko_terms = wp_get_post_terms($ko_post->ID, $taxonomy);
        if (is_wp_error($ko_terms) || empty($ko_terms)) continue;

        foreach ($languages as $lang) {
            if (empty($post_translations[$lang])) continue;
            $trans_post_id = $post_translations[$lang];

            // 번역 포스트의 terms
            $trans_terms = wp_get_post_terms($trans_post_id, $taxonomy);
            if (is_wp_error($trans_terms) || empty($trans_terms)) continue;

            // 한국어 term → 번역 term 매칭 (Polylang 링크 없으면 이름 유사도 또는 순서로)
            foreach ($ko_terms as $ko_term) {
                $existing_link = pll_get_term($ko_term->term_id, $lang);
                if ($existing_link) continue; // 이미 링크됨

                // 번역 포스트에서 같은 taxonomy의 아직 링크 안 된 term 찾기
                foreach ($trans_terms as $tt) {
                    if (pll_get_term_language($tt->term_id) !== $lang) continue;
                    $tt_ko_link = pll_get_term($tt->term_id, 'ko');
                    if ($tt_ko_link) continue; // 이미 다른 한국어 term에 링크됨

                    // 같은 계층 수준인지 확인 (둘 다 부모 / 둘 다 자식)
                    $same_level = ($ko_term->parent > 0) === ($tt->parent > 0);
                    if (!$same_level && count($ko_terms) > 1) continue;

                    // 링크 설정
                    $group = PLL()->model->term->get_translations($ko_term->term_id);
                    $group['ko'] = $ko_term->term_id;
                    $group[$lang] = $tt->term_id;
                    PLL()->model->term->save_translations($ko_term->term_id, $group);

                    // _ft_ko_slug도 설정
                    if (!get_term_meta($tt->term_id, '_ft_ko_slug', true)) {
                        update_term_meta($tt->term_id, '_ft_ko_slug', $ko_term->slug);
                    }

                    $stats['polylang_linked']++;
                    WP_CLI::log("  Linked [{$lang}] {$taxonomy}: \"{$ko_term->name}\" (ko:{$ko_term->term_id}) ↔ \"{$tt->name}\" ({$lang}:{$tt->term_id})");
                    break;
                }
            }
        }
    }
}

// ═══════════════════════════════════════════════════════
// 5. 쓰레기 번역 term 삭제 (한국어 링크 없고 포스트 없는 term)
// ═══════════════════════════════════════════════════════
WP_CLI::log('');
WP_CLI::log('═══ Step 5: Remove orphan garbage terms ═══');

$orphans_deleted = 0;
foreach ($taxonomies as $taxonomy) {
    foreach ($languages as $lang) {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'lang'       => $lang,
        ]);

        if (is_wp_error($terms) || empty($terms)) continue;

        $terms = array_filter($terms, function ($t) use ($lang) {
            return pll_get_term_language($t->term_id) === $lang;
        });

        foreach ($terms as $t) {
            // 포스트가 연결된 term은 건드리지 않음
            if ($t->count > 0) continue;

            // Polylang 한국어 링크가 있으면 유지
            $ko_id = pll_get_term($t->term_id, 'ko');
            if ($ko_id && $ko_id !== $t->term_id) continue;

            // 포스트도 없고 한국어 링크도 없는 고아 term → 삭제
            wp_delete_term($t->term_id, $taxonomy);
            $orphans_deleted++;
            WP_CLI::log("  Deleted orphan [{$lang}] {$taxonomy}: \"{$t->name}\" (ID:{$t->term_id})");
        }
    }
}

// ═══════════════════════════════════════════════════════
// Summary
// ═══════════════════════════════════════════════════════
// ═══════════════════════════════════════════════════════
// 6. 캐시 정리
// ═══════════════════════════════════════════════════════
WP_CLI::log('');
WP_CLI::log('═══ Step 6: Flush caches ═══');
wp_cache_flush();
if (function_exists('PLL')) {
    PLL()->model->clean_languages_cache();
}
clean_term_cache([], '');
WP_CLI::log('  WordPress object cache flushed');
WP_CLI::log('  Polylang language cache cleared');
WP_CLI::log('  Term cache cleared');

WP_CLI::log('');
WP_CLI::success('Fix completed!');
WP_CLI::log("  Duplicates merged:     {$stats['duplicates_merged']}");
WP_CLI::log("  Parents fixed:         {$stats['parents_fixed']}");
WP_CLI::log("  _ft_ko_slug set:       {$stats['ko_slug_set']}");
WP_CLI::log("  Polylang links added:  {$stats['polylang_linked']}");
WP_CLI::log("  Orphan terms deleted:  {$orphans_deleted}");
