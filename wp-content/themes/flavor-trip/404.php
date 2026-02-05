<?php
/**
 * 404 ?˜ì´ì§€
 *
 * @package TripTalk
 */

get_header();
?>

<div class="container error-404-page">
    <div class="error-content">
        <h1 class="error-code">404</h1>
        <h2 class="error-title"><?php esc_html_e('?˜ì´ì§€ë¥?ì°¾ì„ ???†ìŠµ?ˆë‹¤', 'flavor-trip'); ?></h2>
        <p class="error-message"><?php esc_html_e('?”ì²­?˜ì‹  ?˜ì´ì§€ê°€ ì¡´ìž¬?˜ì? ?Šê±°???´ë™?˜ì—ˆ?????ˆìŠµ?ˆë‹¤.', 'flavor-trip'); ?></p>

        <div class="error-search">
            <?php get_search_form(); ?>
        </div>

        <div class="error-links">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary"><?php esc_html_e('?ˆìœ¼ë¡??Œì•„ê°€ê¸?, 'flavor-trip'); ?></a>
            <a href="<?php echo esc_url(get_post_type_archive_link('travel_itinerary')); ?>" class="btn btn-outline"><?php esc_html_e('?¬í–‰ ?¼ì • ë³´ê¸°', 'flavor-trip'); ?></a>
        </div>
    </div>
</div>

<?php
get_footer();
