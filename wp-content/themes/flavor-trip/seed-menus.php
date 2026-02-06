<?php
/**
 * TripTalk 메뉴 시드 스크립트
 *
 * 실행 방법:
 * wp eval-file /var/www/html/wp-content/themes/flavor-trip/seed-menus.php --allow-root
 *
 * @package TripTalk
 */

if (!defined('WP_CLI') || !WP_CLI) {
    echo "이 스크립트는 WP-CLI에서만 실행할 수 있습니다.\n";
    exit(1);
}

WP_CLI::log('=== TripTalk 메뉴 생성 시작 ===');

// ─────────────────────────────────────────────
// 1. 기존 메뉴 삭제
// ─────────────────────────────────────────────
$menu_names = ['Primary Menu', 'Footer Menu'];
foreach ($menu_names as $menu_name) {
    $menu = wp_get_nav_menu_object($menu_name);
    if ($menu) {
        wp_delete_nav_menu($menu->term_id);
        WP_CLI::log("  [-] 기존 메뉴 삭제: {$menu_name}");
    }
}

// ─────────────────────────────────────────────
// 2. Primary Menu 생성
// ─────────────────────────────────────────────
WP_CLI::log('');
WP_CLI::log('── Primary Menu 생성 ──');

$primary_menu_id = wp_create_nav_menu('Primary Menu');

// 홈
wp_update_nav_menu_item($primary_menu_id, 0, [
    'menu-item-title'   => '홈',
    'menu-item-url'     => home_url('/'),
    'menu-item-status'  => 'publish',
    'menu-item-type'    => 'custom',
]);

// 여행 일정
wp_update_nav_menu_item($primary_menu_id, 0, [
    'menu-item-title'   => '여행 일정',
    'menu-item-url'     => get_post_type_archive_link('travel_itinerary'),
    'menu-item-status'  => 'publish',
    'menu-item-type'    => 'custom',
]);

// 여행지 (부모 메뉴)
$dest_parent_id = wp_update_nav_menu_item($primary_menu_id, 0, [
    'menu-item-title'   => '여행지',
    'menu-item-url'     => '#',
    'menu-item-status'  => 'publish',
    'menu-item-type'    => 'custom',
]);

// 여행지 하위 메뉴 - 지역별
$destinations = [
    'asia'    => '아시아',
    'europe'  => '유럽',
    'oceania' => '오세아니아',
    'korea'   => '국내',
];

foreach ($destinations as $slug => $name) {
    $term = get_term_by('slug', $slug, 'destination');
    if ($term) {
        wp_update_nav_menu_item($primary_menu_id, 0, [
            'menu-item-title'     => $name,
            'menu-item-object'    => 'destination',
            'menu-item-object-id' => $term->term_id,
            'menu-item-type'      => 'taxonomy',
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => $dest_parent_id,
        ]);
    }
}

// 여행 스타일 (부모 메뉴)
$style_parent_id = wp_update_nav_menu_item($primary_menu_id, 0, [
    'menu-item-title'   => '여행 스타일',
    'menu-item-url'     => '#',
    'menu-item-status'  => 'publish',
    'menu-item-type'    => 'custom',
]);

// 여행 스타일 하위 메뉴
$styles = ['가족여행', '커플여행', '혼자여행', '미식여행', '문화체험', '힐링여행'];

foreach ($styles as $style_name) {
    $term = get_term_by('name', $style_name, 'travel_style');
    if ($term) {
        wp_update_nav_menu_item($primary_menu_id, 0, [
            'menu-item-title'     => $style_name,
            'menu-item-object'    => 'travel_style',
            'menu-item-object-id' => $term->term_id,
            'menu-item-type'      => 'taxonomy',
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => $style_parent_id,
        ]);
    }
}

// Primary Menu를 theme location에 할당
$locations = get_theme_mod('nav_menu_locations', []);
$locations['primary'] = $primary_menu_id;
set_theme_mod('nav_menu_locations', $locations);

WP_CLI::success("Primary Menu 생성 완료 (ID: {$primary_menu_id})");

// ─────────────────────────────────────────────
// 3. Footer Menu 생성
// ─────────────────────────────────────────────
WP_CLI::log('');
WP_CLI::log('── Footer Menu 생성 ──');

$footer_menu_id = wp_create_nav_menu('Footer Menu');

// 홈
wp_update_nav_menu_item($footer_menu_id, 0, [
    'menu-item-title'   => '홈',
    'menu-item-url'     => home_url('/'),
    'menu-item-status'  => 'publish',
    'menu-item-type'    => 'custom',
]);

// 여행 일정
wp_update_nav_menu_item($footer_menu_id, 0, [
    'menu-item-title'   => '여행 일정',
    'menu-item-url'     => get_post_type_archive_link('travel_itinerary'),
    'menu-item-status'  => 'publish',
    'menu-item-type'    => 'custom',
]);

// 인기 여행지
$popular_destinations = [
    'japan'     => '일본',
    'taiwan'    => '대만',
    'jeju'      => '제주도',
    'osaka'     => '오사카',
    'tokyo'     => '도쿄',
];

foreach ($popular_destinations as $slug => $name) {
    $term = get_term_by('slug', $slug, 'destination');
    if ($term) {
        wp_update_nav_menu_item($footer_menu_id, 0, [
            'menu-item-title'     => $name,
            'menu-item-object'    => 'destination',
            'menu-item-object-id' => $term->term_id,
            'menu-item-type'      => 'taxonomy',
            'menu-item-status'    => 'publish',
        ]);
    }
}

// Footer Menu를 theme location에 할당
$locations = get_theme_mod('nav_menu_locations', []);
$locations['footer'] = $footer_menu_id;
set_theme_mod('nav_menu_locations', $locations);

WP_CLI::success("Footer Menu 생성 완료 (ID: {$footer_menu_id})");

// ─────────────────────────────────────────────
// 4. 여행 스타일 텀 생성 (없으면)
// ─────────────────────────────────────────────
WP_CLI::log('');
WP_CLI::log('── 여행 스타일 텀 확인 ──');

$travel_styles = [
    '가족여행'   => '아이와 함께하는 가족 친화적 여행',
    '커플여행'   => '연인과 함께하는 로맨틱 여행',
    '혼자여행'   => '나 홀로 떠나는 자유로운 여행',
    '미식여행'   => '현지 맛집과 음식 중심 여행',
    '문화체험'   => '역사와 문화를 체험하는 여행',
    '힐링여행'   => '휴식과 재충전을 위한 여행',
    '액티비티'   => '스포츠와 모험 중심 여행',
    '쇼핑여행'   => '쇼핑 중심의 여행',
];

foreach ($travel_styles as $name => $description) {
    if (!term_exists($name, 'travel_style')) {
        wp_insert_term($name, 'travel_style', [
            'description' => $description,
            'slug'        => sanitize_title($name),
        ]);
        WP_CLI::log("  [+] 여행 스타일 추가: {$name}");
    }
}

WP_CLI::log('');
WP_CLI::success('=== 메뉴 생성 완료 ===');
