<?php
/**
 * 404 페이지
 *
 * @package Flavor_Trip
 */

get_header();
?>

<div class="container error-404-page">
    <div class="error-content">
        <h1 class="error-code">404</h1>
        <h2 class="error-title"><?php esc_html_e('페이지를 찾을 수 없습니다', 'flavor-trip'); ?></h2>
        <p class="error-message"><?php esc_html_e('요청하신 페이지가 존재하지 않거나 이동되었을 수 있습니다.', 'flavor-trip'); ?></p>

        <div class="error-search">
            <?php get_search_form(); ?>
        </div>

        <div class="error-links">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary"><?php esc_html_e('홈으로 돌아가기', 'flavor-trip'); ?></a>
            <a href="<?php echo esc_url(get_post_type_archive_link('travel_itinerary')); ?>" class="btn btn-outline"><?php esc_html_e('여행 일정 보기', 'flavor-trip'); ?></a>
        </div>
    </div>
</div>

<?php
get_footer();
