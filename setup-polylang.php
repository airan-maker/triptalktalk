<?php
/**
 * Setup Polylang languages
 * Run: wp eval-file setup-polylang.php --allow-root
 */

$languages = array(
    array('name' => '한국어',       'locale' => 'ko_KR', 'slug' => 'ko',    'flag' => 'kr'),
    array('name' => 'English',      'locale' => 'en_US', 'slug' => 'en',    'flag' => 'us'),
    array('name' => '中文 (中国)',   'locale' => 'zh_CN', 'slug' => 'zh-cn', 'flag' => 'cn'),
    array('name' => '日本語',       'locale' => 'ja',    'slug' => 'ja',    'flag' => 'jp'),
    array('name' => 'Français',     'locale' => 'fr_FR', 'slug' => 'fr',    'flag' => 'fr'),
    array('name' => 'Deutsch',      'locale' => 'de_DE', 'slug' => 'de',    'flag' => 'de'),
);

$model = PLL()->model;
$order = 0;

echo "=== Polylang 언어 설정 시작 ===\n\n";

foreach ($languages as $lang) {
    $order++;
    $existing = $model->get_language($lang['slug']);
    if ($existing) {
        echo "  [=] {$lang['name']} ({$lang['locale']}) - 이미 존재\n";
        continue;
    }
    $result = $model->add_language(array(
        'name'       => $lang['name'],
        'slug'       => $lang['slug'],
        'locale'     => $lang['locale'],
        'rtl'        => 0,
        'flag'       => $lang['flag'],
        'term_group' => $order,
    ));
    if (is_wp_error($result)) {
        echo "  [!] {$lang['name']} - 실패: " . $result->get_error_message() . "\n";
    } else {
        echo "  [+] {$lang['name']} ({$lang['locale']}) - 추가 완료\n";
    }
}

// Set Korean as default
$options = get_option('polylang');
if ($options) {
    $options['default_lang'] = 'ko';
    update_option('polylang', $options);
    echo "\n기본 언어: 한국어(ko) 설정 완료\n";
}

// Assign existing content to Korean
$ko = $model->get_language('ko');
if ($ko) {
    $posts = get_posts(array(
        'post_type'   => array('post', 'page', 'travel_itinerary'),
        'numberposts' => -1,
        'post_status' => 'any',
    ));
    $count = 0;
    foreach ($posts as $post) {
        if (!pll_get_post_language($post->ID)) {
            pll_set_post_language($post->ID, 'ko');
            $count++;
        }
    }
    echo "기존 콘텐츠 {$count}개 → 한국어 할당\n";

    $taxes = get_terms(array(
        'taxonomy'   => array('category', 'post_tag', 'destination', 'travel_style'),
        'hide_empty' => false,
    ));
    $tax_count = 0;
    if (!is_wp_error($taxes)) {
        foreach ($taxes as $term) {
            if (!pll_get_term_language($term->term_id)) {
                pll_set_term_language($term->term_id, 'ko');
                $tax_count++;
            }
        }
    }
    echo "기존 분류 {$tax_count}개 → 한국어 할당\n";
}

echo "\n=== 완료 ===\n";
$all_langs = $model->get_languages_list();
foreach ($all_langs as $l) {
    $d = ($l->slug === 'ko') ? ' ★' : '';
    echo "  {$l->name} [{$l->slug}]{$d}\n";
}
