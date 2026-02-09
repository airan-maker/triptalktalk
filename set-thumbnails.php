<?php
/**
 * 여행 일정 썸네일 이미지 설정
 *
 * 실행: wp eval-file /var/www/html/set-thumbnails.php --allow-root
 */

if (!defined('WP_CLI') || !WP_CLI) {
    echo "WP-CLI에서만 실행 가능합니다.\n";
    exit(1);
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$thumbnails = [
    '아이와 함께하는 나트랑 3박4일' => 'https://images.unsplash.com/photo-1503188991764-408493f288b9?w=1200&q=80',
    '눈 내리는 삿포로 가족 여행 3박4일' => 'https://images.unsplash.com/photo-1519105467443-4779d0fb729d?w=1200&q=80',
    '아이에게 평화를 알려주는 히로시마' => 'https://images.unsplash.com/photo-1697605623014-c68d4b666420?w=1200&q=80',
    '삿포로 로맨틱 2박3일' => 'https://images.unsplash.com/photo-1709459991430-459d35fbe03e?w=1200&q=80',
    '가나자와 감성 커플 여행' => 'https://images.unsplash.com/photo-1684695414445-685455eb85c5?w=1200&q=80',
    '도쿄 3박4일 완벽 가이드' => 'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?w=1200&q=80',
];

foreach ($thumbnails as $title_part => $image_url) {
    // 제목 부분 일치로 포스트 검색
    $posts = get_posts([
        'post_type'      => 'travel_itinerary',
        'posts_per_page' => 1,
        's'              => $title_part,
        'post_status'    => 'publish',
    ]);

    if (empty($posts)) {
        WP_CLI::warning("포스트를 찾을 수 없음: {$title_part}");
        continue;
    }

    $post = $posts[0];

    // 이미 썸네일이 있으면 삭제
    $old_thumb = get_post_thumbnail_id($post->ID);
    if ($old_thumb) {
        wp_delete_attachment($old_thumb, true);
    }

    // 이미지 다운로드 및 미디어 라이브러리 등록
    $attachment_id = media_sideload_image($image_url, $post->ID, $title_part, 'id');

    if (is_wp_error($attachment_id)) {
        WP_CLI::warning("이미지 다운로드 실패: {$title_part} - " . $attachment_id->get_error_message());
        continue;
    }

    // 대표 이미지 설정
    set_post_thumbnail($post->ID, $attachment_id);

    // 번역본에도 동일 썸네일 적용
    if (function_exists('PLL')) {
        $translations = PLL()->model->post->get_translations($post->ID);
        foreach ($translations as $lang => $trans_id) {
            if ($trans_id != $post->ID) {
                set_post_thumbnail($trans_id, $attachment_id);
            }
        }
    }

    WP_CLI::success("썸네일 설정 완료: {$post->post_title} (ID: {$post->ID}, Attachment: {$attachment_id})");
}

WP_CLI::success('모든 썸네일 설정 완료!');
