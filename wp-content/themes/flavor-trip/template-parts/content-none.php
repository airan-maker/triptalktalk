<?php
/**
 * 결과 없음 템플릿
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;
?>

<div class="no-results">
    <h2><?php esc_html_e('결과가 없습니다', 'flavor-trip'); ?></h2>

    <?php if (is_search()) : ?>
        <p><?php esc_html_e('검색어와 일치하는 결과가 없습니다. 다른 키워드로 다시 검색해보세요.', 'flavor-trip'); ?></p>
        <?php get_search_form(); ?>
    <?php else : ?>
        <p><?php esc_html_e('아직 게시된 콘텐츠가 없습니다.', 'flavor-trip'); ?></p>
    <?php endif; ?>
</div>
